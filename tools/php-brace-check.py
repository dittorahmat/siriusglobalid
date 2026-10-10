"""Cek kasar keseimbangan kurung semua file PHP theme + importer."""
import glob
import re

files = glob.glob("wp-content/themes/sirius-custom/**/*.php", recursive=True)
files += ["tools/sgi-importer.php"]
bad = 0
for f in sorted(files):
    src = open(f, encoding="utf-8").read()
    src = re.sub(r"'(?:[^'\\]|\\.)*'", "''", src)
    src = re.sub(r'"(?:[^"\\]|\\.)*"', '""', src)
    src = re.sub(r"//.*", "", src)
    src = re.sub(r"/\*.*?\*/", "", src, flags=re.S)
    o, c = src.count("{"), src.count("}")
    op, cp = src.count("("), src.count(")")
    ok = o == c and op == cp
    bad += not ok
    print(("OK  " if ok else "CEK!") + f" {f} {{ {o}/{c} ( {op}/{cp}")
print("BAIK" if bad == 0 else f"{bad} FILE PERLU CEK MANUAL")
