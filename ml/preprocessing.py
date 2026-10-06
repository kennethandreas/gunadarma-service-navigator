"""
Text preprocessing shared by train.py and predict.py.

Pipeline: lowercase -> strip non letter/number/space -> collapse whitespace
-> remove Indonesian stopwords -> stem to root words (Sastrawi). This
matches the pipeline described in the thesis (User Question -> Text
Preprocessing -> TF-IDF -> Naive Bayes).

Stemming + stopword removal (added after the initial version, which only
did lowercase/strip) meaningfully reduces vocabulary sparsity for this
dataset's size: "pembayaran", "membayar" and "bayar" all collapse to the
same root "bayar", so TF-IDF treats a user's casual phrasing the same as
the training examples instead of scoring it as a different, unseen word.
Sastrawi is the standard stemmer for Bahasa Indonesia (Nazief & Adriani
algorithm) and is a normal citation/justification point for this thesis's
methodology chapter.
"""

import re

from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory

# Created once at import time — both the stemmer and stopword remover build
# a static dictionary/word-list internally, so reusing one instance across
# every row of the dataset (training) and every request (prediction) avoids
# rebuilding that dictionary on every single call.
_stemmer = StemmerFactory().create_stemmer()
_stopword_remover = StopWordRemoverFactory().create_stop_word_remover()


def clean_text(text: str) -> str:
    text = str(text).lower()
    text = re.sub(r"[^a-z0-9\s]", " ", text)
    text = re.sub(r"\s+", " ", text).strip()

    if not text:
        return text

    text = _stopword_remover.remove(text)
    text = _stemmer.stem(text)

    return text
