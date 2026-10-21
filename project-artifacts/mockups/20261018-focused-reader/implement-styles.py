from pathlib import Path
root = Path(__file__).resolve().parents[3]
css = root / 'public/assets/css/article/article-frontend-2026.css'
fields = 'title-size title-leading description-size description-leading metadata-size metadata-gap header-gap cover-gap content-gap paragraph-gap heading-gap body-size body-leading heading-size quote-size quote-padding cover-radius navigation-gap navigation-size align justify ratio'.split()
def variables(device):
    return '\n'.join(f'    --reader-{key}: var(--reader-{key}-{device});' for key in fields)
addition = '\n/* Focused Reader: scoped typography and independent reading / cover widths. */\n.article-detail--focused-reader {\n' + variables('desktop') + '\n}\n' + '''
.article-detail--focused-reader *, .article-detail--focused-reader *::before, .article-detail--focused-reader *::after { box-sizing: border-box; }
.article-detail--focused-reader .article-detail__shell { width: min(100%, calc(max(var(--reader-cover-width), var(--reader-reading-width)) + 80px)); padding: 48px 40px; }
.article-detail--focused-reader .article-back-link { font-size: 13px; font-weight: 500; min-height: 44px; align-items: center; margin-bottom: 28px; }
.article-detail--focused-reader .article-detail__header { display: flex; flex-direction: column; gap: var(--reader-header-gap); max-width: var(--reader-reading-width); margin: 0 auto; text-align: var(--reader-align); }
.article-detail--focused-reader .article-reader-category { color: var(--article-accent); font-size: 12px; font-weight: 700; line-height: 1.5; letter-spacing: .1em; text-transform: uppercase; overflow-wrap: anywhere; }
.article-detail--focused-reader h1 { width: 100%; max-width: var(--reader-title-width); align-self: var(--reader-justify); font-size: var(--reader-title-size); line-height: var(--reader-title-leading); letter-spacing: -.035em; text-wrap: balance; overflow-wrap: anywhere; }
.article-detail--focused-reader .article-detail__dek { max-width: 100%; margin: 0; font-size: var(--reader-description-size); line-height: var(--reader-description-leading); text-wrap: pretty; overflow-wrap: anywhere; }
.article-detail--focused-reader .article-detail__meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: var(--reader-justify); gap: 8px var(--reader-metadata-gap); margin: 0; font-size: var(--reader-metadata-size); line-height: 1.6; overflow-wrap: anywhere; }
.article-detail--focused-reader .article-detail__meta > * { min-width: 0; }
.article-detail--focused-reader .article-reader-author { font-weight: 600; color: #344054; }
.article-detail--focused-reader [data-reader-meta-position="before-summary"] .article-detail__dek { order: 1; }
.article-detail--focused-reader [data-separator="dot"] > * + *::before { content: '·'; margin-right: var(--reader-metadata-gap); }
.article-detail--focused-reader [data-separator="line"] > * + *::before { content: '|'; margin-right: var(--reader-metadata-gap); }
.article-detail--focused-reader .article-reader-cover { width: 100%; max-width: var(--reader-cover-width); margin: var(--reader-cover-gap) auto 0; }
.article-detail--focused-reader[data-reader-cover-width="reading"] .article-reader-cover { max-width: var(--reader-reading-width); }
.article-detail--focused-reader .article-detail__cover { width: 100%; height: auto; aspect-ratio: var(--reader-ratio); object-fit: var(--reader-cover-fit); object-position: var(--reader-cover-position); border-radius: var(--reader-cover-radius); margin: 0; }
.article-detail--focused-reader figcaption { margin-top: 10px; font-size: 12px; line-height: 1.5; color: #667085; overflow-wrap: anywhere; }
.article-detail--focused-reader .article-rich-content { max-width: var(--reader-reading-width); min-width: 0; margin: var(--reader-content-gap) auto 0; font-size: var(--reader-body-size); line-height: var(--reader-body-leading); overflow-wrap: anywhere; }
.article-detail--focused-reader .article-rich-content > :first-child { margin-top: 0; }
.article-detail--focused-reader .article-rich-content p, .article-detail--focused-reader .article-rich-content ul, .article-detail--focused-reader .article-rich-content ol { margin-bottom: var(--reader-paragraph-gap); }
.article-detail--focused-reader .article-rich-content h2, .article-detail--focused-reader .article-rich-content h3, .article-detail--focused-reader .article-rich-content h4, .article-detail--focused-reader .article-rich-content h5, .article-detail--focused-reader .article-rich-content h6 { margin-top: var(--reader-heading-gap); margin-bottom: 16px; line-height: 1.3; letter-spacing: -.02em; }
.article-detail--focused-reader .article-rich-content h2 { font-size: var(--reader-heading-size); }
.article-detail--focused-reader .article-rich-content h3 { font-size: max(var(--reader-body-size), calc(var(--reader-heading-size) * .85)); }
.article-detail--focused-reader .article-rich-content :is(h4, h5, h6) { font-size: var(--reader-body-size); }
.article-detail--focused-reader .article-rich-content blockquote { margin: var(--reader-content-gap) 0; padding: 2px 0 2px var(--reader-quote-padding); border: 0; border-left: 3px solid var(--article-accent); border-radius: 0; background: transparent; font-size: var(--reader-quote-size); line-height: 1.55; }
.article-detail--focused-reader .article-rich-content blockquote > :last-child { margin-bottom: 0; }
.article-detail--focused-reader[data-reader-quote-accent="false"] .article-rich-content blockquote { border-left: 0; }
.article-detail--focused-reader .article-rich-content :is(img, video, iframe, svg, canvas, figure) { max-width: 100%; }
.article-detail--focused-reader .article-rich-content :is(img, video) { height: auto; }
.article-detail--focused-reader .article-rich-content iframe { width: 100%; aspect-ratio: 16 / 9; height: auto; }
.article-detail--focused-reader .article-rich-content figure { margin-inline: 0; }
.article-detail--focused-reader .article-rich-content pre { max-width: 100%; overflow-x: auto; }
.article-detail--focused-reader .article-rich-content table { display: block; width: 100%; max-width: 100%; overflow-x: auto; }
.article-detail--focused-reader .article-detail-navigation { max-width: var(--reader-reading-width); margin: var(--reader-navigation-gap) auto 0; gap: 24px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
body .article-detail--focused-reader .article-detail-navigation__item { padding: 8px 0; border: 0; border-radius: 0; background: transparent; min-height: 44px; }
.article-detail--focused-reader .article-detail-navigation__item > span { min-width: 0; }
.article-detail--focused-reader .article-detail-navigation__item strong { font-size: var(--reader-navigation-size); line-height: 1.5; overflow-wrap: anywhere; display: block; -webkit-line-clamp: unset; }
.article-detail--focused-reader .article-detail-navigation__item small { font-size: 12px; letter-spacing: .04em; }
.article-detail--focused-reader[data-reader-thumbnails="false"] .article-detail-navigation__item { grid-template-columns: minmax(0, 1fr); }
.article-detail--focused-reader[data-reader-thumbnails="false"] .article-detail-navigation__item img { display: none; }
.article-detail--focused-reader[data-reader-divider="false"] .article-detail-navigation { border-top: 0; }
'''
addition += '\n@media (max-width: 991.98px) {\n.article-detail--focused-reader {\n' + variables('tablet') + '\n}\n.article-detail--focused-reader .article-detail__shell { padding: 32px; }\n}\n'
addition += '\n@media (max-width: 575.98px) {\n.article-detail--focused-reader {\n' + variables('mobile') + '\n}\n' + '''
.article-detail--focused-reader .article-detail__shell { padding: 24px 20px 32px; }
.article-detail--focused-reader h1 { font-size: min(var(--reader-title-size), 11vw); }
.article-detail--focused-reader .article-back-link { margin-bottom: 20px; }
.article-detail--focused-reader .article-reader-author { flex-basis: 100%; }
.article-detail--focused-reader .article-reader-author + *::before { content: none; }
.article-detail--focused-reader .article-detail-navigation { grid-template-columns: minmax(0, 1fr); gap: 16px; }
.article-detail--focused-reader .article-detail-navigation > span:empty { display: none; }
.article-detail--focused-reader .article-detail-navigation__item--next { text-align: left; }
.article-detail--focused-reader .article-detail-navigation__item--next img { grid-column: 1; grid-row: 1; }
.article-detail--focused-reader .article-detail-navigation__item--next:has(img) { grid-template-columns: 52px minmax(0, 1fr); }
.article-detail--focused-reader[data-reader-thumbnails="false"] .article-detail-navigation__item--next { grid-template-columns: minmax(0, 1fr); }
.article-detail--focused-reader[data-reader-divider="true"] .article-detail-navigation__item + .article-detail-navigation__item { border-top: 1px solid #e6e9ef; padding-top: 20px; }
}
'''
with css.open('a', encoding='utf-8', newline='') as f: f.write(addition)
manager = root / 'public/assets/css/article/article-template-manager-2026.css'
with manager.open('a', encoding='utf-8', newline='') as f: f.write('''
/* Focused Reader uses the existing options form controls. */
.article-template-reader-fields { display: grid; gap: 24px; margin-top: 20px; }
.article-template-reader-fields fieldset { min-width: 0; border: 0; border-top: 1px solid var(--bs-border-color, #e5e7eb); padding: 20px 0 0; margin: 0; }
.article-template-reader-fields legend { float: none; width: auto; font-size: 14px; font-weight: 600; margin-bottom: 14px; }
.article-template-reader-fields .article-template-options-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
.article-template-reader-fields label { min-width: 0; font-size: 13px; }
.article-template-reader-fields .form-control, .article-template-reader-fields .form-select { min-width: 0; width: 100%; }
.article-template-reader-fields small { display: block; margin-top: 12px; color: var(--bs-secondary-color, #667085); }
.article-template-reader-wide { grid-column: 1 / -1; }
@media (max-width: 575.98px) { .article-template-reader-fields .article-template-options-fields { grid-template-columns: minmax(0, 1fr); } }
''')
index = root / 'resources/views/manage_article/templates/index.blade.php'
s = index.read_text(encoding='utf-8')
old = "in_array($field, ['date', 'author', 'navigation']) ? 'true' : 'false'"
assert s.count(old) == 1
s = s.replace(old, "in_array($field, ['date', 'author', 'navigation']) ? 'true' : ($field === 'reading_time' ? \"optionsModal.key === 'focused-reader'\" : 'false')")
index.write_text(s, encoding='utf-8', newline='')
