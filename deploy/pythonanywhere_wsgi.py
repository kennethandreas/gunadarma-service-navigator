import sys

# Replace <username> with your actual PythonAnywhere username (both here
# and in the path below). This file's contents should be pasted into the
# "WSGI configuration file" that PythonAnywhere generates for you on the
# Web tab, replacing whatever is already in there.
path = '/home/<username>/gunadarma-navigator/ml'
if path not in sys.path:
    sys.path.append(path)

from api import app as application
