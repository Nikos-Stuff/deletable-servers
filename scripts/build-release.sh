#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ID="$(python3 -c 'import json,sys; print(json.load(open(sys.argv[1], encoding="utf-8"))["id"])' "${ROOT}/plugin.json")"
VERSION="$(python3 -c 'import json,sys; print(json.load(open(sys.argv[1], encoding="utf-8"))["version"])' "${ROOT}/plugin.json")"

STAGE="$(mktemp -d)"
DEST="${STAGE}/${ID}"
mkdir -p "${DEST}"

cp -r "${ROOT}/src" "${ROOT}/lang" "${ROOT}/resources" "${ROOT}/plugin.json" "${ROOT}/README.md" "${DEST}/"

python3 - "${DEST}/plugin.json" <<'PY'
import json, sys
path = sys.argv[1]
with open(path, "r", encoding="utf-8") as f:
    data = json.load(f)
data.pop("meta", None)
with open(path, "w", encoding="utf-8") as f:
    json.dump(data, f, indent=4, ensure_ascii=False)
    f.write("\n")
PY

mkdir -p "${ROOT}/dist"
OUT="${ROOT}/dist/${ID}.zip"
rm -f "${OUT}"

python3 - "${STAGE}" "${ID}" "${OUT}" <<'PY'
import os, sys, zipfile
stage, pid, out = sys.argv[1:4]
with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as z:
    for root, _, files in os.walk(os.path.join(stage, pid)):
        for f in sorted(files):
            full = os.path.join(root, f)
            z.write(full, os.path.relpath(full, stage))
PY

rm -rf "${STAGE}"

echo "✅ ${OUT} (v${VERSION})"
python3 -c 'import sys,zipfile; [print(" ", n) for n in zipfile.ZipFile(sys.argv[1]).namelist()]' "${OUT}"