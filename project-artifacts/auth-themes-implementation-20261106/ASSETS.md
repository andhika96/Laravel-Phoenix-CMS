# Aset bawaan autentikasi Phoenix — 6 November 2026

Visual bawaan Clean Peach dan Ocean Split merupakan adaptasi visual dari mockup Dribbble melalui alat edit gambar bawaan. File gambar terpisah yang siap dipakai tidak disediakan pada halaman shot; mockup referensinya disimpan di `references/`. Tidak ada source code privat yang dikirim ke layanan gambar.

| Aset aktif | Referensi | Pengolahan |
| --- | --- | --- |
| `public/assets/images/auth/defaults/clean-illustration.png` | [Clean Login Design, Saad Shaikh](https://dribbble.com/shots/24750563-Clean-Login-Design) | Ilustrasi pria berambut cokelat dengan headphone putih dan laptop dipisahkan/direkonstruksi dari mockup; latar transparan. |
| `public/assets/images/auth/defaults/ocean-water.png` | [Login Page, Kosala B.](https://dribbble.com/shots/26236483-Login-Page) | Foto laut dan burung direkonstruksi tanpa form, teks, logo, atau bingkai. |
| Ikon bintang, kutip, panah | [Sign in Page design, Upnow Studio](https://dribbble.com/shots/23511411-Sign-in-Page-design) | Memakai Font Awesome 5 yang sudah terpasang: `fa-star`, `fa-asterisk`, `fa-quote-left`, `fa-arrow-left`, `fa-arrow-right`. |
| Ikon field dan kontrol | Font Awesome 5 proyek | `fa-envelope`, `fa-lock`, `fa-eye`, `fa-eye-slash`, `fa-check`, `fa-trash-alt`, `fa-image`. |

Gambar bawaan dipakai hanya jika opsi gambar belum pernah disimpan. Remove menyimpan `null` secara eksplisit; nilai ini tidak diganti kembali dengan bawaan. File bawaan adalah aset bersama dan tidak dihapus fisik saat satu halaman melepasnya. Thumbnail di `public/assets/images/auth/themes/` berasal dari render halaman, bukan screenshot referensi.

## Prompt akhir yang dipakai

Clean: Extract only the isolated 3D illustration of the brown haired young man wearing white over-ear headphones and a white short-sleeve t-shirt, typing on the light silver laptop. Preserve subject appearance, proportions, pose, clay-like 3D style, colors and viewing angle. Remove large external laptop/phone mockups, UI, text, peach background and arch. Keep the small laptop. Center the complete subject on a genuinely transparent background with clean alpha edges and modest padding.

Ocean: Extract and reconstruct only the photographic background of the supplied reference: overhead aerial very dark teal ocean, delicate white seafoam and the small white gull. Preserve moody photographic color and realistic water. Remove all UI, text, logos, forms, borders and white card areas; fill removed regions seamlessly. Produce one high resolution landscape photo, approximately 3:2, no typography or frame; gull small and slightly right of center.
