# Workflow Table / Icon / Icon List

User meminta tambahan tabel ala diagrams.net: jumlah row/column custom, resize, dan Font Awesome. Jawaban klarifikasi: kedua jenis ikon (standalone Icon + Icon List dengan teks) diminta.

Implementation: visual nodes `table`, `icon`, `icon-list` disimpan dalam graph.nodes; tidak memiliki port eksekusi. Tidak mengubah schema DB karena graph JSON sudah ada. Definisi visual tetap divalidasi dan tersimpan di draft/version/export/duplicate. Publish visual-only diagram boleh, simulate tanpa executable Trigger ditolak dengan pesan yang jelas. Mixed graph execution mengabaikan annotations dan masih memvalidasi executable graph seperti sebelumnya.

Table contract: config {rows:3, columns:3, cells:string[][], columnWidths:number[], rowHeights:number[], header:true, fontSize:14, align:'left', textColor:'#243746', background:'#ffffff', headerBackground:'#e8f5ef', borderColor:'#b7c9c0'}. Row/col count1..20, widths60..600, heights28..240, text cell max500. Title/header chrome height60px. Node width=sum columnWidths, height=sum rowHeights+60. Resize row/col+whole table with pointer at zoom-aware deltas; numeric alternatives. Insert/delete row/column preserve unaffected cells; undo/redo applies.

Icon contract: config {iconClass:'fas fa-star',color:'#1fa675',size:64,width:160,height:160}. IconList config {items:[{iconClass:'fas fa-check',text:'Item 1'}],color:'#1fa675',textColor:'#243746',fontSize:14,gap:8,width:280}. Max list30; text500. Icon width80..1000,height80..1000,size12..256; IconList width160..1000,font10..32,gap0..40. List height60+items*(max(24,fontSize+8)+gap). Title chrome height60. Use local metadata `/assets/plugins/fontawesome/5.15.3/metadata/icons.json` from Page Builder. Regular/Solid/Brands/Light/Duotone searchable, lazy load, paginate display. No new icon font/dependency.

Ownership: root owns graph helpers/types/sizing + PHP validation + backend/graph tests. UI agent owns workflow-editor.js, editor.blade.php, workflow-editor.css, new workflow-icons.js and its Node tests. Shared stable helper signatures pinned before dispatch. Browser QA exclusively root. No automatic Graphify update, no commit/push, preserve dirty old files. All artifacts project-artifacts; backups before any mutation.

Tasks:
- [x] Helper tests failing-first, helpers/types/geometry and validation.
- [x] Vue table/icon/icon-list renderer+inspector+picker+resizers.
- [x] PHP visual schema/input/publish/simulation tests+implementation.
- [x] Integrated tests+syntax+isolated browser QA for editing, row/col insertion, zoom resize, icon selection, persistence.
- [x] Review, final report and hashes.

Reference private diagram requires Google Drive authorization; not authorized. Official public draw.io table docs inspected for edit/insert/delete/resize semantics. No claim full draw.io parity; merge/formulas/swimlanes not requested.

