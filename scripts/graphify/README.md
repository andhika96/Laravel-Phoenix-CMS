# Graphify Git Sync

Repository ini memakai Git hooks agar knowledge graph Graphify selalu diperbarui tanpa perintah manual.

## Setup pertama di Windows

Jalankan dari root repository:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\graphify\setup-windows.ps1
```

Script tersebut memasang Graphify `0.9.23` bila diperlukan, mengaktifkan `.githooks`, menulis metadata path lokal, dan membuat graph pertama hanya jika `graphify-out\graph.json` belum tersedia.

## Event otomatis

- `post-commit`: update code yang berubah melalui hook bawaan Graphify.
- `post-checkout`: refresh setelah berpindah branch.
- `post-merge`: refresh setelah `git pull` atau merge.
- `post-rewrite`: refresh setelah rebase.
- `pre-push`: pemeriksaan incremental terakhir sebelum push.

Graph tetap lokal dan dikecualikan dari Git melalui `/graphify-out/`. Kegagalan Graphify dicatat di `%USERPROFILE%\.cache\graphify-git-sync.log` dan tidak membatalkan operasi Git.

Untuk melewati hook sementara:

```powershell
$env:GRAPHIFY_SKIP_HOOK = '1'
```