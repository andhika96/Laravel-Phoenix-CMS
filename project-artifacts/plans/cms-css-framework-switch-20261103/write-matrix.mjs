import fs from 'node:fs';
const dir='project-artifacts/plans/cms-css-framework-switch-20261103';
const audit=JSON.parse(fs.readFileSync(dir+'/component-audit.json','utf8'));
const mapping={
 'Modal / confirmation':['Tailwind + Dialog/AlertDialog (kandidat Reka)','Tinggi','Focus trap/return, backdrop static, keyboard, form tetap, modal berantai dan callback after-close'],
 'Native dialog':['Pertahankan dialog native dan controller existing; skin menyesuaikan','Tinggi','Discard perubahan, Escape dan fokus ketika berada di atas modal lain'],
 'Dropdown / action menu':['Tailwind + DropdownMenu','Tinggi','Keyboard, outside-click, auto-close outside, posisi/collision, action async'],
 'Collapse / accordion':['Tailwind + Collapsible/Accordion atau controller existing yang terbukti','Sedang','aria-expanded, height transition, nested menu, event setelah selesai'],
 'Toast / feedback':['Tailwind + Toast','Tinggi','Timeout/pause, live region, isShown, urutan tutup toast-modal, cleanup timer'],
 'Tooltip':['Tailwind + Tooltip','Sedang','Hover/focus, portal, collision, konten dinamis setelah Vue render'],
 'Popover':['Padanan tersedia; jangan dibuat sebelum ada consumer','Belum ditemukan','Scanner tidak menemukan pemakaian pada cakupan ini; konfirmasi G0'],
 'Offcanvas / drawer':['Skin Tailwind + controller mobile existing/primitive Dialog stabil','Tinggi','Body scroll lock, focus, inert, backdrop, resize 768/769, nested menu'],
 'Tabs':['Tailwind + Tabs bila consumer reachable','Sedang','Keyboard, aria-selected, panel tersembunyi, resize editor/chart'],
 'Carousel':['Styling memungkinkan; tidak masuk implementasi tanpa consumer','Belum ditemukan','Tidak ditemukan pada source scope ini; bukan klaim seluruh repository tidak punya'],
 'Pagination':['Pertahankan VuejsPaginateNext; ubah skin/kelas driver','Tinggi','Current/disabled, first/last/ellipsis, filter-reset, query, request race dan page limit'],
 'Table / responsive list':['HTML/Vue existing + utility/komponen CSS','Sedang','Sort/filter/checkbox, detail row, sticky menu, title panjang, overflow mobile'],
 'Form / input group':['Input native/Blade existing + Tailwind component styles','Tinggi','Label, padding, value, focus, autofill, readonly, disabled, input-group'],
 'Checkbox / radio / switch':['Input native existing; primitive tambahan hanya bila diperlukan','Sedang','Checked/indeterminate/disabled, label click, keyboard dan payload'],
 'Validation':['Pertahankan server validation dan Vue state; skin driver','Tinggi','aria-invalid/describedby, error placement, focus error pertama, input tidak hilang'],
 'Loading / placeholder / progress':['Tailwind + state existing; primitive Progress bila memang perlu','Sedang','Loading/submitting/success/error, tombol generated HTML, ukuran tidak meloncat'],
 'Buttons / badge / alert / card':['HTML/Blade/Vue existing + Tailwind CSS','Sedang','Normal/hover/focus/active/disabled, kontras matte/kuning/gelap, semantic danger'],
 'Sidebar / navigation':['Template tema existing + driver/skin','Tinggi','Active route, submenu/parent, collapsed/expanded/mobile, tooltip, role, palet'],
 'Breadcrumb':['Renderer breadcrumb existing + view/skin driver','Rendah','Urutan/link/label panjang, separator, area kecil dan aksesibilitas'],
};
let text='# Matriks komponen CMS dan padanan Tailwind\n\n3 November 2026. Hasil scan 65 view kandidat dan 28 script. Angka adalah jumlah file dengan rujukan pola, **bukan jumlah instance unik atau klaim semua komponen sudah diuji runtime**.\n\nTailwind menangani tampilan; perilaku di kolom padanan memerlukan komponen/controller yang sesuai. Status semua padanan adalah **rencana, belum prototype CMS Tailwind**.\n\n| Komponen | File terdeteksi | Padanan/rencana | Risiko | Kontrak yang wajib dipertahankan |\n|---|---:|---|---|---|\n';
for(const [name,data] of Object.entries(audit.categories)){
 const [target,risk,contract]=mapping[name];
 text+=`| ${name} | ${data.files} | ${target} | ${risk} | ${contract} |\n`;
}
text+='\n## Plugin yang tetap dipertahankan\n\nJumlah rujukan plugin termasuk asset include dan kode init; pemakaian aktif tiap halaman ditutup di G0.\n\n| Plugin/kelompok | File dengan rujukan | Penanganan |\n|---|---:|---|\n';
for(const [name,data] of Object.entries(audit.plugins).filter(([,data])=>data.files))text+=`| ${name} | ${data.files} | Pertahankan paket/logic; audit CSS, init, overlay, resize, lifecycle dan input/output. |\n`;
text+='\nVue Select, VueDatePicker dan VuejsPaginateNext mempunyai ketergantungan runtime Vue yang harus tetap sama. CKFinder merupakan media picker tersendiri. Referensi Leaflet CSS tidak membuktikan Leaflet map aktif; jsVectorMap mempunyai inisialisasi nyata di dashboard. Nama CSS dropzone bukan bukti library Dropzone/FilePond dipakai, sehingga tidak dimasukkan sebagai paket migrasi.\n\n## Sumber contoh yang sudah dibuka\n\n';
for(const [name,data] of Object.entries(audit.categories).filter(([,data])=>data.files)){
 text+=`- **${name}**: `+data.evidence.slice(0,2).map(item=>`[${item.file}](${process.cwd().replaceAll('\\','/')}/${item.file}:${item.line})`).join('; ')+'.\n';
}
text+='\n## Referensi kemampuan\n\n- [Tailwind utility styling](https://tailwindcss.com/docs/styling-with-utility-classes) dan [custom styles](https://tailwindcss.com/docs/adding-custom-styles): fondasi styling; tidak mengubah API Bootstrap.\n- [Reka introduction](https://www.reka-ui.com/docs/overview/introduction): pustaka Vue, unstyled, fokus aksesibilitas dan state yang dapat dikendalikan.\n- [Dialog](https://www.reka-ui.com/docs/components/dialog), [DropdownMenu](https://www.reka-ui.com/docs/components/dropdown-menu), [Toast](https://www.reka-ui.com/docs/components/toast), [Tooltip](https://www.reka-ui.com/docs/components/tooltip), [Collapsible](https://www.reka-ui.com/docs/components/collapsible): kandidat perilaku interaktif.\n- [Reka Pagination](https://www.reka-ui.com/docs/components/pagination): padanan tersedia, tetapi rencana memilih mempertahankan VuejsPaginateNext existing terlebih dahulu.\n- [Headless UI Vue Dialog](https://headlessui.com/v1/vue/dialog): alternatif Vue dengan focus/keyboard; bukan pilihan otomatis untuk seluruh kebutuhan CMS.\n\nKetersediaan komponen di dokumentasi tidak membuktikan kecocokan dengan Vue global, lifecycle dan plugin CMS. G1 pada PLAN.md wajib lulus sebelum kandidat dipilih final.\n';
fs.writeFileSync(dir+'/COMPONENT-MATRIX.md',text);
console.log('Component matrix written.');
