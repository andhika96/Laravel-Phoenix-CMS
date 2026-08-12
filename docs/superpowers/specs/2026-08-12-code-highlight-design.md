# Code Highlight - Design Spec

## Scope

Add `Code Highlight` as a Pro widget in the isolated Page Builder v2.3 track. The widget must keep editor state, canvas preview, persisted settings, Blade output, and frontend runtime behavior aligned.

## Settings mapping

### Content

Section `Code`:

- `Language`: Plain Text, HTML, CSS, JavaScript, TypeScript, JSON, PHP, Python, Bash, SQL, Java, C#, C++, C, Go, Rust, Ruby, YAML, Markdown, XML, JSX, TSX, Vue, Sass, and SCSS.
- `Code`: multiline code input.
- `Line Numbers`: show or hide line numbers.
- `Copy to Clipboard`: show or hide the copy button.
- `Highlight Lines`: comma-separated line numbers/ranges such as `2-7, 10, 13-15`.
- `Word Wrap`: wrap long lines inside the viewport.
- `Theme`: Light or Dark.
- `Height`: responsive size control using `px`, `em`, `rem`, or `vh`.
- `Font Size`: responsive size control using `px`, `pt`, `em`, or `rem`.

### Style

Section `Code`:

- `Text Color` and `Background Color`.
- `Typography` for code text, including responsive font family, size, weight, line height, letter spacing, word spacing, transform, style, and decoration.
- `Padding` with four-side linked values.
- `Border Radius` with four-side linked values.

Section `Line Numbers`:

- `Text Color`.
- `Background Color`.
- `Gutter Width`.
- `Highlight Color` for selected lines.
- `Highlight Border Color` for the selected-line marker.

Section `Copy Button`:

- `Text Color`, `Background Color`.
- `Text Color (Hover)`, `Background Color (Hover)`.
- `Typography`.
- `Padding` with four-side linked values.
- `Border Radius`.

### Advanced

Use the shared v2.3 Advanced controls for layout, positioning, responsive visibility, motion effects, transform, background, border, mask, attributes, CSS classes/ID, custom CSS, display conditions, cache, and related widget-level controls. `code_highlight` must be included in the same Advanced capability gate as the other Pro widgets.

## Rendering and interaction

- Render the code as escaped line fragments; user code must never become executable markup.
- Apply lightweight dependency-free token coloring for common language families. Unknown languages remain safely escaped and readable.
- Preserve line order and empty lines.
- Highlight the line ranges parsed from the Content setting.
- Keep the copy source in a non-visible form so the frontend runtime can copy the exact original text without reading colored markup.
- Use `navigator.clipboard.writeText()` with a textarea/`execCommand` fallback and expose an accessible status message.
- Canvas and frontend use the same setting names, CSS variables, line-range grammar, and visual state classes.

## Acceptance criteria

1. The widget appears in the Pro toolbox and uses the shared Pro Settings and Canvas components.
2. All Content, Style, and Advanced controls above are visible in the correct editor tab and persist through the existing v2.3 save payload.
3. Canvas preview reflects language, theme, line numbers, highlighted lines, word wrap, responsive height/font size, typography, colors, spacing, and copy-button settings.
4. Blade output is escaped, has matching visual settings, and contains the copy interaction contract.
5. Frontend runtime initializes copy behavior once, supports keyboard activation, and reports success/failure accessibly.
6. Focused Node and PHP regression tests cover registration, normalization, settings mapping, canvas output, safe escaping, Blade output, runtime wiring, and Advanced capability.
