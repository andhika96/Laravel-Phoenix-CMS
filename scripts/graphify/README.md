# Graphify Git Sync

Repository ini memakai Git hooks agar knowledge graph Graphify selalu diperbarui tanpa perintah manual.

## Setup pertama di Windows

Jalankan dari root repository:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\graphify\setup-windows.ps1
```

Script tersebut memasang Graphify `0.9.23` bila diperlukan, mengaktifkan `.githooks`, menulis metadata path lokal, dan membuat graph pertama hanya jika `graphify-out\graph.json` belum tersedia. File hook di `.githooks` dikelola oleh Git; setup tidak menjalankan `graphify hook install` agar hook tidak ditimpa path Python milik komputer lain.

## Event otomatis

- `post-commit`: update code yang berubah melalui hook bawaan Graphify.
- `post-checkout`: refresh setelah berpindah branch.
- `post-merge`: refresh setelah `git pull` atau merge.
- `post-rewrite`: refresh setelah rebase.
- `pre-push`: pemeriksaan incremental terakhir sebelum push.

Graph tetap lokal dan dikecualikan dari Git melalui `/graphify-out/`. Kegagalan Graphify dicatat di `%USERPROFILE%\.cache\graphify-git-sync.log` dan tidak membatalkan operasi Git.

Jika Graphify mendeteksi graph lama masih berisi file yang kini dikecualikan oleh `.graphifyignore`, updater mengenali kedua diagnostic pengaman Graphify (`Refusing to overwrite` dan `left the scan corpus`) lalu menjalankan kembali scan code-only dengan `--force`. Pemulihan ini hanya berlaku untuk kombinasi diagnostic tersebut; error lain tetap dicatat tanpa mengganggu Git. Dengan begitu, laptop dan PC kantor membangun graph lokal masing-masing dari scope source yang sama tanpa mengirim `graphify-out` ke repository.

Untuk melewati hook sementara:

```powershell
$env:GRAPHIFY_SKIP_HOOK = '1'
```
