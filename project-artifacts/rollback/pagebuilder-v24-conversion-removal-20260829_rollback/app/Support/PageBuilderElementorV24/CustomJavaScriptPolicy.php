<?php

namespace App\Support\PageBuilderElementorV24;

final class CustomJavaScriptPolicy
{
    public const MAX_BYTES = 102400;

    /** @var array<int, string> */
    public const MODES = ['disabled', 'exact_sandbox', 'published'];

    /**
     * @return array{code:string,mode:string,warnings:array<int,string>,blocked:array<int,string>,diagnostics:array<int,array{severity:string,key:string,message:string,line:int,column:int}>}
     */
    public function normalize(mixed $code, mixed $mode = 'disabled'): array
    {
        $source = is_string($code) ? str_replace(["\r\n", "\r"], "\n", $code) : '';
        $source = trim($source);
        $normalizedMode = is_string($mode) && in_array($mode, self::MODES, true) ? $mode : 'disabled';
        $warnings = [];
        $blocked = [];
        $diagnostics = [];

        $addDiagnostic = function (string $severity, string $key, string $message, ?int $offset = null) use (&$diagnostics, $source): void {
            if (collect($diagnostics)->contains(fn (array $item): bool => $item['key'] === $key)) return;
            $safeOffset = max(0, min(strlen($source), $offset ?? 0));
            $before = substr($source, 0, $safeOffset);
            $line = substr_count($before, "\n") + 1;
            $lastBreak = strrpos($before, "\n");
            $column = $safeOffset - ($lastBreak === false ? 0 : $lastBreak + 1) + 1;
            $diagnostics[] = [
                'severity' => $severity,
                'key' => $key,
                'message' => $message,
                'line' => $line,
                'column' => $column,
            ];
        };

        if (! is_string($mode) || ! in_array($mode, self::MODES, true)) {
            $blocked[] = 'invalid-mode';
            $addDiagnostic('blocked', 'invalid-mode', 'Execution mode is not allowed.');
        }
        if ($source === '') {
            return [
                'code' => '',
                'mode' => 'disabled',
                'warnings' => [],
                'blocked' => array_values(array_unique($blocked)),
                'diagnostics' => $diagnostics,
            ];
        }

        $blockedPatterns = [
            ['key' => 'script-wrapper', 'pattern' => '/<\s*\/?\s*script\b/i', 'message' => 'Script wrappers are not accepted.'],
            ['key' => 'external-script', 'pattern' => '/<\s*script\b[^>]*\bsrc\s*=|\bsrc\s*=\s*["\']https?:/i', 'message' => 'External script tags are not accepted.'],
            ['key' => 'javascript-url', 'pattern' => '/\bjavascript\s*:/i', 'message' => 'JavaScript URLs are not accepted.'],
            ['key' => 'eval', 'pattern' => '/\beval\s*\(/i', 'message' => 'Dynamic code evaluation is blocked.'],
            ['key' => 'new-function', 'pattern' => '/\bnew\s+Function\s*\(/i', 'message' => 'Dynamic function constructors are blocked.'],
        ];
        if (strlen($source) > self::MAX_BYTES) {
            $blocked[] = 'max-size';
            $addDiagnostic('blocked', 'max-size', 'Code exceeds the 100 KB limit.');
        }
        if (str_contains($source, "\0")) {
            $blocked[] = 'null-byte';
            $addDiagnostic('blocked', 'null-byte', 'Null bytes are not accepted.', strpos($source, "\0") ?: 0);
        }
        if (preg_match('/[\x01-\x08\x0B\x0C\x0E-\x1F\x7F]/', $source, $controlMatch, PREG_OFFSET_CAPTURE)) {
            $blocked[] = 'control-byte';
            $addDiagnostic('blocked', 'control-byte', 'Unsupported control bytes are not accepted.', (int) ($controlMatch[0][1] ?? 0));
        }
        foreach ($blockedPatterns as $item) {
            if (! preg_match($item['pattern'], $source, $match, PREG_OFFSET_CAPTURE)) continue;
            $blocked[] = $item['key'];
            $addDiagnostic('blocked', $item['key'], $item['message'], (int) ($match[0][1] ?? 0));
        }

        $warningPatterns = [
            ['key' => 'document.cookie', 'pattern' => '/\bdocument\s*\.\s*cookie\b/i', 'message' => 'Code reads browser cookies.'],
            ['key' => 'localStorage', 'pattern' => '/\blocalStorage\b/i', 'message' => 'Code accesses localStorage.'],
            ['key' => 'sessionStorage', 'pattern' => '/\bsessionStorage\b/i', 'message' => 'Code accesses sessionStorage.'],
            ['key' => 'fetch', 'pattern' => '/\bfetch\s*\(/i', 'message' => 'Code can issue browser requests with fetch.'],
            ['key' => 'XMLHttpRequest', 'pattern' => '/\bXMLHttpRequest\b/i', 'message' => 'Code can create XMLHttpRequest calls.'],
            ['key' => 'WebSocket', 'pattern' => '/\bWebSocket\s*\(/i', 'message' => 'Code can open a WebSocket.'],
            ['key' => 'window.open', 'pattern' => '/\bwindow\s*\.\s*open\s*\(/i', 'message' => 'Code can try to open a new window.'],
            ['key' => 'form submission', 'pattern' => '/\b(?:form|document)\s*\.\s*submit\s*\(|\bsubmit\b/i', 'message' => 'Code can submit a browser form.'],
            ['key' => 'timers', 'pattern' => '/\b(?:setTimeout|setInterval|requestAnimationFrame)\s*\(/i', 'message' => 'Code uses timers or animation frames.'],
            ['key' => 'DOM mutation', 'pattern' => '/\b(?:appendChild|insertBefore|removeChild|innerHTML|outerHTML|classList)\b/i', 'message' => 'Code can mutate the page DOM.'],
            ['key' => 'cross-origin URL', 'pattern' => '/https?:\/\//i', 'message' => 'Code contains an absolute URL that may be cross-origin.'],
        ];
        foreach ($warningPatterns as $item) {
            if (! preg_match($item['pattern'], $source, $match, PREG_OFFSET_CAPTURE)) continue;
            $warnings[] = $item['key'];
            $addDiagnostic('warning', $item['key'], $item['message'], (int) ($match[0][1] ?? 0));
        }

        $blocked = array_values(array_unique($blocked));
        if ($blocked !== []) {
            return [
                'code' => '',
                'mode' => 'disabled',
                'warnings' => array_values(array_unique($warnings)),
                'blocked' => $blocked,
                'diagnostics' => $diagnostics,
            ];
        }

        return [
            'code' => $source,
            'mode' => $normalizedMode,
            'warnings' => array_values(array_unique($warnings)),
            'blocked' => [],
            'diagnostics' => $diagnostics,
        ];
    }
}
