export const FONT_AWESOME_METADATA_URL = '/assets/plugins/fontawesome/5.15.3/metadata/icons.json';

export const FONT_AWESOME_ICON_GROUPS = Object.freeze([
  { key: 'all', label: 'All Icons', style: null, icon: 'fas fa-bars' },
  { key: 'regular', label: 'Regular', style: 'regular', icon: 'far fa-image' },
  { key: 'solid', label: 'Solid', style: 'solid', icon: 'fas fa-star' },
  { key: 'brands', label: 'Brands', style: 'brands', icon: 'fab fa-font-awesome-flag' },
  { key: 'light', label: 'Light', style: 'light', icon: 'fal fa-star' },
  { key: 'duotone', label: 'Duotone', style: 'duotone', icon: 'fad fa-star' },
]);

const STYLE_PREFIXES = Object.freeze({ regular: 'far', solid: 'fas', brands: 'fab', light: 'fal', duotone: 'fad' });
const ALLOWED_STYLES = Object.keys(STYLE_PREFIXES);

export function fontAwesomeStylePrefix(style) {
  return STYLE_PREFIXES[String(style || '').trim().toLowerCase()] || STYLE_PREFIXES.regular;
}

export function humanizeIconName(name) {
  const raw = String(name || '').trim();
  if (!raw) return 'Star';
  return raw.split('-').filter(Boolean).map((part) => part.charAt(0).toUpperCase() + part.slice(1)).join(' ');
}

export function iconClassName(style, name) {
  const safeName = String(name || '').trim().toLowerCase().replace(/^fa-/, '') || 'star';
  return `${fontAwesomeStylePrefix(style)} fa-${safeName}`;
}

export function parseIconClass(value) {
  const tokens = String(value || '').trim().split(/\s+/).filter(Boolean);
  const style = tokens.find((token) => STYLE_PREFIXES && Object.values(STYLE_PREFIXES).includes(token)) || 'far';
  const name = tokens.find((token) => token.startsWith('fa-')) || 'fa-star';
  const styleName = Object.entries(STYLE_PREFIXES).find(([, prefix]) => prefix === style)?.[0] || 'regular';
  return { style: styleName, name: name.slice(3) };
}

export function buildFontAwesomeIconLibrary(metadata) {
  if (!metadata || typeof metadata !== 'object' || Array.isArray(metadata)) return [];
  const entries = [];
  Object.entries(metadata).forEach(([rawName, meta]) => {
    if (!meta || typeof meta !== 'object') return;
    const name = String(rawName || '').trim().toLowerCase();
    if (!name) return;
    const label = String(meta.label || '').trim() || humanizeIconName(name);
    const terms = Array.isArray(meta.search?.terms) ? meta.search.terms.join(' ') : '';
    const searchText = `${label} ${name} ${terms}`.toLowerCase();
    (Array.isArray(meta.styles) ? meta.styles : []).forEach((rawStyle) => {
      const style = String(rawStyle || '').trim().toLowerCase();
      if (!ALLOWED_STYLES.includes(style)) return;
      entries.push({
        id: `${style}:${name}`,
        style,
        name,
        label,
        searchText,
        className: iconClassName(style, name),
      });
    });
  });
  return entries.sort((a, b) => a.label.localeCompare(b.label) || a.style.localeCompare(b.style) || a.name.localeCompare(b.name));
}

export function filterIconLibrary(icons, { group = 'all', query = '', offset = 0, limit = 100 } = {}) {
  const needle = String(query || '').trim().toLowerCase();
  const style = String(group || 'all').trim().toLowerCase();
  const filtered = (Array.isArray(icons) ? icons : []).filter((item) => {
    if (!item || (style !== 'all' && item.style !== style)) return false;
    return !needle || String(item.searchText || '').toLowerCase().includes(needle);
  });
  const start = Math.max(0, Number(offset) || 0);
  const size = Math.max(1, Number(limit) || 100);
  return { items: filtered.slice(start, start + size), total: filtered.length };
}

export async function loadFontAwesomeIconLibrary(fetcher = globalThis.fetch) {
  if (typeof fetcher !== 'function') throw new Error('Fetch is not available.');
  const response = await fetcher(FONT_AWESOME_METADATA_URL);
  if (!response?.ok) throw new Error(`HTTP ${response?.status || 0}`);
  return buildFontAwesomeIconLibrary(await response.json());
}

if (typeof window !== 'undefined') {
  window.WorkflowIcons = {
    FONT_AWESOME_METADATA_URL,
    FONT_AWESOME_ICON_GROUPS,
    fontAwesomeStylePrefix,
    humanizeIconName,
    iconClassName,
    parseIconClass,
    buildFontAwesomeIconLibrary,
    filterIconLibrary,
    loadFontAwesomeIconLibrary,
  };
}
