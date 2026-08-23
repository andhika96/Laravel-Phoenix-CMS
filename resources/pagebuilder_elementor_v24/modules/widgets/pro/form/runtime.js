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

function parseProConfig(root) {
		try { return JSON.parse(root.getAttribute('data-pro-config') || '{}'); }
		catch (_) { return {}; }
	}

function markProReady(root, kind) {
		if (!root || root.getAttribute('data-pb-pro-ready')) return false;
		root.setAttribute('data-pb-pro-ready', kind);
		return true;
	}

function initProForm(root) {
		if (!markProReady(root, 'form')) return;
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
				if (submit) submit.disabled = false;
				root.setAttribute('aria-busy', 'false');
			}
		});
		if (steps.length) renderStep(0);
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-pro-form]:not([data-product-lead-form])").forEach(initProForm);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["form"] = Object.freeze({ init, initProForm });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
