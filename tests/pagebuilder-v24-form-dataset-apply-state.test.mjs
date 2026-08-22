import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";

const settings = fs.readFileSync(
    new URL("../resources/pagebuilder_elementor_v24/modules/widgets/pro/form/Settings.vue", import.meta.url),
    "utf8",
);

test("Apply dataset stays in the modal and exposes a Manage Role-style notice toast", () => {
    const saveMethod = settings.match(/async saveFormDataset\([\s\S]*?\n        \},/u)?.[0] || "";
    assert.notEqual(saveMethod, "");
    assert.doesNotMatch(saveMethod, /this\.closeFormDatasetModal\(\)/);
    assert.match(settings, /responsePayload\?\.success\s*!==\s*true/);
    assert.match(settings, /pb-form-dataset-toast/);
    assert.match(settings, /toast ph-notice-toast ph-callout-no-border/);
    assert.match(settings, /toast-header-title toast-header-icon/);
    assert.match(settings, />just now</);
    assert.match(settings, /class="btn-close"/);
    assert.match(settings, /ph-callout-success/);
    assert.match(settings, /toast-container position-fixed top-0 end-0 p-3 pb-form-dataset-toast-container/);
    assert.match(settings, /Dataset (?:created|updated) successfully/);
    assert.match(settings, /clearFormDatasetNotice/);
    assert.match(settings, /\.pb-form-dataset-modal__footer \.pb-btn\.primary\s*\{[\s\S]*?gap:\s*6px;/);
});

test("confirmation buttons keep an accessible gap between the icon and label", () => {
    assert.match(settings, /pb-form-dataset-delete-modal__confirm[\s\S]*?gap:\s*6px/);
});
