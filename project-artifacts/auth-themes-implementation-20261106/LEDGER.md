# Execution ledger

- Authorized: implementation of reviewed themes/settings/upload behavior, local project.
- Baseline: tracked checkout clean; existing preview and Graphify artifacts untracked and preserved.
- Memory: E: unavailable; relevant Phoenix auth handoff read from C:.
- Graph: Appearance controller EXTRACTED members and custom_page_theme INFERRED callers checked against active source; graph not changed.
- Ruling: reuse the current checkout on a codex branch, preserve all artifacts, make no commits or deployment.
- Ruling: keep only supported existing authentication flows; adding OAuth providers is outside this change. Do not ship working-looking dead provider buttons.
- Task 1 complete: original source hash manifest and four auth rows retained; additive migration applied locally, sixteen new theme rows registered without selecting them.
- Task 2 complete: image endpoint/storage tests passed; guards cover guest/non-admin, slot/path/type/size limits, stale remove, avatar limit, failed replacement, and regular-save image immutability. Empty image keys are rejected with the `missing` rule.
- Task 3 complete: existing Vue/Bootstrap/Coloris integration; actual browser upload 0/99 then 100, preview/open/close/remove and gray state passed. Modal teleports to body to avoid the CMS stacking context covering it.
- Task 4 complete: all sixteen new views share existing request/form contracts; all thirty-two old/new views render. Password toggles and custom button label recovery passed.
- Task 5 complete: 160 browser viewport checks across 32 views passed at 320/390/768/820/1280px. A further 20 Clean Peach checks passed after adding Font Awesome field icons. Thirty-six response/label/network-error checks passed. Actual local admin login and image filesystem creation/removal passed. No real account creation, password reset or outgoing email was performed.
- Task 6 complete: one fresh read-only Terra review found the recovery-notice location and signup reCAPTCHA action mismatch. Both fixed and covered by tests. Additional runtime checks fixed modal backdrop stacking and preserved image references against empty normal-save payloads.
- Final test: php vendor/phpunit/phpunit/phpunit project-artifacts/auth-themes-implementation-20261106/tests --colors=never -> 15 tests, 220 assertions passed. PHP lint, JS syntax and git diff --check passed.
- Incident/recovery: Site Config is MyISAM, so initial transaction-based captcha fixtures persisted. The original six changed fields were recovered from the first matching before-image in binlog.001527, restored and verified. The captcha tests now override retrieved model values in memory; subsequent complete runs left the original config intact. No secret values are recorded in the recovery report.
- Final: Ruling: the pre-existing optional Terms checkbox behavior remains unchanged; this task changes auth themes, not registration policy. Cost if policy requirements change: that separate backend policy would need implementation.
- User correction: retain original image-only card shape, fill the gallery width with five columns on wide screens, reduce check marker to 25px, and use removable default visuals adapted from the supplied Dribbble designs; available control/decorative icons use existing Font Awesome 5.
- User changes: the user saved a new Login theme and theme drafts while implementation was active. These settings were preserved; only the known null image override introduced by browser QA was removed, with a selected-theme guard and a before snapshot.
- Graph remains unchanged, SHA256 0cb6deffe24e3d77dd2d4a59281835dc773a73f6155c3bab9650f4a67056ecb6. No commit, staging, push or deployment. Plans/backups retained per user instruction.
