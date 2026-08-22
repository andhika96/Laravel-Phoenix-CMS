import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";

const settings = fs.readFileSync(
    new URL("../resources/pagebuilder_elementor_v24/modules/widgets/pro/form/Settings.vue", import.meta.url),
    "utf8",
);

test("condition deletion explains that Apply dataset is still required", () => {
    assert.match(settings, /conditionDeleteNotice/);
    assert.match(settings, /Condition removed\. Click Apply dataset to save\./);
    assert.match(settings, /serverError \|\| notice \|\| conditionDeleteNotice/);
});
