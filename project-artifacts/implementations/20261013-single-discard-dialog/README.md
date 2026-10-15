# Single discard confirmation — 13 Oktober 2026

Implemented: removed duplicate warning toast and footer confirmation. One native modal dialog centered over Template Options with safe initial focus, Keep editing, Discard changes, Escape cancellation and responsive mobile actions. Existing CMS success/error notices unchanged.

Files: manager Vue JS, manager CSS, index Blade, manager unit tests. Backups listed in backup.txt. Context: prior implementation report in project, screenshot supplied by user, Graphify query closeTemplateOptions keepEditing, direct source/callers verified. Graph not updated (manual only).

Verification: 35 Node tests PASS, Blade compile PASS, six browser checks PASS on actual source fixture: exactly one dialog/no toast, no footer warning, safe focus, Escape preserves draft and parent, 390px viewport fit, Discard closes both dialogs. Desktop/mobile screenshots available here. Authenticated CMS session not directly exercised; source fixture uses actual production modal/CSS/JS.
