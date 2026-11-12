from pathlib import Path
from datetime import datetime
import hashlib
import json
import re
import shutil

root = Path(__file__).resolve().parents[2]
artifact = Path(__file__).resolve().parent
stamp = datetime.now().strftime('%Y%m%d_%H%M%S')
backup = artifact / ('backups_' + stamp)
changes = {}

def edit(name, transform):
    source = (root / name).read_bytes()
    text = source.decode('utf-8')
    updated = transform(text)
    assert updated != text, name
    changes[name] = (source, updated.encode('utf-8'))

def replace(text, before, after):
    assert before in text, before[:100]
    return text.replace(before, after)

# Reuse the exact CMS shell, preserving preview widths and scroll settings.
for name in [
    'awesome_admin/awesome_admin_smtp.blade.php',
    'awesome_admin/awesome_admin_appearance.blade.php',
    'awesome_admin/awesome_admin_themes.blade.php',
    'awesome_admin/menu/backend/awesome_admin_category_menu_be.blade.php',
    'awesome_admin/menu/frontend/awesome_admin_category_menu_fe.blade.php',
    'awesome_admin/menu/frontend/awesome_admin_parent_menu_fe.blade.php',
]:
    def standardize(text):
        text = re.sub(r'class="modal-dialog (?!ph-modal-dialog)', 'class="modal-dialog ph-modal-dialog ', text)
        if name.endswith('awesome_admin_smtp.blade.php'):
            text = replace(text, 'class="modal fade" id="modalDeleteSMTP"', 'class="modal ph-modal-delete fade" id="modalDeleteSMTP"')
        if 'category_menu_' in name:
            text = replace(text, 'class="modal fade" id="modalDeleteCategoryMenu"', 'class="modal ph-modal-delete fade" id="modalDeleteCategoryMenu"')
        if name.endswith('awesome_admin_parent_menu_fe.blade.php'):
            text = replace(text, 'class="modal-header px-4 py-3"', 'class="modal-header"')
            text = replace(text, 'class="modal-footer px-4 py-3"', 'class="modal-footer"')
        return text
    edit('resources/views/' + name, standardize)

def confirmation(modal_id, title, message, value, handler, cancel='No, keep it', action='Yes, Delete', icon='trash-alt'):
    return f'''<div class="modal ph-modal-delete fade" id="{modal_id}" tabindex="-1" aria-labelledby="{modal_id}Label" aria-hidden="true">
            <div class="modal-dialog ph-modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body pt-5 px-5 text-center">
                        <div class="mb-4"><i class="far fa-{icon} fs-1" aria-hidden="true"></i></div>
                        <h5 id="{modal_id}Label" class="modal-title fw-bold">{{{{ t('{title}') }}}}</h5>
                        <p class="mb-2"><strong>@{{{{ {value} }}}}</strong></p>
                        <div>{{{{ t('{message}') }}}}</div>
                    </div>
                    <div class="modal-footer pb-5 d-block border-0">
                        <div class="row gx-2 justify-content-center">
                            <div class="col-auto"><button type="button" class="btn btn-secondary font-size-inherit" data-bs-dismiss="modal">{{{{ t('{cancel}') }}}}</button></div>
                            <div class="col-auto"><button type="button" class="btn ph-btn-theme font-size-inherit" @click="{handler}">{{{{ t('{action}') }}}}</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>'''

def event_list(text):
    old = next(line.strip() for line in text.splitlines() if 'id="eventDeleteModal"' in line)
    text = replace(text, old, '<Teleport to="body">\n        ' + confirmation('eventDeleteModal', 'Delete Event', 'Do you really want to delete this event? This process cannot be undone.', 'selectedEvent?.title', 'deleteEvent') + '\n        </Teleport>')
    return replace(text, 'class="modal fade" id="eventCategoryDeleteModal"', 'class="modal ph-modal-delete fade" id="eventCategoryDeleteModal"')

edit('resources/views/manage_event/manage_event.blade.php', event_list)

def event_form(text):
    for modal_id, title, message, handler, action, icon in [
        ('eventOccurrenceDeleteModal', 'Delete occurrence', 'Do you really want to delete this occurrence? This process cannot be undone.', 'deleteOccurrence', 'Yes, Delete', 'trash-alt'),
        ('eventOccurrenceCancelModal', 'Cancel occurrence', 'All active registrations will be cancelled and notified.', 'cancelOccurrence', 'Cancel session', 'calendar-times'),
    ]:
        old = next(line.strip() for line in text.splitlines() if f'id="{modal_id}"' in line)
        text = replace(text, old, '<Teleport to="body">\n        ' + confirmation(modal_id, title, message, 'pendingOccurrence?.label', handler, action=action, icon=icon) + '\n        </Teleport>')
    return text

edit('resources/views/manage_event/partials/form.blade.php', event_form)

def shared(text):
    marker = '.ph-modal-dialog .modal-content'
    native = '''/* Native CMS dialogs share the Bootstrap modal shell without changing their lifecycle. */
.ph-modal-native
{
    --bs-modal-bg: var(--cms-bs-surface, var(--bs-body-bg));
    --bs-modal-color: var(--cms-bs-ink, var(--bs-body-color));
    --bs-modal-border-color: var(--ph-border-color, var(--bs-border-color));
    --bs-modal-border-width: var(--bs-border-width);
    --bs-modal-header-padding: 1rem;
    --bs-modal-padding: 1rem;
    --bs-modal-footer-gap: .5rem;
    --bs-modal-footer-bg: transparent;
    width: min(500px, calc(100% - 1rem));
    max-width: none;
    max-height: calc(100% - 1rem);
    margin: auto;
    padding: 0;
    border: 0;
    border-radius: 1rem;
    background: transparent;
    color: var(--bs-modal-color);
    font: inherit;
}

.ph-modal-native::backdrop
{
    background: rgba(0, 0, 0, .5);
}

'''
    return replace(text, marker, native + marker)

edit('public/assets/css/shared/phoenix-cms.css', shared)

def header(text):
    start = text.index('\t<dialog id="headerEditorExitDialog"')
    end = text.index('\t</dialog>', start) + len('\t</dialog>')
    return text[:start] + '''\t<dialog id="headerEditorExitDialog" class="ph-modal-native ph-modal-dialog" aria-labelledby="headerEditorExitTitle" aria-describedby="headerEditorExitDescription">
        <div class="modal-content">
            <div class="modal-header"><h5 id="headerEditorExitTitle" class="modal-title">Leave without saving?</h5></div>
            <div class="modal-body"><p id="headerEditorExitDescription" class="mb-0">Your unsaved header navigation changes will be lost.</p></div>
            <div class="modal-footer header-editor-dialog-actions">
                <button type="button" class="btn btn-secondary font-size-inherit" id="headerEditorKeepEditing" autofocus>Keep editing</button>
                <button type="button" class="btn ph-btn-theme font-size-inherit" id="headerEditorDiscard">Leave without saving</button>
            </div>
        </div>
    </dialog>''' + text[end:]

edit('resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php', header)

def header_css(text):
    start = text.index('#headerEditorExitDialog {')
    end = text.index('@media (max-width: 900px)', start)
    return text[:start] + '#headerEditorExitDialog { width: min(var(--ph-modal-update-width, 500px), calc(100vw - 1rem)); }\n' + text[end:]

edit('public/assets/css/header-navigation/awesome-admin-header-navigation-fullscreen.css', header_css)

def workflow(text):
    text = replace(text, 'class="workflow-icon-dialog"', 'class="workflow-icon-dialog ph-modal-native ph-modal-dialog"')
    text = replace(text, 'class="workflow-icon-dialog__panel"', 'class="workflow-icon-dialog__panel modal-content"')
    text = replace(text, 'class="workflow-icon-dialog__header"', 'class="workflow-icon-dialog__header modal-header"')
    text = replace(text, 'class="workflow-icon-dialog__footer"', 'class="workflow-icon-dialog__footer modal-footer"')
    text = replace(text, 'id="workflow-icon-dialog-title" class="h5 mb-0"', 'id="workflow-icon-dialog-title" class="modal-title h5 mb-0"')
    # Only the picker footer/header; other compact editor actions are unchanged.
    start = text.index('<dialog ')
    end = text.index('</dialog>', start)
    section = text[start:end].replace('class="btn btn-sm btn-outline-secondary"', 'class="btn btn-secondary font-size-inherit"').replace('class="btn btn-sm ph-btn-theme"', 'class="btn ph-btn-theme font-size-inherit"')
    return text[:start] + section + text[end:]

edit('resources/views/workflow/editor.blade.php', workflow)

def workflow_css(text):
    text = replace(text, '.workflow-icon-dialog::backdrop {\n    background: rgba(16, 24, 40, .58);\n}\n\n', '')
    text = replace(text, '    border-radius: .7rem;\n    box-shadow: var(--workflow-shadow);\n    display: flex;\n    flex-direction: column;\n    height: 100%;', '    border-radius: 1rem;\n    display: flex;\n    flex-direction: column;\n    height: 100%;')
    text = replace(text, '    padding: .9rem 1rem;\n}\n\n.workflow-icon-dialog__header {\n    border-bottom: 1px solid var(--workflow-border);', '    padding: 1rem;\n}\n\n.workflow-icon-dialog__header {\n    border-bottom: 0;\n    padding-bottom: 0;')
    return replace(text, '.workflow-icon-dialog__footer {\n    border-top: 1px solid var(--workflow-border);', '.workflow-icon-dialog__footer {\n    border-top: 0;\n    padding-top: 0;')

edit('public/assets/css/workflow/workflow-editor.css', workflow_css)

def template_css(text):
    # Preserve the editor's layout while letting the shared shell control its separators.
    for selector, prop in [('modal-header', 'border-bottom'), ('modal-footer', 'border-top')]:
        pattern = rf'(\.article-template-options-modal \.{selector}\s*\{{[^}}]*?){prop}: 1px solid var\(--article-template-line\);'
        text, count = re.subn(pattern, rf'\g<1>{prop}: 0;', text)
        assert count == 1, selector
    # The nested discard dialog must not inherit the full-height options shell.
    for part in ['modal-content', 'modal-header', 'modal-body', 'modal-footer']:
        old = '.article-template-options-modal .' + part
        new = '.article-template-options-modal > .modal-dialog > .modal-content'
        if part != 'modal-content':
            new += ' > .' + part
        text = text.replace(old, new)
    start = text.index('.article-template-discard-dialog {')
    end = text.index('/* Focused Reader', start)
    text = text[:start] + '.article-template-discard-dialog { width: min(var(--ph-modal-delete-width, 425px), calc(100vw - 1rem)); }\n\n' + text[end:]
    return text

edit('public/assets/css/article/article-template-manager-2026.css', template_css)

def discard(text):
    start = text.index('<dialog ref="discardDialog"')
    end = text.index('</dialog>', start) + len('</dialog>')
    return text[:start] + '''<dialog ref="discardDialog" class="article-template-discard-dialog ph-modal-native ph-modal-dialog" aria-labelledby="article-discard-title" aria-describedby="article-discard-description" @cancel.prevent.stop="keepEditing" @keydown.esc.stop>
                                <div class="modal-content">
                                    <div class="modal-body pt-5 px-5 text-center">
                                        <div class="mb-4"><i class="far fa-exclamation-triangle fs-1" aria-hidden="true"></i></div>
                                        <h5 id="article-discard-title" class="modal-title fw-bold">{{ t('Discard unsaved changes?') }}</h5>
                                        <p id="article-discard-description" class="mb-0">{{ t('Your changes in Template Options have not been applied. Discard them or continue editing.') }}</p>
                                    </div>
                                    <div class="modal-footer pb-5 d-block border-0">
                                        <div class="row gx-2 justify-content-center">
                                            <div class="col-auto"><button autofocus type="button" class="btn btn-secondary font-size-inherit" @click="keepEditing">{{ t('Keep editing') }}</button></div>
                                            <div class="col-auto"><button type="button" class="btn ph-btn-theme font-size-inherit" @click="discardTemplateOptions">{{ t('Discard changes') }}</button></div>
                                        </div>
                                    </div>
                                </div>
                            </dialog>''' + text[end:]

edit('resources/views/manage_article/templates/index.blade.php', discard)

manifest = []
for name, (before, after) in changes.items():
    target = root / name
    saved = backup / name
    saved.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(target, saved)
    assert saved.read_bytes() == before
    manifest.append({'source': name, 'backup': str(saved.relative_to(root)), 'sha256': hashlib.sha256(before).hexdigest()})
for name, (_, after) in changes.items():
    (root / name).write_bytes(after)
(artifact / 'backup-manifest.json').write_text(json.dumps(manifest, indent=2), encoding='utf-8')
print(json.dumps({'files': len(changes), 'backup': str(backup), 'sources': list(changes)}, indent=2))
