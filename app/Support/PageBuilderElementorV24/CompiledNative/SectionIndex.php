<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final readonly class SectionIndex
{
    /**
     * @param array<int,array<string,mixed>> $sections
     * @param array<int,array<string,mixed>> $diagnostics
     */
    public function __construct(
        public array $sections,
        public array $diagnostics = [],
    ) {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'sections' => $this->sections,
            'diagnostics' => $this->diagnostics,
        ];
    }

    public function rename(string $sectionId, string $label): self
    {
        $sections = array_map(static function (array $section) use ($sectionId, $label): array {
            if (($section['id'] ?? '') !== $sectionId || trim($label) === '') {
                return $section;
            }

            return [...$section, 'label' => trim($label)];
        }, $this->sections);

        return new self($sections, $this->diagnostics);
    }

    public function setCompile(string $sectionId, bool $compile): self
    {
        $sections = array_map(static function (array $section) use ($sectionId, $compile): array {
            return ($section['id'] ?? '') === $sectionId ? [...$section, 'compile' => $compile] : $section;
        }, $this->sections);

        return new self($sections, $this->diagnostics);
    }
}
