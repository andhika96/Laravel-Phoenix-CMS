import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

class MockClassList {
  constructor() {
    this.values = new Set();
  }

  contains(name) {
    return this.values.has(name);
  }

  toggle(name, force) {
    if (force) {
      this.values.add(name);
    } else {
      this.values.delete(name);
    }
  }
}

function createElement(tagName, colorButtons) {
  const element = {
    tagName,
    attributes: new Map(),
    children: [],
    classList: new MockClassList(),
    dataset: {},
    listeners: {},
    style: {},
    appendChild(child) {
      this.children.push(child);
    },
    addEventListener(name, listener) {
      this.listeners[name] = listener;
    },
    setAttribute(name, value) {
      this.attributes.set(name, value);
    },
  };

  if (tagName === 'button') {
    colorButtons.push(element);
  }

  return element;
}

test('Arunika Equinox profile palette applies, persists, and marks selected colors', () => {
  const script = readFileSync(
    'public/assets/js/themes/arunika_equinox/arunika_equinox.js',
    'utf8',
  );
  const paletteScript = script.split('// --- 2. SIDEBAR & SCROLLBAR LOGIC ---')[0];
  const colorButtons = [];
  const storedValues = new Map();
  const rootValues = new Map();
  const pickerContainer = {
    children: [],
    appendChild(child) {
      this.children.push(child);
    },
    querySelectorAll() {
      return colorButtons;
    },
    replaceChildren() {
      this.children = [];
      colorButtons.length = 0;
    },
  };
  const documentElement = {
    dataset: {},
    style: {
      setProperty(name, value) {
        rootValues.set(name, value);
      },
    },
  };
  const context = {
    document: {
      documentElement,
      createElement(tagName) {
        return createElement(tagName, colorButtons);
      },
      getElementById(id) {
        return id === 'color-picker-container' ? pickerContainer : null;
      },
    },
    localStorage: {
      getItem(name) {
        return storedValues.get(name) ?? null;
      },
      setItem(name, value) {
        storedValues.set(name, value);
      },
    },
  };

  vm.runInNewContext(paletteScript, context);

  assert.equal(colorButtons.length, 9);
  assert.equal(colorButtons[0].dataset.themeColor, '#0F766E');
  assert.equal(colorButtons.at(-1).dataset.themeColor, '#C7CCD8');
  assert.equal(colorButtons[0].classList.contains('is-active'), true);

  colorButtons[2].listeners.click();

  assert.equal(storedValues.get('theme-color'), '#9D00FF');
  assert.equal(rootValues.get('--ph-theme-primary'), '#9D00FF');
  assert.equal(rootValues.get('--ph-theme-surface-tint'), '#9D00FF');
  assert.equal(colorButtons[2].classList.contains('is-active'), true);
  assert.equal(colorButtons[0].classList.contains('is-active'), false);

  colorButtons.at(-1).listeners.click();

  assert.equal(storedValues.get('theme-color'), '#C7CCD8');
  assert.equal(rootValues.get('--ph-theme-primary'), '#667085');
  assert.equal(rootValues.get('--ph-theme-surface-tint'), '#C7CCD8');
  assert.equal(documentElement.dataset.phThemeColor, 'cool-gray');
  assert.equal(colorButtons.at(-1).attributes.get('aria-pressed'), 'true');
});
