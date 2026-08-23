(function (core) {
	'use strict';
	if (!core) return;

	const boundRoots = new WeakSet();
	const boundCarouselRoots = new WeakSet();
	const boundBasicGalleryRoots = new WeakSet();
	const boundBasicImageRoots = new WeakSet();
	const boundTabsRoots = new WeakSet();
	const boundProductColorSelectorRoots = new WeakSet();
	const heroSliderScriptPromises = new Map();
	const transitionState = new WeakMap();
	const prefersReducedMotion = () => core.prefersReducedMotion();
	const openMediaLightbox = (mediaSource, mediaType, alt, settings = {}) => core.openMediaLightbox(
		mediaSource,
		mediaType,
		alt,
		{ className: 'pb-pro-media-lightbox', ...settings },
	);
	const productLevelDefaults = [
		{ key: 'model', fieldId: 'product_model', queryKey: 'model', required: true, defaultNodeId: '' },
		{ key: 'type', fieldId: 'product_type', queryKey: 'type', required: true, defaultNodeId: '' },
		{ key: 'variant', fieldId: 'product_variant', queryKey: 'variant', required: true, defaultNodeId: '' },
	];
	function productLevels(settings) {
		const source = Array.isArray(settings?.productLevels) ? settings.productLevels : [];
		const count = Math.max(1, Math.min(3, Number(settings?.productLevelCount) || 3));
		return productLevelDefaults.slice(0, count).map(function (fallback, index) {
			const raw = source[index] && typeof source[index] === 'object' ? source[index] : {};
			return {
				...fallback,
				...raw,
				key: fallback.key,
				fieldId: String(raw.fieldId || fallback.fieldId),
				queryKey: String(raw.queryKey || fallback.queryKey),
				required: raw.required !== false,
				defaultNodeId: String(raw.defaultNodeId || ''),
			};
		});
	}
	function productRecords(nodes, levelCount) {
		const active = (Array.isArray(nodes) ? nodes : []).filter(function (node) { return node && typeof node === 'object' && node.active !== false; });
		const byId = new Map(active.map(function (node) { return [String(node.id || ''), node]; }));
		const memo = new Map();
		function depth(node, trail = new Set()) {
			const id = String(node?.id || '');
			if (!id || trail.has(id)) return -1;
			if (memo.has(id)) return memo.get(id);
			const parentId = node?.parentId == null ? '' : String(node.parentId);
			if (!parentId) { memo.set(id, 0); return 0; }
			if (!byId.has(parentId)) { memo.set(id, -1); return -1; }
			const nextTrail = new Set(trail); nextTrail.add(id);
			const parentDepth = depth(byId.get(parentId), nextTrail);
			const value = parentDepth < 0 ? -1 : parentDepth + 1;
			memo.set(id, value); return value;
		}
		return active.map(function (node, index) {
			return { node, id: String(node.id || ''), parentId: node.parentId == null || String(node.parentId) === '' ? null : String(node.parentId), depth: depth(node), index };
		}).filter(function (record) { return record.id && record.depth >= 0 && record.depth < levelCount; })
			.sort(function (left, right) { return (Number(left.node.sortOrder) || 0) - (Number(right.node.sortOrder) || 0) || left.index - right.index; });
	}
	function resolveProductSelection(nodes, settings = {}, queryValues = {}) {
		const levels = productLevels(settings);
		const records = productRecords(nodes, levels.length);
		const byId = new Map(records.map(function (record) { return [record.id, record]; }));
		const selected = Array(levels.length).fill(null);
		const invalidQueryKeys = [];
		function chain(record) {
			const result = Array(levels.length).fill(null);
			let current = record;
			while (current && current.depth >= 0) {
				result[current.depth] = current;
				current = current.parentId ? byId.get(current.parentId) || null : null;
			}
			return result;
		}
		for (let index = levels.length - 1; index >= 0; index -= 1) {
			const requested = String(queryValues?.[levels[index].queryKey] || '').trim().toLowerCase();
			if (!requested) continue;
			const matches = records.filter(function (record) { return record.depth === index && String(record.node.code || '').trim().toLowerCase() === requested; });
			if (matches.length === 1) chain(matches[0]).forEach(function (record, levelIndex) { if (record) selected[levelIndex] = record; });
			else invalidQueryKeys.push(levels[index].queryKey);
			break;
		}
		levels.forEach(function (level, index) {
			const parentId = index === 0 ? null : selected[index - 1]?.id || null;
			const eligible = records.filter(function (record) { return record.depth === index && record.parentId === parentId; });
			const requested = String(queryValues?.[level.queryKey] || '').trim().toLowerCase();
			if (requested) {
				const matches = eligible.filter(function (record) { return String(record.node.code || '').trim().toLowerCase() === requested; });
				if (matches.length === 1) selected[index] = matches[0];
				else if (!invalidQueryKeys.includes(level.queryKey)) invalidQueryKeys.push(level.queryKey);
			}
			if (!selected[index] || !eligible.some(function (record) { return record.id === selected[index].id; })) {
				selected[index] = eligible.find(function (record) { return record.id === level.defaultNodeId; }) || eligible[0] || null;
			}
		});
		const compact = selected.filter(Boolean);
		return {
			nodes: compact.map(function (record) { return record.node; }),
			ids: compact.map(function (record) { return record.id; }),
			codes: compact.map(function (record) { return String(record.node.code || ''); }),
			values: compact.map(function (record) { return String(record.node.value || ''); }),
			invalidQueryKeys,
		};
	}
	function updateProductQuery(selection, settings = {}) {
		const url = new URL(window.location.href);
		productLevels(settings).forEach(function (level, index) {
			const code = String(selection?.codes?.[index] || '');
			if (code) url.searchParams.set(level.queryKey, code);
			else url.searchParams.delete(level.queryKey);
		});
		window.history.replaceState(null, '', url.toString());
		return url.toString();
	}
	function inheritedProductMeta(nodes) {
		const result = {};
		(Array.isArray(nodes) ? nodes : []).forEach(function (node) {
			const meta = node?.meta && typeof node.meta === 'object' ? node.meta : {};
			Object.entries(meta).forEach(function ([key, value]) { if (value !== '' && value !== null && value !== undefined) result[key] = value; });
		});
		return result;
	}
	function safeProductUrl(value) {
		const raw = String(value || '').trim();
		if (!raw || raw.startsWith('//') || /[\u0000-\u001f\u007f]/.test(raw)) return '';
		return /^(?:https?:\/\/|\/|#)/i.test(raw) ? raw : '';
	}
	function parseProductLeadConfig(root) {
		try { return JSON.parse(root.getAttribute('data-product-lead-config') || '{}'); }
		catch (_) { return {}; }
	}
	function locationQuery() {
		const params = new URL(window.location.href).searchParams;
		const values = {};
		params.forEach(function (value, key) { values[key] = value; });
		return values;
	}
	function initProductSelector(shell, form) {
		const config = parseProductLeadConfig(shell);
		const nodes = Array.isArray(config.nodes) ? config.nodes : [];
		const settings = config.settings && typeof config.settings === 'object' ? config.settings : {};
		const levels = productLevels(settings);
		const records = productRecords(nodes, levels.length);
		const recordsById = new Map(records.map(function (record) { return [record.id, record]; }));
		const options = Array.from(shell.querySelectorAll('[data-product-option]'));
		const selects = Array.from(shell.querySelectorAll('[data-product-select]'));
		const mainImage = shell.querySelector('[data-product-main-image]');
		const mediaEmpty = shell.querySelector('[data-product-media-empty]');
		const title = shell.querySelector('[data-product-title]');
		const description = shell.querySelector('[data-product-description]');
		const detailLink = shell.querySelector('[data-product-detail-link]');
		const submit = form.querySelector('button[type="submit"]');
		let selection = resolveProductSelection(nodes, settings, locationQuery());

		function render(nextSelection, writeUrl) {
			selection = nextSelection;
			levels.forEach(function (level, levelIndex) {
				const parentId = levelIndex === 0 ? null : String(selection.ids[levelIndex - 1] || '');
				options.filter(function (option) { return Number(option.getAttribute('data-product-option-level')) === levelIndex; }).forEach(function (option) {
					const optionParent = String(option.getAttribute('data-product-parent-id') || '');
					const eligible = levelIndex === 0 ? optionParent === '' : optionParent === parentId;
					const selected = eligible && String(option.getAttribute('data-product-node-id') || '') === String(selection.ids[levelIndex] || '');
					option.hidden = !eligible;
					option.classList?.toggle?.('is-selected', selected);
					option.setAttribute('aria-checked', selected ? 'true' : 'false');
					const input = option.querySelector('input[type="radio"]');
					if (input) input.checked = selected;
				});
				selects.filter(function (select) { return Number(select.getAttribute('data-product-level-index')) === levelIndex; }).forEach(function (select) {
					Array.from(select.options || []).forEach(function (option) {
						const nodeId = String(option.getAttribute?.('data-product-node-id') || '');
						if (!nodeId) return;
						const record = recordsById.get(nodeId);
						const eligible = Boolean(record) && (levelIndex === 0 ? record.parentId === null : record.parentId === parentId);
						option.hidden = !eligible;
						option.disabled = !eligible;
					});
					select.value = String(selection.ids[levelIndex] || '');
				});
				const hidden = form.querySelector(`[data-product-value-index="${levelIndex}"]`);
				if (hidden) hidden.value = String(selection.values[levelIndex] || '');
			});

			const activeNode = selection.nodes.at(-1) || null;
			const meta = inheritedProductMeta(selection.nodes);
			const imageUrl = safeProductUrl(meta.imageUrl || meta.thumbnailUrl);
			if (mainImage) {
				mainImage.hidden = !imageUrl;
				if (imageUrl) mainImage.setAttribute('src', imageUrl);
				mainImage.setAttribute('alt', String(meta.imageAlt || meta.thumbnailAlt || activeNode?.label || 'Product image'));
			}
			if (mediaEmpty) mediaEmpty.hidden = Boolean(imageUrl);
			if (title) { title.textContent = String(activeNode?.label || ''); title.hidden = !activeNode; }
			if (description) { description.textContent = String(meta.description || ''); description.hidden = !meta.description; }
			if (detailLink) {
				const href = safeProductUrl(meta.detailUrl);
				detailLink.hidden = !href;
				if (href) detailLink.setAttribute('href', href);
				detailLink.textContent = String(meta.detailLabel || 'Learn More');
			}
			const invalid = levels.some(function (level, index) { return level.required && !selection.nodes[index]; });
			if (submit) { submit.dataset.productInvalid = invalid ? '1' : '0'; submit.disabled = invalid; }
			shell.setAttribute('data-product-selection', selection.ids.join(','));
			if (writeUrl && settings.syncProductQuery !== false) {
				updateProductQuery(selection, settings);
				window.dispatchEvent?.(new CustomEvent('pagebuilder:product-query-change', { detail: { source: shell } }));
			}
		}

		function choose(levelIndex, nodeId) {
			const query = {};
			selection.nodes.slice(0, levelIndex).forEach(function (node, index) { query[levels[index].queryKey] = String(node.code || ''); });
			const record = recordsById.get(String(nodeId || ''));
			if (record) query[levels[levelIndex].queryKey] = String(record.node.code || '');
			render(resolveProductSelection(nodes, settings, query), true);
		}

		options.forEach(function (option) {
			option.addEventListener('change', function (event) {
				if (event.target?.checked === false) return;
				choose(Number(option.getAttribute('data-product-option-level')) || 0, option.getAttribute('data-product-node-id'));
			});
		});
		selects.forEach(function (select) {
			select.addEventListener('change', function () { choose(Number(select.getAttribute('data-product-level-index')) || 0, select.value); });
		});
		window.addEventListener?.('popstate', function () { render(resolveProductSelection(nodes, settings, locationQuery()), false); });
		window.addEventListener?.('pagebuilder:product-query-change', function (event) {
			if (event?.detail?.source === shell) return;
			render(resolveProductSelection(nodes, settings, locationQuery()), false);
		});
		render(selection, settings.syncProductQuery !== false);
	}

function parseProConfig(root) {
		try { return JSON.parse(root.getAttribute('data-pro-config') || '{}'); }
		catch (_) { return {}; }
	}

function markProReady(root, kind) {
		if (!root || root.getAttribute('data-pb-pro-ready')) return false;
		root.setAttribute('data-pb-pro-ready', kind);
		return true;
	}

function initProductLeadForm(shell) {
		const root = shell?.matches?.('[data-pro-form]') ? shell : shell?.querySelector?.('[data-pro-form]');
		if (!root || !markProReady(shell, 'product-lead-form')) return;
		initProductSelector(shell, root);
		const config = parseProConfig(root);
		const validation = root.dataset.validation === 'custom' ? 'custom' : 'browser';
		const steps = Array.from(root.querySelectorAll(':scope [data-pro-form-step]'));
		const indicators = Array.from(root.querySelectorAll('[data-pro-step-indicator]'));
		const stepProgressBar = root.querySelector('[role="progressbar"]');
		const stepProgressFill = root.querySelector('[data-pro-step-progress-fill]');
		const stepProgressText = root.querySelector('[data-pro-step-progress-text]');
		const message = root.querySelector('[data-pro-form-message]');
		const messageLayer = message?.closest?.('[data-pro-form-message-layer]') || message;
		const messageTitle = message?.querySelector?.('[data-pro-form-message-title]') || null;
		const messageText = message?.querySelector?.('[data-pro-form-message-text]') || null;
		const messageIcon = message?.querySelector?.('[data-pro-form-message-icon]') || null;
		const messageClose = message?.querySelector?.('[data-pro-form-message-close]') || null;
		const fieldRoots = function () { return Array.from(root.querySelectorAll('[data-pro-form-field]')); };
		let activeStep = 0;
		function hideMessage() {
			if (messageLayer) messageLayer.hidden = true;
		}
		function showMessage(state, copy, title = '') {
			if (!message) return;
			const text = String(copy || '');
			const isError = state === 'error';
			message.classList.toggle('is-error', isError);
			message.classList.toggle('is-success', state === 'success');
			messageLayer?.classList?.toggle('is-error', isError);
			messageLayer?.classList?.toggle('is-success', state === 'success');
			if (messageTitle) messageTitle.textContent = title || (isError ? root.dataset.errorTitle || 'Submission failed' : root.dataset.successTitle || 'Message sent');
			if (messageText) messageText.textContent = text;
			else message.textContent = text;
			if (messageIcon) messageIcon.className = isError ? 'fas fa-exclamation-circle' : state === 'sending' ? 'fas fa-spinner fa-spin' : 'fas fa-check-circle';
			if (messageLayer) messageLayer.hidden = text === '';
		}
		messageClose?.addEventListener('click', function (event) { event.preventDefault(); hideMessage(); });
		messageLayer?.addEventListener?.('click', function (event) {
			if (event.target === messageLayer && root.dataset.messageDisplay === 'modal' && root.dataset.messageDismissible === '1') hideMessage();
		});
		function fieldValues() {
			const values = {};
			fieldRoots().forEach(function (wrapper) {
				const fieldId = wrapper.getAttribute('data-pro-form-field');
				if (!fieldId) return;
				const controls = Array.from(wrapper.querySelectorAll('input,textarea,select'));
				if (!controls.length) return;
				const first = controls[0];
				if (first.type === 'checkbox') {
					values[fieldId] = controls.filter(function (control) { return control.checked; }).map(function (control) { return control.value; });
				} else if (first.type === 'radio') {
					values[fieldId] = controls.find(function (control) { return control.checked; })?.value || '';
				} else if (first.multiple) {
					values[fieldId] = Array.from(first.selectedOptions || []).map(function (option) { return option.value; });
				} else {
					values[fieldId] = first.value || '';
				}
			});
			return values;
		}
		function conditionEmpty(value) {
			return Array.isArray(value) ? value.length === 0 || value.every(function (entry) { return String(entry || '') === ''; }) : String(value || '').trim() === '';
		}
		function conditionRuleMatches(rule, values) {
			const selectedParent = rule && rule.valueSource === 'selectedParent' && rule.parentFieldId;
			const actual = selectedParent ? values[rule.parentFieldId] : values[rule.fieldId];
			const expectedSource = selectedParent ? rule.parentValue : rule.value;
			if (selectedParent && ['equals', 'not_equals', 'contains'].includes(rule.operator) && conditionEmpty(expectedSource)) return false;
			const expectedValues = (Array.isArray(expectedSource) ? expectedSource : [expectedSource]).map(function (entry) { return String(entry || '').trim(); });
			const normalized = Array.isArray(actual) ? actual.map(function (entry) { return String(entry || '').trim(); }) : String(actual || '').trim();
			const equalsExpected = function (entry) {
				return expectedValues.some(function (expected) { return String(entry || '').toLowerCase() === expected.toLowerCase(); });
			};
			if (rule.operator === 'empty') return conditionEmpty(actual);
			if (rule.operator === 'not_empty') return !conditionEmpty(actual);
			if (rule.operator === 'contains') return Array.isArray(normalized) ? normalized.some(function (entry) { return expectedValues.some(function (expected) { return entry.toLowerCase().includes(expected.toLowerCase()); }); }) : expectedValues.some(function (expected) { return normalized.toLowerCase().includes(expected.toLowerCase()); });
			if (rule.operator === 'not_equals') return Array.isArray(normalized) ? !normalized.some(equalsExpected) : !equalsExpected(normalized);
			return Array.isArray(normalized) ? normalized.some(equalsExpected) : equalsExpected(normalized);
		}
		function conditionalVisible(wrapper, values) {
			let condition = {};
			try { condition = JSON.parse(wrapper.getAttribute('data-pro-conditional') || '{}'); } catch (_) { condition = {}; }
			const rules = Array.isArray(condition.rules) ? condition.rules.filter(function (rule) { return rule && rule.fieldId; }) : [];
			if (condition.enabled !== true || !rules.length) return true;
			const results = rules.map(function (rule) { return conditionRuleMatches(rule, values); });
			return condition.relation === 'any' ? results.some(Boolean) : results.every(Boolean);
		}
		function datasetOptions(wrapper, values) {
			const datasetId = wrapper.getAttribute('data-pro-dataset-id');
			if (!datasetId || !Array.isArray(config.datasets)) return null;
			const dataset = config.datasets.find(function (entry) { return String(entry.id) === String(datasetId); });
			if (!dataset || !Array.isArray(dataset.nodes)) return [];
			const parentFieldId = wrapper.getAttribute('data-pro-dataset-parent') || '';
			const parentValue = parentFieldId ? values[parentFieldId] : null;
			const parentValues = Array.isArray(parentValue) ? parentValue : [parentValue];
			const parentIds = parentFieldId
				? dataset.nodes.filter(function (node) { return parentValues.some(function (value) { return String(node.id) === String(value) || String(node.value) === String(value); }); }).map(function (node) { return String(node.id); })
				: [null];
			return dataset.nodes.filter(function (node) { return node && node.active !== false && parentIds.includes(node.parentId == null ? null : String(node.parentId)); }).sort(function (left, right) { return (Number(left.sortOrder) || 0) - (Number(right.sortOrder) || 0); });
		}
		function syncDatasetSelect(wrapper, values) {
			const select = wrapper.querySelector('select');
			const options = datasetOptions(wrapper, values);
			if (!select || options === null) return;
			const currentValue = select.value;
			const placeholder = Array.from(select.options || []).find(function (option) { return option.value === ''; });
			select.innerHTML = '';
			if (placeholder) select.appendChild(placeholder.cloneNode(true));
			options.forEach(function (node) {
				const option = document.createElement('option');
				option.value = String(node.value ?? node.code ?? node.id ?? '');
				option.textContent = String(node.label ?? node.name ?? option.value);
				select.appendChild(option);
			});
			if (options.some(function (node) { return String(node.value ?? node.code ?? node.id ?? '') === currentValue; })) select.value = currentValue;
			else select.value = '';
		}
		function syncConditionalFields() {
			const values = fieldValues();
			fieldRoots().forEach(function (wrapper) {
				const visible = conditionalVisible(wrapper, values);
				wrapper.hidden = !visible;
				wrapper.classList.toggle('is-conditional-hidden', !visible);
				const controls = Array.from(wrapper.querySelectorAll('input,textarea,select'));
				controls.forEach(function (control) {
					if (!control.dataset.originalRequired) control.dataset.originalRequired = control.required ? '1' : '0';
					control.required = visible && control.dataset.originalRequired === '1';
					control.disabled = !visible;
					if (!visible) {
						if (control.type === 'checkbox' || control.type === 'radio') control.checked = false;
						else if (control.type !== 'file') control.value = '';
					}
				});
				if (visible) syncDatasetSelect(wrapper, values);
			});
		}
		function renderStep(index) {
			activeStep = Math.max(0, Math.min(steps.length - 1, Number(index) || 0));
			steps.forEach(function (step, stepIndex) { step.hidden = stepIndex !== activeStep; });
			indicators.forEach(function (indicator, stepIndex) {
				indicator.classList.toggle('active', stepIndex <= activeStep);
				if (stepIndex === activeStep) indicator.setAttribute('aria-current', 'step');
				else indicator.removeAttribute('aria-current');
			});
			const progress = steps.length ? Math.round(((activeStep + 1) / steps.length) * 100) : 0;
			if (stepProgressBar) stepProgressBar.setAttribute('aria-valuenow', String(progress));
			if (stepProgressFill) stepProgressFill.style.width = progress + '%';
			if (stepProgressText) stepProgressText.textContent = 'Step ' + (activeStep + 1) + ' of ' + steps.length + ' · ' + progress + '%';
			syncConditionalFields();
		}
		function validateStep() {
			const fields = steps[activeStep]?.querySelectorAll('input,textarea,select') || [];
			for (const field of fields) {
				if (field.checkValidity()) continue;
				if (validation === 'browser') field.reportValidity();
				else field.focus?.();
				return false;
			}
			return true;
		}
		root.querySelectorAll('[data-pro-next]').forEach(function (button) { button.addEventListener('click', function () { if (validateStep()) renderStep(activeStep + 1); }); });
		root.querySelectorAll('[data-pro-previous]').forEach(function (button) { button.addEventListener('click', function () { renderStep(activeStep - 1); }); });
		root.addEventListener('input', syncConditionalFields);
		root.addEventListener('change', syncConditionalFields);
		root.addEventListener('submit', async function (event) {
			event.preventDefault();
			syncConditionalFields();
			if (!root.checkValidity()) { if (validation === 'browser') root.reportValidity(); else root.querySelector(':invalid')?.focus?.(); showMessage('error', root.dataset.errorMessage || 'Please check the form fields.', root.dataset.errorTitle || 'Submission failed'); return; }
			const actions = config.actions || [];
			const data = new FormData(root);
			const submit = root.querySelector('button[type="submit"]');
			if (!config.submitUrl) {
				const localOnly = actions.every(function (action) { return ['message', 'redirect'].includes(action); });
				showMessage(localOnly ? 'success' : 'error', localOnly ? root.dataset.successMessage || 'The form was sent successfully.' : root.dataset.errorMessage || 'This form is not connected yet.');
				return;
			}
			if (submit) submit.disabled = true;
			root.setAttribute('aria-busy', 'true');
			showMessage('sending', 'Sending...', '');
			try {
				const response = await fetch(config.submitUrl, { method: 'POST', headers: { Accept: 'application/json' }, body: data });
				const payload = await response.json().catch(function () { return {}; });
				if (!response.ok) throw payload;
				showMessage('success', payload.message || ((actions.includes('message')) ? root.dataset.successMessage || 'The form was sent successfully.' : ''));
				root.dispatchEvent(new CustomEvent('pagebuilder:form-submit', { bubbles: true, detail: { form: root, data, actions, response: payload } }));
				if (payload.redirect && /^(?:https?:\/\/|\/|#)/i.test(String(payload.redirect))) window.location.assign(payload.redirect);
			} catch (error) {
				showMessage('error', error?.message || root.dataset.errorMessage || 'An error occurred.');
			} finally {
				if (submit) submit.disabled = submit.dataset.productInvalid === '1';
				root.setAttribute('aria-busy', 'false');
			}
		});
		if (steps.length) renderStep(0);
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		Array.from(root.querySelectorAll("[data-product-lead-form]"))
			.filter(function (candidate) { return !candidate.parentElement?.closest?.('[data-product-lead-form]'); })
			.forEach(initProductLeadForm);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["product_lead_form"] = Object.freeze({ init, initProductLeadForm, resolveProductSelection, updateProductQuery });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
