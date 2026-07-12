# Database Theme Activation Backup

Captured before activating `arunika_v2` on 2026-07-12 Asia/Jakarta.

## Active theme setting

```json
{
  "id": 1,
  "theme_id": 5,
  "theme_code": "arunika_v1",
  "theme_name": "Arunika v1 Theme"
}
```

## Existing Arunika V1 theme

```json
{
  "id": 5,
  "theme_code": "arunika_v1",
  "theme_name": "Arunika V1 Theme",
  "theme_foldername": "arunika_v1",
  "theme_cms": "cms_layout",
  "theme_auth": "auth_layout",
  "theme_frontend": "frontend_layout",
  "theme_version": "1.0.0",
  "created_at": "2025-06-18 03:54:21"
}
```

## Rollback

```sql
UPDATE theme_settings
SET theme_id = 5,
    theme_code = 'arunika_v1',
    theme_name = 'Arunika v1 Theme'
WHERE id = 1;

DELETE FROM themes
WHERE theme_code = 'arunika_v2';
```
