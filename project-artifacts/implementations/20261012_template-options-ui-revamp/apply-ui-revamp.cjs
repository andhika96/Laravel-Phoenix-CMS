const fs = require('node:fs');
const path = require('node:path');
const root = process.cwd();
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const write = (file, value) => fs.writeFileSync(path.join(root, file), value);
function replace(source, before, after) {
  if (!source.includes(before)) throw new Error('Missing edit anchor: ' + before.slice(0, 80));
  return source.replace(before, after);
}
const cssPath = 'public/assets/css/article/article-template-manager-2026.css';
let css = read(cssPath);
const rhythm = css.indexOf('/* Separate settings from their neighbours, while keeping labels close to their inputs. */');
if (rhythm < 0) throw new Error('Missing form rhythm anchor');
const deviceTabs = css.slice(css.indexOf('.article-template-options-panel .article-template-device-tabs {', rhythm));
css = css.slice(0, rhythm) + deviceTabs + '\n/* Shared Template Options layout and form scale, verified across archive and detail templates. */\n' + read('project-artifacts/mockups/20261012_template-options-ui-revamp/revamp.css');
css += `
.article-template-notice .toast-container { z-index: 1100; }
.article-template-notice .toast { max-width: calc(100vw - 32px); }
.article-template-options-dismiss,
.article-template-options-preview__error {
  position: relative;
  border: 0;
  overflow: hidden;
  color: var(--ph-callout-color);
  background-color: var(--ph-callout-bg);
}
`;
write(cssPath, css);

const jsPath = 'public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js';
let js = read(jsPath).replace(/\r\n/g, '\n');
js = replace(js, "\t\t\t\tthis.modalPreviewError = 'Preview timed out. Try again.';", "\t\t\t\tthis.modalPreviewError = 'Preview timed out. Try again.';\n\t\t\t\tthis.showNotice('danger', this.modalPreviewError);");
js = replace(js, "\t\t\t\tthis.modalPreviewError = 'Preview is unavailable. Check your session and retry.';", "\t\t\t\tthis.modalPreviewError = 'Preview is unavailable. Check your session and retry.';\n\t\t\t\tthis.showNotice('danger', this.modalPreviewError);");
js = replace(js, "\t\t\tthis.modalPreviewError = 'Unable to load the preview.';", "\t\t\tthis.modalPreviewError = 'Unable to load the preview.';\n\t\t\tthis.showNotice('danger', this.modalPreviewError);");
js = replace(js, "\t\t\t\tthis.optionsModal.dismissOpen = true;\n\t\t\t\treturn;", "\t\t\t\tthis.optionsModal.dismissOpen = true;\n\t\t\t\tthis.showNotice('warning', 'You have unsaved changes. Keep editing or discard them.');\n\t\t\t\treturn;");
js = replace(js, "\t\t\tthis.closeTemplateOptions();\n\t\t},\n\t\tcolumnChoices", "\t\t\tthis.closeTemplateOptions();\n\t\t\tthis.showNotice('success', 'Changes applied to the draft. Save Template to persist them.');\n\t\t},\n\t\tcolumnChoices");
js = replace(js, '        async save() {', `        showNotice(type, message) {
            this.noticeType = ['success', 'warning'].includes(type) ? type : 'danger';
            this.notice = message;
            this.$nextTick(() => {
                const toast = document.getElementById('article-template-notice');
                if (toast && typeof bootstrap !== 'undefined') bootstrap.Toast.getOrCreateInstance(toast).show();
            });
        },
        async save() {`);
js = replace(js, "            this.notice = '';\n            try {", '            try {');
js = replace(js, "                this.notice = response.data.message || 'Article templates saved successfully';\n                this.noticeType = 'success';", "                this.showNotice('success', response.data.message || 'Article templates saved successfully');");
js = replace(js, "                const message = error.response?.data?.message;\n                this.notice = typeof message === 'string' ? message : Object.values(message || {}).flat().join(' ') || 'Unable to save article templates';\n                this.noticeType = 'danger';", "                const message = error.response?.data?.errors || error.response?.data?.message;\n                this.showNotice('danger', typeof message === 'string' ? message : Object.values(message || {}).flat().join(' ') || 'Unable to save article templates');");
write(jsPath, js);

const viewPath = 'resources/views/manage_article/templates/index.blade.php';
let view = read(viewPath);
view = replace(view, '            <div v-if="notice" class="alert" :class="noticeType === \'success\' ? \'alert-success\' : \'alert-danger\'" role="status">@{{ notice }}</div>', '');
view = view.replace(/rows="10"/g, 'rows="3"');
view = replace(view, 'class="article-template-options-dismiss" role="alert"', 'class="article-template-options-dismiss ph-callout-warning ph-callout-no-border" role="alert"');
view = replace(view, 'class="article-template-options-preview__error" role="alert"', 'class="article-template-options-preview__error ph-callout-danger ph-callout-no-border" role="alert"');
view = replace(view, '            </Teleport>', `                <div class="ph-notice article-template-notice" v-cloak>
                    <div aria-live="polite" aria-atomic="true" class="position-relative">
                        <div class="toast-container position-fixed top-0 end-0 p-3">
                            <div id="article-template-notice" :class="'toast ph-notice-toast ph-callout-no-border ph-callout-' + noticeType" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                                <div :class="'toast-header ph-callout-' + noticeType + ' px-3 pt-3 pb-1 border-0'">
                                    <strong class="me-auto">{{ t('Notice') }}</strong>
                                    <small>{{ t('just now') }}</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ t('Close') }}"></button>
                                </div>
                                <div class="toast-body p-3 text-start">@{{ notice }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>`);
write(viewPath, view);

const testPath = 'tests/manage-article-template-manager.test.mjs';
let tests = read(testPath).replace(/\r\n/g, '\n');
tests = replace(tests, '\t\tcloseTemplateOptions() { this.modalClosed = true; },', '\t\tcloseTemplateOptions() { this.modalClosed = true; },\n\t\tshowNotice(type, message) { this.notice = { type, message }; },');
tests = replace(tests, '\t\tcloseTemplateOptions: options.methods.closeTemplateOptions,', '\t\tcloseTemplateOptions: options.methods.closeTemplateOptions,\n\t\tshowNotice(type, message) { this.notice = { type, message }; },');
tests = replace(tests, '\t\tmodalPreviewError: \'\',\n\t\tmodalPreviewTimer: null,', '\t\tmodalPreviewError: \'\',\n\t\tshowNotice(type, message) { this.notice = { type, message }; },\n\t\tmodalPreviewTimer: null,');
write(testPath, tests);
