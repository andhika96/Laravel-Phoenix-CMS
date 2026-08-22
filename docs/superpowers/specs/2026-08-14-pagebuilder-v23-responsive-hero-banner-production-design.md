# Page Builder v2.3 Responsive Hero Banner Production Design

**Tanggal:** 2026-08-14

## Approved Product Decision

Widget bernama **Hero Banner**, type internal `hero_banner`, category `pro`, dan icon `fas fa-image`. Category Pro hanya pengelompokan toolbox; tidak menambah licensing atau feature gate.

Prototype yang disetujui di `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype` adalah visual dan interaction source of truth. Implementasi hanya untuk Page Builder v2.3.

## Production Contract

- Dedicated definition, Settings, Canvas, dan Blade view agar tidak menambah cabang baru ke shared Pro SFC yang sudah besar.
- Definition menormalisasi satu sampai tiga button, content order, enum, responsive settings, dan default Advanced settings.
- Settings menyediakan Content, Style, dan Advanced.
- Editor canvas dan frontend memakai schema setting yang sama.
- Persistence memakai payload node v2.3 yang sudah ada; tidak membuat endpoint atau tabel baru.
- CKFinder memakai `editor.chooseMedia(target, key)` yang sudah ada.
- Link memakai shared `LinkControl` serta whitelist custom attributes yang sudah ada pada renderer v2.3.
- Popup memakai `openMediaLightbox` dari `frontend-runtime.js`; runtime tersebut diperluas untuk Dailymotion dan self-hosted video, focus return, dan body scroll lock tanpa membuat modal kedua.

## Content Schema

Global settings:

- `positioningMode`: `grouped|independent`.
- `title`, `subtitle`, `titleTag`, `subtitleTag`.
- `contentOrder`: permutation dari `title`, `subtitle`, `buttons`.
- `showTitle`, `showSubtitle`, `showButtons`.
- `buttons`: satu sampai tiga item.

Button item:

```js
{
  id: 'hero-button-1',
  text: 'Watch Video',
  actionType: 'link|video_popup|image_popup',
  linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [],
  videoSource: 'youtube|vimeo|dailymotion|self_hosted', videoUrl: '',
  imageSource: 'ckfinder|url', imageUrl: '', imageAlt: ''
}
```

Responsive keys mengikuti suffix v2.3 (`Tablet`, `Mobile`) dan inheritance Mobile → Tablet → Desktop:

- hero media: `imageSource`, `imageUrl`, `imageAlt`, `objectFit`, `objectPosition`;
- size: `minHeight`;
- grouped/independent target: `{group|title|subtitle|buttons}{Anchor|X|Y|Width|Align}`;
- Button Group: `buttonDirection`, `buttonAlign`, `buttonGap`, `buttonWrap`.

## Content Panel

- Content Behavior: Grouped atau Independent.
- Title dan Subtitle content/tag/visibility.
- Content Order hanya pada Grouped, memakai Move Up/Down.
- Buttons repeater dengan add, duplicate, remove, batas 1–3.
- Conditional action controls per button.
- Responsive Position menampilkan target Group atau Title/Subtitle/Button Group.
- Button Group Layout responsive.
- Responsive Media dengan CKFinder atau External URL dan reset override.

## Style Panel

- responsive minimum height dan content gap;
- overlay color;
- title/subtitle color, size, weight;
- button normal/hover text/background, border radius, padding;
- shared modal background dan UI colors.

Advanced memakai komponen `WidgetAdvancedControls` yang sudah dipakai widget v2.3 lain.

## Canvas and Frontend

- Hero adalah relative container dengan responsive image layer, optional overlay, dan absolute content target.
- Grouped merender content sesuai `contentOrder`; Independent merender tiga target terpisah.
- Button Group selalu satu positioning target.
- Link kosong tidak menavigasi.
- Video URL dinormalisasi untuk YouTube, Vimeo, Dailymotion; self-hosted memakai native video.
- Image/video popup memiliki close button, Escape, backdrop close, focus return, body scroll lock, dan media teardown.
- Reduced motion menonaktifkan transitions yang relevan.

## Safety and Isolation

- URL media menerima HTTP(S) atau local absolute path; protocol-relative dan unsafe schemes ditolak.
- Link custom attributes hanya melewati whitelist existing v2.3.
- Type, tag, enum, number, color, dan CSS length dinormalisasi sebelum output.
- Tidak mengubah Page Builder v2.0.
- Browser QA tidak menekan Save.

## Verification

- Node parity test untuk definition, Settings, Canvas, app integration, CSS, dan runtime.
- Laravel feature test untuk registry, safe renderer, responsive output, actions, custom attributes, dan invalid URL rejection.
- Existing v2.3 frontend runtime tests dan PageBuilderElementor test suite.
- Browser QA: toolbox, add widget, Content/Style/Advanced, responsive preview, popup, console, dan frontend render bila route fixture tersedia; editor state tidak disimpan.
