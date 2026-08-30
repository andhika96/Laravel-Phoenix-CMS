<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final readonly class FrameworkBundle
{
    /**
     * @param array<int,string> $detectedFrameworks
     * @param array<int,string> $inlineStyles
     * @param array<int,string> $externalStylesheets
     * @param array<int,array<string,mixed>> $cssSources
     * @param array<int,array<string,mixed>> $assetManifest
     * @param array<int,string> $runtimeScripts
     * @param array<int,array<string,mixed>> $diagnostics
     */
    public function __construct(
        public string $html,
        public string $css,
        public string $framework,
        public array $detectedFrameworks = [],
        public array $inlineStyles = [],
        public array $externalStylesheets = [],
        public array $cssSources = [],
        public array $assetManifest = [],
        public array $runtimeScripts = [],
        public array $diagnostics = [],
    ) {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'html' => $this->html,
            'css' => $this->css,
            'framework' => $this->framework,
            'detectedFrameworks' => $this->detectedFrameworks,
            'inlineStyles' => $this->inlineStyles,
            'externalStylesheets' => $this->externalStylesheets,
            'cssSources' => $this->cssSources,
            'assetManifest' => $this->assetManifest,
            'runtimeScripts' => $this->runtimeScripts,
            'diagnostics' => $this->diagnostics,
        ];
    }
}
