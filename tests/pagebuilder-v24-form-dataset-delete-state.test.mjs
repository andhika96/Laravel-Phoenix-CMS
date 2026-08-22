import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";

const settings = fs.readFileSync(
    new URL("../resources/pagebuilder_elementor_v24/modules/widgets/pro/form/Settings.vue", import.meta.url),
    "utf8",
);

test("dataset delete requires a successful JSON payload before changing editor state", () => {
    assert.match(settings, /payload\??\.success\s*!==\s*true/);
    assert.match(settings, /loadFormDatasets\(true\)/);
    assert.match(settings, /formDatasetNotice/);

    const handler = settings.match(/handleDeletedDataset\([\s\S]*?\n        \},/u)?.[0] || "";
    assert.notEqual(handler, "");
    assert.doesNotMatch(handler, /closeFormDatasetModal\(\)/);
});
