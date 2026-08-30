# Page Builder v2.4 conversion rollback

- Date: 2026-08-29
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Conversion feature commit: `91ebf822275b2635d74edd9d75a03ab54f51cc42`
- Pre-conversion parent: `a7a35e968f57bc708812226f079d0dce41754d1f`
- Scope: remove the active Exact Visual, Static HTML, Editable Native import, Guided Mapping, and Compiled Native conversion workflow.

## Safety boundary

The repository was not reset globally. Existing Page Builder v2.4 editor work, Custom JavaScript, widgets, layout modules, Grid/Flex/Row Grid behavior, persistence, and the main responsive engine were preserved. No v2.3 source was changed. Existing database/page data was not deleted.

The Git history still contains the original conversion commit for auditability. The working source/runtime is the rollback state; integration, staging, and commit remain separate actions.

## Removed from active runtime

- `import/static` route and controller endpoint.
- Static import request/service/analyzer/CSS processor/compatibility/validator source.
- Import helper assets under `public/js/pagebuilder_elementor_v24/`.
- Static HTML widget module and its active manifest/assets.
- Import UI controls, Guided Mapping modal, Compile modal, report modal, and generated CSS adapter.
- Conversion-only marker attributes and source-class preservation in shared resolver, layout modules, basic widgets, and Form.
- Conversion-only feature tests and module-count expectations.
- Custom JavaScript `exact_sandbox` mode, because it depended on the removed Exact Visual iframe; `disabled` and `published` modes remain.

## Rollback backup

The pre-removal active files and the generated route cache are preserved at this directory. The backup payload contains 86 files totaling 2,363,313 bytes; the manifest files are metadata extras. Key SHA-256 values:

```text
app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php  0B952C59D849BF5F03A26FCF914D48CB2BC9BC9097F46FDAA8370EA54BD3FFD2
app/Support/PageBuilderElementorV24/WidgetAdvancedStyleResolver.php                    D248BDFB4ABD00FEA1B75F85A792EC66D41A85B4171E4B2D75B872187A796AEE
public/assets/css/pagebuilder_elementor_v24.css                                        43106A6DE0670E7AB2BC0F239C849DAB406CA57F3F3B5208001AC79141C38009
public/js/pagebuilder_elementor_v24/app.js                                             28F2A0CD5E69344E216667D80BA3E477E427852EFEB87CC96CECCED4AFAE0D8D
resources/views/pagebuilder_elementor_v24/editor_shell.blade.php                      9613BEB14FCE270193F65D32523F0A29E4509E76A314DC2B5FC088FACA2CC475
resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php                  674F085760EA88EE4E1DF4F15DD3552D71A55A7102F46527A9B2D1D7B6AD2820
routes/pagebuilder_elementor_v24.php                                                    2A8954E570FC793837F24D5302DCFB790803E5684D1BED8F388DA775D4CD5051
```

## Verification completed

- Node v2.4 regression suite: 57 passed, 0 failed.
- PHP v2.4 regression suite: 38 passed, 8,590 assertions, 0 failed.
- PHP syntax checks: controller, Add/Edit requests, CustomJavaScriptPolicy, and shared Advanced resolver passed.
- Active module catalog: 50 modules; `static-html` is no longer active.
- Active-source conversion reference scan: no route/service/UI/helper/marker/Exact Visual/Compiled Native references found outside retained backups and historical artifacts.
- Generated route cache was cleared and rechecked; the loaded v2.4 route set contains no static import route.
- Graphify was force re-extracted after excluding the rollback archive; stale conversion nodes are no longer present.

## Retained history

Timestamped `.bak` files and prior QA/plan artifacts remain untouched for recovery and audit. They are excluded from the active-source scan and are not loaded by the runtime/module catalog.
