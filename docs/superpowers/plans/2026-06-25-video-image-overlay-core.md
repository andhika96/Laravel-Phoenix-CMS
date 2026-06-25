# Video Image Overlay Core Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Expose the core `Image Overlay` flow for the `Video` widget across standard video sources while keeping the existing choose-image and click-to-play behavior intact.

**Architecture:** Reuse the already-implemented overlay settings and rendering pipeline. The minimal change is to widen the editor-side source gate in `public/js/pagebuilder_elementor/app.js`, then lock it with regression coverage in the existing `PageBuilderElementorVideoWidgetContentParityTest`.

**Tech Stack:** Laravel feature tests, Vue template strings inside `public/js/pagebuilder_elementor/app.js`, existing video preview component, Blade frontend renderer.

---

### Task 1: Lock the Missing Overlay Scope With a Failing Test

**Files:**
- Modify: `tests/Feature/PageBuilderElementorVideoWidgetContentParityTest.php`
- Verify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Write the failing test**

Add an assertion that the editor-side `videoShowsOverlay` helper supports iframe sources:

```php
public function test_editor_exposes_image_overlay_for_iframe_video_sources(): void
{
    $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

    $this->assertIsString($appJs);
    $this->assertStringContainsString(
        "return source === 'youtube' || source === 'vimeo' || source === 'dailymotion' || source === 'self_hosted' || source === 'videopress';",
        $appJs
    );
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_editor_exposes_image_overlay_for_iframe_video_sources`
Expected: FAIL because `videoShowsOverlay()` still only returns `self_hosted` and `videopress`.

### Task 2: Implement the Minimal Editor Gate Change

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`
- Test: `tests/Feature/PageBuilderElementorVideoWidgetContentParityTest.php`

- [ ] **Step 1: Write minimal implementation**

Update the helper:

```js
function videoShowsOverlay(node) {
    const source = videoCurrentSource(node);
    return source === 'youtube' || source === 'vimeo' || source === 'dailymotion' || source === 'self_hosted' || source === 'videopress';
}
```

- [ ] **Step 2: Run focused test to verify it passes**

Run: `php artisan test --filter=test_editor_exposes_image_overlay_for_iframe_video_sources`
Expected: PASS

### Task 3: Verify the Existing Video Parity Surface Still Holds

**Files:**
- Verify: `tests/Feature/PageBuilderElementorVideoWidgetContentParityTest.php`
- Verify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Run the existing video parity test file**

Run: `php artisan test --filter=PageBuilderElementorVideoWidgetContentParityTest`
Expected: PASS with `0` failures.

- [ ] **Step 2: Run syntax and diff hygiene checks**

Run: `node --check public/js/pagebuilder_elementor/app.js`
Expected: exit `0`

Run: `git diff --check -- tests/Feature/PageBuilderElementorVideoWidgetContentParityTest.php public/js/pagebuilder_elementor/app.js`
Expected: no output

- [ ] **Step 3: Confirm the builder route still responds**

Run: `powershell -Command "try { (Invoke-WebRequest -UseBasicParsing 'http://laravel-13-phoenix.aruna/pagebuilder-elementor/create' -TimeoutSec 10).StatusCode } catch { $_.Exception.Message }"`
Expected: `200`
