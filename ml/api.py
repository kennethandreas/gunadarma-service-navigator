"""
Minimal REST API exposing the trained intent classifier to Laravel.

    python api.py

Runs on http://127.0.0.1:5000 by default (matches AI_SERVICE_URL in
.env.example). Laravel calls this over HTTP instead of shelling out to
Python directly — simple, keeps the two apps independently runnable, and
(as of Phase 18) avoids a Windows-specific issue where spawning a *new*
Python process from PHP can crash on `import asyncio`. Retraining reuses
this already-running process instead of starting a new one.

Endpoints:
    GET  /health   -> { "status": "ok" }
    POST /predict  -> body { "question": "..." }
                       returns { "question", "intent", "confidence" }
    POST /retrain  -> retrains + re-evaluates the model in-process,
                       returns the resulting metadata + evaluation summary
"""

from flask import Flask, jsonify, request

import predict as predict_module
from evaluate import run_evaluation
from predict import predict
from train import run_training

app = Flask(__name__)


@app.get("/health")
def health():
    return jsonify({"status": "ok"})


@app.post("/predict")
def predict_intent():
    data = request.get_json(silent=True) or {}
    question = str(data.get("question") or "").strip()

    if not question:
        return jsonify({"error": "Field 'question' wajib diisi."}), 400

    try:
        intent, confidence = predict(question)
    except FileNotFoundError as e:
        return jsonify({"error": str(e)}), 503

    return jsonify({
        "question": question,
        "intent": intent,
        "confidence": round(confidence, 4),
    })


@app.post("/retrain")
def retrain():
    try:
        metadata = run_training()
        evaluation = run_evaluation()
    except Exception as e:  # noqa: BLE001 - surface any training failure to the admin UI
        return jsonify({"error": str(e)}), 500

    # The next /predict call should use the freshly trained model, not one
    # cached in memory from before this request.
    predict_module.reset_cache()

    return jsonify({
        "status": "ok",
        "metadata": metadata,
        "accuracy": evaluation["accuracy"],
    })


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=False)
