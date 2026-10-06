"""
Train the intent classifier: TF-IDF + Multinomial Naive Bayes.

    python train.py

Reads ml/dataset/intent_dataset.csv, trains the model, and saves the fitted
vectorizer + classifier + metadata into ml/model/. The Laravel app never
trains at request time — it only ever triggers this via the /retrain
endpoint in api.py (see Phase 18's admin "Train / Retrain Model" button).
"""

import json
from datetime import datetime
from pathlib import Path

import joblib
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics import accuracy_score
from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import MultinomialNB

from preprocessing import clean_text

BASE_DIR = Path(__file__).resolve().parent
DATASET_PATH = BASE_DIR / "dataset" / "intent_dataset.csv"
MODEL_DIR = BASE_DIR / "model"


def run_training() -> dict:
    """Train the model and return the metadata dict that was saved."""
    MODEL_DIR.mkdir(exist_ok=True)

    df = pd.read_csv(DATASET_PATH)
    df["clean_text"] = df["text"].apply(clean_text)

    x_train, x_test, y_train, y_test = train_test_split(
        df["clean_text"],
        df["intent"],
        test_size=0.2,
        random_state=42,
        stratify=df["intent"],
    )

    vectorizer = TfidfVectorizer()
    x_train_vec = vectorizer.fit_transform(x_train)
    x_test_vec = vectorizer.transform(x_test)

    model = MultinomialNB()
    model.fit(x_train_vec, y_train)

    train_accuracy = accuracy_score(y_train, model.predict(x_train_vec))
    test_accuracy = accuracy_score(y_test, model.predict(x_test_vec))

    joblib.dump(vectorizer, MODEL_DIR / "vectorizer.joblib")
    joblib.dump(model, MODEL_DIR / "model.joblib")

    metadata = {
        "model": "MultinomialNB",
        "vectorizer": "TfidfVectorizer",
        "trained_at": datetime.now().isoformat(timespec="seconds"),
        "dataset_size": int(len(df)),
        "train_size": int(len(x_train)),
        "test_size": int(len(x_test)),
        "train_accuracy": round(float(train_accuracy), 4),
        "test_accuracy": round(float(test_accuracy), 4),
        "num_intents": int(df["intent"].nunique()),
        "intents": sorted(df["intent"].unique().tolist()),
    }
    with open(MODEL_DIR / "metadata.json", "w", encoding="utf-8") as f:
        json.dump(metadata, f, indent=2, ensure_ascii=False)

    return metadata


def main() -> None:
    metadata = run_training()

    print(f"Dataset: {metadata['dataset_size']} baris, {metadata['num_intents']} intent")
    print(f"Train accuracy: {metadata['train_accuracy']:.4f}")
    print(f"Test accuracy:  {metadata['test_accuracy']:.4f}")
    print(f"Model tersimpan di: {MODEL_DIR}")


if __name__ == "__main__":
    main()
