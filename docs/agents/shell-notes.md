# Disiplin shell (pelajaran OOM 11 Okt 2026)

Dua panggilan jaringan via `Invoke-WebRequest` / curl multi-URL membuat proses
PowerShell tool kehabisan memori. Aturan berikut menghindarinya:

1. Satu URL per panggilan. Jangan loop multi-URL dalam satu command.
2. Untuk cek status saja: `curl.exe -s -o NUL -w '%{http_code}' --max-time 15 <url>`
   (pakai `curl.exe` natif, bukan alias; selalu `--max-time`).
3. Jangan `Invoke-WebRequest` untuk halaman HTML (buffer seluruh DOM).
4. Jangan `Select-String` atas stream tak terbatas; batasi (`Select-Object -First N`)
   atau simpan ke file dulu lalu `Grep`.
5. Panggilan jaringan sekuensial, bukan paralel dalam satu blok.
6. Shell adalah PowerShell: tanpa `head`/`tail` (pakai `Select-Object -First N`),
   tanpa heredoc `cat <<EOF` (untuk body multi-baris, tulis file via Write
   tool lalu pakai flag `--body-file` / `--body-file` setara).
