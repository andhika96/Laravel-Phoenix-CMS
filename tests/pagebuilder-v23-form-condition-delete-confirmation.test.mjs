import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";

const settings = fs.readFileSync(
    new URL("../public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue", import.meta.url),
    "utf8",
);

test("condition delete buttons open a confirmation modal before removing a rule", () => {
    assert.match(settings, /openConditionDeleteConfirmation\(index\)/);
    assert.match(settings, /conditionDeleteConfirmationOpen/);
    assert.match(settings, /Delete condition\?/);
    assert.match(settings, /confirmConditionDelete/);
    assert.doesNotMatch(settings, /pb-form-dataset-condition-remove[^\n]*@click="removeConditionRule\(index\)"/);
});
