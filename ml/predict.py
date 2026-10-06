"""
Predict the intent of a question using the saved TF-IDF + Naive Bayes model.

CLI usage (mainly for manual testing):
    python predict.py "Saya mau cek jadwal kuliah"

Programmatic usage (used by the Phase 15 prediction API):
    from predict import predict
    intent, confidence = predict("Saya mau cek jadwal kuliah")
"""

import json
import sys
from pathlib import Path

import joblib

from preprocessing import clean_text

BASE_DIR = Path(__file__).resolve().parent
MODEL_DIR = BASE_DIR / "model"

_vectorizer = None
_model = None


def _load():
    global _vectorizer, _model
    if _vectorizer is None or _model is None:
        vectorizer_path = MODEL_DIR / "vectorizer.joblib"
        model_path = MODEL_DIR / "model.joblib"

        if not vectorizer_path.exists() or not model_path.exists():
            raise FileNotFoundError(
                "Model belum dilatih. Jalankan `python train.py` terlebih dahulu."
            )

        _vectorizer = joblib.load(vectorizer_path)
        _model = joblib.load(model_path)

    return _vectorizer, _model


def reset_cache() -> None:
    """Force the next predict() call to reload the model from disk.

    Called by api.py right after /retrain saves a freshly trained model,
    so the running Flask process picks it up without needing a restart.
    """
    global _vectorizer, _model
    _vectorizer = None
    _model = None


def predict(text: str) -> tuple[str, float]:
    """Return (intent, confidence) for a free-text question."""
    vectorizer, model = _load()

    cleaned = clean_text(text)
    vector = vectorizer.transform([cleaned])

    intent = model.predict(vector)[0]
    probabilities = model.predict_proba(vector)[0]
    confidence = float(max(probabilities))

    return intent, confidence


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print('Usage: python predict.py "pertanyaan kamu"')
        sys.exit(1)

    question = " ".join(sys.argv[1:])
    predicted_intent, predicted_confidence = predict(question)

    print(json.dumps({
        "question": question,
        "intent": predicted_intent,
        "confidence": round(predicted_confidence, 4),
    }, ensure_ascii=False))
