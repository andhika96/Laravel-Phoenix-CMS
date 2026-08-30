(function (root) {
	'use strict';

	const START_MARKER = '/* PHOENIX_STATIC_IMPORT_COMPILED_START */';
	const END_MARKER = '/* PHOENIX_STATIC_IMPORT_COMPILED_END */';
	const MAX_BYTES = 512 * 1024;

	function byteLength(value) {
		const source = String(value || '');
		if (typeof TextEncoder === 'function') return new TextEncoder().encode(source).length;
		if (typeof Blob === 'function') return new Blob([source]).size;
		return source.length;
	}

	function normalizeLineEndings(value) {
		return String(value || '').replace(/\r\n?/g, '\n');
	}

	function findGeneratedBlocks(source) {
		const blocks = [];
		const text = String(source || '');
		let cursor = 0;
		let malformed = false;
		while (cursor < text.length) {
			const start = text.indexOf(START_MARKER, cursor);
			const strayEnd = text.indexOf(END_MARKER, cursor);
			if (start < 0) {
				if (strayEnd >= 0) malformed = true;
				break;
			}
			if (strayEnd >= 0 && strayEnd < start) malformed = true;
			const end = text.indexOf(END_MARKER, start + START_MARKER.length);
			if (end < 0) {
				malformed = true;
				break;
			}
			const blockText = text.slice(start, end + END_MARKER.length);
			const hashMatch = blockText.match(/sourceHash:\s*([A-Za-z0-9_.:-]*)/i);
			blocks.push({ start, end: end + END_MARKER.length, sourceHash: hashMatch ? hashMatch[1] : '' });
			cursor = end + END_MARKER.length;
		}
		return { blocks, malformed };
	}

	function removeGeneratedBlocks(source) {
		const text = String(source || '');
		const found = findGeneratedBlocks(text);
		if (found.malformed) {
			return { css: text, blocks: [], replacedBlocks: 0, warnings: ['malformed-generated-block'] };
		}
		if (!found.blocks.length) return { css: text, blocks: [], replacedBlocks: 0, warnings: [] };
		let output = '';
		let cursor = 0;
		found.blocks.forEach((block) => {
			output += text.slice(cursor, block.start);
			cursor = block.end;
		});
		output += text.slice(cursor);
		const warnings = found.blocks.length > 1 ? ['duplicate-generated-block'] : [];
		return { css: output, blocks: found.blocks, replacedBlocks: found.blocks.length, warnings };
	}

	function hasBalancedBraces(source) {
		const text = String(source || '');
		let depth = 0;
		let quote = '';
		let escaped = false;
		for (let index = 0; index < text.length; index += 1) {
			const character = text[index];
			if (escaped) {
				escaped = false;
				continue;
			}
			if (character === '\\') {
				escaped = true;
				continue;
			}
			if (quote) {
				if (character === quote) quote = '';
				continue;
			}
			if (character === '"' || character === "'") {
				quote = character;
				continue;
			}
			if (character === '{') depth += 1;
			if (character === '}') {
				depth -= 1;
				if (depth < 0) return false;
			}
		}
		return depth === 0 && quote === '';
	}

	function safeMetadata(value) {
		return String(value || '').replace(/[^A-Za-z0-9_.:-]/g, '').slice(0, 160);
	}

	function generatedBlock(generatedCss, metadata) {
		const meta = metadata && typeof metadata === 'object' ? metadata : {};
		const stats = meta.stats && typeof meta.stats === 'object' ? meta.stats : meta;
		const lines = [
			START_MARKER,
			'/* generatedBy: browser-utility-compiler */',
			'/* sourceHash: ' + safeMetadata(meta.sourceHash) + ' */',
			'/* generatedRules: ' + safeMetadata(stats.generatedRules) + ' */',
			normalizeLineEndings(generatedCss).trim(),
			END_MARKER,
		];
		return lines.filter((line) => line !== '').join('\n');
	}

	function replaceGeneratedStaticImportCss(existingCss, generatedCss, metadata) {
		const existing = String(existingCss || '');
		const generated = normalizeLineEndings(generatedCss).trim();
		const removed = removeGeneratedBlocks(existing);
		const warnings = removed.warnings.slice();
		const expectedHash = safeMetadata(metadata && metadata.sourceHash);
		if (expectedHash && removed.blocks.some((block) => block.sourceHash && block.sourceHash !== expectedHash)) warnings.push('generated-block-source-mismatch');
		if (!generated) {
			return { valid: true, css: removed.css, warnings, replacedBlocks: removed.replacedBlocks, generated: false, stats: { generatedRules: 0 } };
		}
		if (byteLength(generated) > MAX_BYTES) {
			warnings.push('generated-css-too-large');
			return { valid: false, css: existingCss, warnings, replacedBlocks: 0, generated: false, stats: { generatedRules: 0 } };
		}
		if (/tailwind|bootstrap|cdn\.tailwindcss|--tw-/i.test(generated)) {
			warnings.push('forbidden-framework-marker');
			return { valid: false, css: existingCss, warnings, replacedBlocks: 0, generated: false, stats: { generatedRules: 0 } };
		}
		if (!hasBalancedBraces(generated)) {
			warnings.push('unbalanced-generated-css');
			return { valid: false, css: existingCss, warnings, replacedBlocks: 0, generated: false, stats: { generatedRules: 0 } };
		}

		const block = generatedBlock(generated, metadata);
		const outside = removed.css;
		const css = outside === '' ? block : (outside.endsWith('\n') ? outside + block : outside + '\n' + block);
		return {
			valid: true,
			css,
			warnings,
			replacedBlocks: removed.replacedBlocks,
			generated: true,
			stats: metadata && metadata.stats && typeof metadata.stats === 'object' ? metadata.stats : { generatedRules: 0 },
		};
	}

	root.PhoenixStaticImportCss = {
		START_MARKER,
		END_MARKER,
		MAX_BYTES,
		replaceGeneratedStaticImportCss,
		__test: { byteLength, findGeneratedBlocks, hasBalancedBraces, removeGeneratedBlocks },
	};
})(typeof window !== 'undefined' ? window : globalThis);
