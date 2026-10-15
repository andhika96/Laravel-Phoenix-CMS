from pathlib import Path
p=Path('resources/views/manage_article/templates/index.blade.php');s=p.read_text();start=s.index('<div v-if="optionsModal.dismissOpen"');end=s.index('<div class="article-template-options-footer-main">',start);s=s[:start]+s[end:];anchor='<div class="modal-footer" v-if="optionsModal.value">';dialog='''<dialog ref="discardDialog" class="article-template-discard-dialog" aria-labelledby="article-discard-title" aria-describedby="article-discard-description" @cancel.prevent.stop="keepEditing" @keydown.esc.stop>
+ <div class="article-template-discard-dialog__icon" aria-hidden="true"><i class="fas fa-exclamation-triangle"></i></div>
+ <h5 id="article-discard-title">{{ t('Discard unsaved changes?') }}</h5>
+ <p id="article-discard-description">{{ t('Your changes in Template Options have not been applied. Discard them or continue editing.') }}</p>
+ <div class="article-template-discard-dialog__actions"><button autofocus type="button" class="btn ph-btn-theme-outline" @click="keepEditing">{{ t('Keep editing') }}</button><button type="button" class="btn btn-danger" @click="discardTemplateOptions">{{ t('Discard changes') }}</button></div>
+ </dialog>
+ '''.replace('\n+','\n');s=s.replace(anchor,dialog+anchor);p.write_text(s)
p=Path('public/assets/css/article/article-template-manager-2026.css');s=p.read_text();s+='''
/* One focused confirmation for discarding the options draft. */
.article-template-discard-dialog { width: min(440px, calc(100vw - 32px)); max-height: calc(100dvh - 32px); margin: auto; padding: 28px; border: 1px solid #e4e7ec; border-radius: 16px; color: #172033; background: #fff; box-shadow: 0 24px 64px #10182833; font-family: var(--ph-font-family, inherit); }
.article-template-discard-dialog::backdrop { background: rgb(16 24 40 / 38%); }
.article-template-discard-dialog__icon { display: grid; place-items: center; width: 44px; height: 44px; margin-bottom: 18px; border-radius: 50%; background: #fff4df; color: #a76808; font-size: 18px; }
.article-template-discard-dialog h5 { margin: 0 0 10px; font-size: 18px; font-weight: 700; line-height: 1.4; }
.article-template-discard-dialog p { margin: 0; color: #667085; font-size: 14px; line-height: 1.65; }
.article-template-discard-dialog__actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }
.article-template-discard-dialog__actions .btn { min-height: 40px; font-size: 14px; padding: 8px 14px; }
@media (max-width: 480px) { .article-template-discard-dialog { padding: 24px; } .article-template-discard-dialog__actions { display: grid; grid-template-columns: 1fr; } .article-template-discard-dialog__actions .btn { min-height: 44px; } }
''';p.write_text(s)
p=Path('tests/manage-article-template-manager.test.mjs');s=p.read_text();s=s.replace("test('modal dismissal exposes an inline dirty confirmation and retryable preview lifecycle'", "test('modal dismissal uses one focused confirmation without a duplicate toast'");s=s.replace('closeTemplateOptions: options.methods.closeTemplateOptions,',"closeTemplateOptions: options.methods.closeTemplateOptions,\n        $nextTick(callback) { callback(); },\n        $refs: { discardDialog: { showModal() {}, close() {} } },");s=s.replace("assert.equal(state.notice.type, 'warning');","assert.equal(state.notice, undefined);");p.write_text(s)
