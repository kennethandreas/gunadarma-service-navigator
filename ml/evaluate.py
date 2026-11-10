"""
Evaluate the trained intent classifier: accuracy, precision, recall,
F1-score (per intent + macro/weighted average) and a confusion matrix.

    python evaluate.py

Requires a model already trained via train.py. Evaluates on the same
held-out test split train.py used (same random_state), so this never
scores the model on data it already saw during training. Saves the full
report to ml/model/evaluation.json for the admin "Model AI" page (Phase 18).
"""

import json
from pathlib import Path

import joblib
import pandas as pd
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score
from sklearn.model_selection import train_test_split

from preprocessing import clean_text

BASE_DIR = Path(__file__).resolve().parent
DATASET_PATH = BASE_DIR / "dataset" / "intent_dataset.csv"
MODEL_DIR = BASE_DIR / "model"


def run_evaluation() -> dict:
    """Evaluate the saved model and return the report dict that was saved."""
    vectorizer_path = MODEL_DIR / "vectorizer.joblib"
    model_path = MODEL_DIR / "model.joblib"

    if not vectorizer_path.exists() or not model_path.exists():
        raise FileNotFoundError("Model belum dilatih. Jalankan `python train.py` terlebih dahulu.")

    vectorizer = joblib.load(vectorizer_path)
    model = joblib.load(model_path)

    df = pd.read_csv(DATASET_PATH)
    df["clean_text"] = df["text"].apply(clean_text)

    # Same split train.py used, so this evaluates on the held-out test set.
    _, x_test, _, y_test = train_test_split(
        df["clean_text"],
        df["intent"],
        test_size=0.2,
        random_state=42,
        stratify=df["intent"],
    )

    x_test_vec = vectorizer.transform(x_test)
    y_pred = model.predict(x_test_vec)

    labels = sorted(df["intent"].unique().tolist())

    accuracy = accuracy_score(y_test, y_pred)
    report = classification_report(y_test, y_pred, labels=labels, output_dict=True, zero_division=0)
    matrix = confusion_matrix(y_test, y_pred, labels=labels)

    evaluation = {
        "accuracy": round(float(accuracy), 4),
        "test_size": int(len(x_test)),
        "labels": labels,
        "confusion_matrix": matrix.tolist(),
        "per_intent": {
            label: {
                "precision": round(float(report[label]["precision"]), 4),
                "recall": round(float(report[label]["recall"]), 4),
                "f1_score": round(float(report[label]["f1-score"]), 4),
                "support": int(report[label]["support"]),
            }
            for label in labels
        },
        "macro_avg": {
            "precision": round(float(report["macro avg"]["precision"]), 4),
            "recall": round(float(report["macro avg"]["recall"]), 4),
            "f1_score": round(float(report["macro avg"]["f1-score"]), 4),
        },
        "weighted_avg": {
            "precision": round(float(report["weighted avg"]["precision"]), 4),
            "recall": round(float(report["weighted avg"]["recall"]), 4),
            "f1_score": round(float(report["weighted avg"]["f1-score"]), 4),
        },
    }

    with open(MODEL_DIR / "evaluation.json", "w", encoding="utf-8") as f:
        json.dump(evaluation, f, indent=2, ensure_ascii=False)

    return evaluation


def main() -> None:
    evaluation = run_evaluation()
    labels = evaluation["labels"]
    matrix = evaluation["confusion_matrix"]

    print(f"Accuracy: {evaluation['accuracy']:.4f}\n")
    for label in labels:
        row = evaluation["per_intent"][label]
        print(f"  {label:25s} precision={row['precision']:.2f} recall={row['recall']:.2f} f1={row['f1_score']:.2f}")

    print("\nConfusion Matrix (baris = aktual, kolom = prediksi)")
    print(f"Urutan label: {labels}\n")
    for label, row in zip(labels, matrix):
        print(f"  {label:25s} {row}")

    print(f"\nHasil evaluasi tersimpan di: {MODEL_DIR / 'evaluation.json'}")


if __name__ == "__main__":
    main()
