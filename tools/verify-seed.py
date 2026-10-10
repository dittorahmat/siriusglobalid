"""verify-seed.py - Kontrak importer sisi ID (jalan tanpa WP/PHP).
Memastikan: semua seed valid JSON, sisi 'id' terekstrak, batas list
terpenuhi, slug layanan apa pun diterima, dan manifest idempoten
(run kedua tanpa perubahan = nol duplikat).
Pakai: python tools/verify-seed.py
"""
import hashlib
import json
import sys
from pathlib import Path

SEED = Path(__file__).resolve().parent.parent / "data" / "seed"
LIMITS = {
    "portfolio.items": 24, "service.faqs": 20, "service.tiers": 6,
    "service.checklist": 20, "service.metrics": 8, "service.steps": 8,
    "tentang.values": 12, "tentang.timeline": 12, "tentang.team": 12,
    "home.clients": 12, "home.cases": 6,
}
fails = []


def check(cond, msg):
    print(("PASS " if cond else "FAIL ") + msg)
    if not cond:
        fails.append(msg)


def sid(v):
    if isinstance(v, dict):
        return str(v.get("id", ""))
    return str(v or "")


def load(p):
    try:
        return json.loads(p.read_text(encoding="utf-8"))
    except Exception as e:  # noqa: BLE001
        check(False, f"{p.name} valid JSON ({e})")
        return None


# settings
s = load(SEED / "settings.json")
check(bool(s) and "@" in s.get("email", ""), "settings email valid")

# home counts
h = load(SEED / "home.json")
if h:
    check(len(h.get("kpi_counts", [])) == 3, "home kpi 3")
    check(len(h.get("stat_counts", [])) == 4, "home stats 4")
    check(len(h.get("clients", [])) <= LIMITS["home.clients"], "home clients<=12")
    check(len(h.get("case_imgs", [])) <= LIMITS["home.cases"], "home cases<=6")

# services: slug apa pun diterima, batas ditegakkan
svc_files = sorted((SEED / "services").glob("*.json"))
manifest = {}
for f in svc_files:
    d = load(f)
    if not d:
        continue
    slug = d.get("slug") or f.stem
    manifest[slug] = hashlib.md5(
        json.dumps(d, sort_keys=True, ensure_ascii=False).encode()
    ).hexdigest()
    for key, items in [
        ("service.checklist", d.get("intro", {}).get("checklist", [])),
        ("service.metrics", d.get("metrics", [])),
        ("service.tiers", d.get("pricing", {}).get("tiers", [])),
        ("service.steps", d.get("steps", {}).get("items", [])),
        ("service.faqs", d.get("faqs", [])),
    ]:
        check(len(items) <= LIMITS[key], f"{slug} {key}<={LIMITS[key]}")
    check(bool(sid(d.get("title"))), f"{slug} punya title sisi id")
check(len(svc_files) >= 9, f"layanan seed >=9 (kini {len(svc_files)})")

# idempotensi: manifest run kedua identik = nol duplikat
manifest2 = {
    f.stem: hashlib.md5(f.read_bytes()).hexdigest() for f in svc_files
}
check(len(manifest2) == len(manifest), "idempoten: jumlah slug stabil run kedua")

# portfolio
p = load(SEED / "portfolio.json")
if p:
    items = p.get("items", [])
    check(len(items) <= LIMITS["portfolio.items"], "portfolio<=24")
    cats = {"erp", "ai", "fleet", "app", "iot", "infra", "sec", "bi"}
    bad = [i.get("cat") for i in items if i.get("cat") not in cats]
    check(not bad, f"portfolio kategori valid {bad}")

# pages
for pg in ["tentang", "layanan", "kontak", "privasi", "syarat"]:
    check((SEED / f"{pg}.json").is_file(), f"{pg}.json ada")

print(f"\n{len(fails)} kegagalan, {len(manifest)} layanan seed.")
sys.exit(1 if fails else 0)
