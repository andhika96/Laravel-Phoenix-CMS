<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\CustomJavaScriptPolicy;
use Tests\TestCase;

class PageBuilderElementorV24CustomJavaScriptPolicyTest extends TestCase
{
    public function test_it_normalizes_safe_code_and_disabled_mode(): void
    {
        $result = app(CustomJavaScriptPolicy::class)->normalize("const title = 'CEO';\r\nconsole.log(title);", 'disabled');

        $this->assertSame("const title = 'CEO';\nconsole.log(title);", $result['code']);
        $this->assertSame('disabled', $result['mode']);
        $this->assertSame([], $result['blocked']);
    }

    public function test_it_reports_browser_capabilities_without_executing_code(): void
    {
        $result = app(CustomJavaScriptPolicy::class)->normalize(
            "document.querySelector('#menu').addEventListener('click', () => fetch('/api/menu'));\nlocalStorage.setItem('open', '1');",
            'exact_sandbox',
        );

        $this->assertSame('exact_sandbox', $result['mode']);
        $this->assertContains('fetch', $result['warnings']);
        $this->assertContains('localStorage', $result['warnings']);
        $this->assertSame([], $result['blocked']);
    }

    public function test_it_blocks_wrappers_and_code_execution_primitives(): void
    {
        $result = app(CustomJavaScriptPolicy::class)->normalize(
            '<script>eval("alert(1)")</script>',
            'published',
        );

        $this->assertContains('script-wrapper', $result['blocked']);
        $this->assertContains('eval', $result['blocked']);
        $this->assertSame('disabled', $result['mode']);
        $this->assertSame('', $result['code']);
    }

    public function test_it_rejects_invalid_mode_and_oversized_payload(): void
    {
        $result = app(CustomJavaScriptPolicy::class)->normalize(str_repeat('x', 100 * 1024 + 1), 'main');

        $this->assertContains('invalid-mode', $result['blocked']);
        $this->assertContains('max-size', $result['blocked']);
        $this->assertSame('disabled', $result['mode']);
    }

    public function test_it_returns_structured_diagnostics_with_a_source_location(): void
    {
        $result = app(CustomJavaScriptPolicy::class)->normalize("const ready = true;\nlocalStorage.setItem('ready', ready);", 'exact_sandbox');

        $diagnostic = collect($result['diagnostics'])->firstWhere('key', 'localStorage');
        $this->assertIsArray($diagnostic);
        $this->assertSame('warning', $diagnostic['severity']);
        $this->assertSame(2, $diagnostic['line']);
        $this->assertGreaterThan(0, $diagnostic['column']);
    }

    public function test_it_preserves_unicode_and_accepts_the_exact_byte_limit(): void
    {
        $safe = str_repeat('x', CustomJavaScriptPolicy::MAX_BYTES - strlen('é')) . 'é';
        $result = app(CustomJavaScriptPolicy::class)->normalize($safe, 'disabled');

        $this->assertSame($safe, $result['code']);
        $this->assertSame([], $result['blocked']);
    }
}
