# Slash commands untuk skill mattpocock/skills

`npx skills add` hanya menginstal isi skill ke `.agents/skills/` (gitignored).
Slash command di opencode adalah file prompt terpisah di `.opencode/commands/*.md`
(juga gitignored, local-only) dan **tidak** dibuatkan otomatis. Setiap command
di bawah ini memuat `.agents/skills/<nama>/SKILL.md` dari disk lalu menjalankan
prosesnya, jadi tetap jalan walau daftar `skill` tool sesi itu basi (cache saat
sesi dimulai; perlu restart sesi untuk refresh).

## Kapan regen

Hanya setelah `npx skills update` menambah/mengubah skill (deskripsi command
basi). Operasi langka; tidak ada watcher.

## Cara regen

Jalankan skrip di bawah dari root repo dengan `python3`. Ia membaca daftar
skill `mattpocock/skills` dari `skills-lock.json`, mengambil `description` dari
frontmatter tiap `SKILL.md`, dan menulis ulang `.opencode/commands/<nama>.md`.
Idempoten; file `opsx-*` yang sudah ada tidak disentuh.

```python
import json
import pathlib
import re

ROOT = pathlib.Path(r"D:\development\siriusglobalid")
lock = json.loads((ROOT / "skills-lock.json").read_text(encoding="utf-8"))
names = sorted(
    k for k, v in lock["skills"].items() if v.get("source") == "mattpocock/skills"
)
outdir = ROOT / ".opencode" / "commands"
outdir.mkdir(parents=True, exist_ok=True)

for name in names:
    skill = ROOT / ".agents" / "skills" / name / "SKILL.md"
    desc = name
    if skill.exists():
        head = skill.read_text(encoding="utf-8")[:1200]
        m = re.search(r'description:\s*"([^"]+)"', head, re.S)
        if not m:
            m2 = re.search(r"^description:\s*(.+)$", head, re.M)
            if m2:
                desc = re.sub(r"\s+", " ", m2.group(1)).strip()[:220]
        if m:
            desc = re.sub(r"\s+", " ", m.group(1)).strip()[:220]
    body = (
        "---\n"
        f"description: {desc}\n"
        "---\n"
        "\n"
        f"Load and follow the `{name}` skill end to end. Read "
        f"`.agents/skills/{name}/SKILL.md` from disk first (plus any files it "
        "references, resolved relative to that skill folder), then execute "
        "its process exactly as written.\n"
        "\n"
        "Provided arguments: $ARGUMENTS\n"
    )
    (outdir / f"{name}.md").write_text(body, encoding="utf-8")

print("wrote", len(names))
```

## Catatan skills CLI di environment ini

- Interactive prompt butuh TTY; selalu pakai flag non-interaktif:
  `npx skills@latest add mattpocock/skills -a opencode -s '*' -y`.
- Daftar skill: `npx skills@latest add mattpocock/skills -a opencode -l`.
