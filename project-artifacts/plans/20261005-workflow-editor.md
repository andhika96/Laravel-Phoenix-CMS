# Workflow Editor Vue 3 — implementation plan

Goal: halaman daftar dan editor workflow aktif di Phoenix, definisi tersimpan di database, versi publish immutable, simulasi deterministik tercatat, serta schema eksekusi/approval.

Architecture: global Vue 3 milik tema CMS + Blade + CSS scoped; API session Laravel. Workflow terpisah dari Page Builder. Graph berisi nodes/edges/viewport. Simulasi memakai graph tersimpan dan tidak mengirim email/webhook atau mengubah data modul lain.

Constraints: tidak install dependency; semua backup/test/QA di project-artifacts; preserve dirty Article files; Graphify query-only; tidak menjalankan migration lain. User meminta langsung implementasi berdasarkan diskusi, VueJS 3 wajib.

Tasks:
- [x] Focused failing PHP tests: schema rollback, routes/auth, persistence/revision, publish validation/version isolation, simulation branches and no side effects.
- [x] Add migrations/models, guarded routes/controller, graph validation and simulation.
- [x] Add Vue 3 list/canvas/inspector, JSON import/export, undo/redo, responsive controls and frontend checks.
- [x] Back up routes/web.php and append isolated route include.
- [x] Run focused PHP/Node/syntax/route checks, isolated migration up/down rehearsal and targeted local migrations.
- [x] Inspect integrated UI in existing browser session when available; otherwise isolated browser harness with explicit runtime limitation.
- [x] Record QA and final scope. No automatic Graphify update, commit or push.

Acceptance: CRUD/archive/duplicate, incomplete drafts accepted, publication rejects invalid/unreachable/cyclic graphs, concurrent writes return 409, simulation evaluates configured conditions, revisions don't mutate published versions, endpoints enforce authorization/ownership, Vue controls functional without real external effects.

Remaining after this stage: automatic Form/data triggers, production action execution, real human approval inbox/resume and scheduler integration. Schema prepared here does not mean these integrations are active.

