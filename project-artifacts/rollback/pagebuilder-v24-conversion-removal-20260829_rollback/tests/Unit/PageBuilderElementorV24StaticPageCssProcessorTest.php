<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\StaticImport\StaticPageCssProcessor;
use DOMDocument;
use Tests\TestCase;

class PageBuilderElementorV24StaticPageCssProcessorTest extends TestCase
{
    public function test_it_scopes_safe_css_and_rejects_unsafe_css_or_stylesheets(): void
    {
        $html = <<<'HTML'
<!doctype html><html><head>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
<link rel="stylesheet" href="https://evil.example.com/site.css">
<script>tailwind.config={theme:{extend:{colors:{ink:"#061426",cream:"#f4eddf",goldSoft:"#a17b3d"},fontFamily:{display:["Cormorant Garamond","serif"]},boxShadow:{gold:"0 18px 50px rgba(215,178,106,.14)"}}}};</script>
<style>
@import url("https://evil.example.com/import.css");
:root { --ink: #061426; }
body { background: var(--ink); }
.card:hover { color: var(--ink); background-image: url("javascript:alert(1)"); }
#hero > .title, ::selection { color: #fff; }
@media (max-width: 767px) { .card { display: block; } }
</style>
</head><body><div id="hero" class="card bg-ink/95 shadow-gold"><h1 class="title text-cream">Hero</h1></div></body></html>
HTML;

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"?>'.$html, LIBXML_NONET | LIBXML_HTML_NODEFDTD | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $result = app(StaticPageCssProcessor::class)->process($dom, $html);

        $this->assertSame(
            ['https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap'],
            $result['stylesheets'],
        );
        $this->assertStringContainsString('.pb-import-root{--ink:#061426}', $result['css']);
        $this->assertStringContainsString('.pb-import-root .card:hover{color:var(--ink)}', $result['css']);
        $this->assertStringContainsString('.pb-import-root :is(#hero,[data-css-id="hero"]) > .title', $result['css']);
        $this->assertStringContainsString('@media (max-width: 767px){.pb-import-root .card{display:block}}', $result['css']);
        $this->assertStringNotContainsString('@import', $result['css']);
        $this->assertStringNotContainsString('javascript:', $result['css']);
        $this->assertGreaterThanOrEqual(3, $result['dropped']);
        $this->assertSame('#061426', $result['tailwindConfig']['theme']['extend']['colors']['ink']);
        $this->assertSame('#a17b3d', $result['tailwindConfig']['theme']['extend']['colors']['goldSoft']);
        $this->assertSame(['Cormorant Garamond', 'serif'], $result['tailwindConfig']['theme']['extend']['fontFamily']['display']);
        $this->assertSame('0 18px 50px rgba(215,178,106,.14)', $result['tailwindConfig']['theme']['extend']['boxShadow']['gold']);
        $this->assertStringContainsString('.pb-import-root .text-cream{color:#f4eddf!important}', $result['css']);
        $this->assertStringContainsString('.pb-import-root .bg-ink\/95{background-color:rgba(6,20,38,0.95)!important}', $result['css']);
        $this->assertStringContainsString('.pb-import-root .shadow-gold{box-shadow:0 18px 50px rgba(215,178,106,.14)!important}', $result['css']);
    }
}
