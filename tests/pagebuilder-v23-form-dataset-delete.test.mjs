import test from "node:test";
import assert from "node:assert/strict";
import fs from "node:fs";

const settings = fs.readFileSync(
    new URL("../public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue", import.meta.url),
    "utf8",
);
const controller = fs.readFileSync(
    new URL("../app/Http/Controllers/Web/PageBuilderElementorV23/FormDatasetController.php", import.meta.url),
    "utf8",
);
const routes = fs.readFileSync(
    new URL("../routes/experimentalFeaturesWebv2.php", import.meta.url),
    "utf8",
);

test("dataset picker owns the delete action and confirmation state", () => {
    assert.match(settings, /pb-form-dataset-picker/);
    assert.match(settings, /Delete selected dataset/);
    assert.match(settings, /deleteConfirmationOpen/);
    assert.match(settings, /Delete dataset\?/);
});

test("dataset deletion uses the owner-scoped v2.3 destroy endpoint", () => {
    assert.match(settings, /endpoints\?\.destroy/);
    assert.match(settings, /method:\s*["']DELETE["']/);
    assert.match(controller, /public function destroy\(/);
    assert.match(routes, /Route::delete\('\/{datasetId}'/);
});
