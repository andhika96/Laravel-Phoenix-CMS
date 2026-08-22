# CMS Real-time Notification — Laravel Reverb Setup Guide

## File yang dibuat/dimodifikasi

### File Baru
- `app/Events/CMS/UserRegistered.php`  — Event: user baru dibuat
- `app/Events/CMS/UserUpdated.php`     — Event: user diperbarui
- `app/Events/CMS/ArticleCreated.php`  — Event: artikel baru dibuat
- `app/Events/CMS/ArticleUpdated.php`  — Event: artikel disunting
- `resources/views/components/cms-realtime-notification.blade.php` — Komponen bell + toast

### File Dimodifikasi
- `.env` — `BROADCAST_CONNECTION=reverb` + REVERB_ keys
- `app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_User_Controller.php` — dispatch `UserRegistered` & `UserUpdated`
- `app/Http/Controllers/Web/Manage_Article/Manage_Article_Controller.php` — dispatch `ArticleCreated` & `ArticleUpdated`
- `resources/views/themes/arunika_v1/cms/cms_layout.blade.php` — inject komponen notifikasi
- `resources/views/themes/arunika_v2/cms/cms_layout.blade.php` — inject komponen notifikasi

---

## Cara Menjalankan

### 1. Pastikan .env sudah benar
```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=cms-main
REVERB_APP_KEY=cms-main-key
REVERB_APP_SECRET=cms-main-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

> **Catatan:** Jika Anda sudah punya `REVERB_APP_KEY` dari instalasi Reverb sebelumnya
> (misal untuk File Manager), gunakan nilai yang sama atau buat app baru di `config/reverb.php`.

### 2. Jalankan Reverb server (terminal terpisah)
```bash
php artisan reverb:start --host=127.0.0.1 --port=8080
```

Atau dengan debug:
```bash
php artisan reverb:start --host=127.0.0.1 --port=8080 --debug
```

### 3. Clear config cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. (Opsional) Jika pakai Queue untuk broadcast
Saat ini event menggunakan `ShouldBroadcastNow` (synchronous, tanpa queue).
Jika ingin async, ubah ke `ShouldBroadcast` dan jalankan:
```bash
php artisan queue:work
```

---

## Cara Kerja

```
User/Admin action
    ↓
Controller (store/update)
    ↓
event(new UserRegistered(...))  ←── Langsung dispatch (ShouldBroadcastNow)
    ↓
Laravel Reverb Server (WebSocket)
    ↓
Pusher.js di browser (semua tab yang terbuka)
    ↓
Toast popup + Bell badge notification
```

## Channel & Events

| Channel | Event | Trigger |
|---------|-------|---------|
| `cms-notifications` | `user.registered` | Admin buat user baru |
| `cms-notifications` | `user.updated` | Admin update user |
| `cms-notifications` | `article.created` | User buat artikel baru |
| `cms-notifications` | `article.updated` | User sunting artikel |

---

## Troubleshooting

**Notifikasi tidak muncul?**
1. Pastikan Reverb server berjalan: `php artisan reverb:start`
2. Cek browser console: seharusnya ada log `✅ CMS Notifications: Reverb connected`
3. Pastikan `BROADCAST_CONNECTION=reverb` di `.env` (bukan `log`)
4. Jalankan `php artisan config:clear` setelah ubah `.env`

**Port konflik dengan File Manager Reverb?**
- File Manager pakai `REVERB_FM_APP_KEY` di app/server yang sama
- CMS Main pakai `REVERB_APP_KEY`
- Kedua app bisa share server Reverb yang sama (Reverb route by app_key)
