export const VISUAL_TYPES = ['table', 'icon', 'icon-list'];
export const NODE_TYPES = ['trigger', 'condition', 'approval', 'delay', 'email', 'webhook', 'update', 'end', ...VISUAL_TYPES];
export const CATEGORIES = ['general', 'approval', 'form', 'automation', 'diagram'];
export const NODE_WIDTH = 220;
export const NODE_HEIGHT = 96;
export const TABLE_CHROME_HEIGHT = 60;

const EVENT_VALUES = ['manual', 'form.submitted', 'data.created', 'data.updated'];
const CONDITION_OPERATORS = ['eq', 'neq', 'gt', 'gte', 'lt', 'lte', 'contains', 'exists'];
const METHOD_VALUES = ['POST'];
const PORTS_BY_TYPE = {
  trigger: ['out'],
  condition: ['true', 'false'],
  approval: ['approved', 'rejected'],
  end: [],
  delay: ['out'],
  email: ['out'],
  webhook: ['out'],
  update: ['out'],
};

export const NODE_CATALOG = {
  trigger: { label: 'Trigger', icon: 'fa-bolt', tone: 'primary', description: 'Mulai alur dari event' },
  condition: { label: 'Condition', icon: 'fa-code-branch', tone: 'warning', description: 'Pilih jalur berdasarkan data' },
  approval: { label: 'Approval', icon: 'fa-check-circle', tone: 'info', description: 'Persetujuan simulasi' },
  delay: { label: 'Delay', icon: 'fa-clock', tone: 'secondary', description: 'Tunda alur beberapa menit' },
  email: { label: 'Email', icon: 'fa-envelope', tone: 'success', description: 'Langkah email simulasi' },
  webhook: { label: 'Webhook', icon: 'fa-external-link-alt', tone: 'danger', description: 'Panggilan webhook simulasi' },
  update: { label: 'Update', icon: 'fa-sync-alt', tone: 'purple', description: 'Perbarui nilai field' },
  end: { label: 'End', icon: 'fa-stop-circle', tone: 'dark', description: 'Akhir alur kerja' },
  table: { label: 'Table', icon: 'fa-table', tone: 'info', description: 'Tabel dengan row dan column custom' },
  icon: { label: 'Icon', icon: 'fa-icons', tone: 'warning', description: 'Ikon Font Awesome' },
  'icon-list': { label: 'Icon List', icon: 'fa-list-ul', tone: 'success', description: 'Daftar ikon dan teks' },
};

const DEFAULT_CONFIG = {
  trigger: { event: 'manual' },
  condition: { field: '', operator: 'eq', value: '' },
  approval: { assignee_id: '', assignee_role: '' },
  delay: { minutes: 5 },
  email: { to: '', subject: '' },
  webhook: { url: '', method: 'POST' },
  update: { field: '', value: '' },
  end: {},
  table: tableConfig(),
  icon: { iconClass: 'fas fa-star', color: '#1fa675', size: 64, width: 160, height: 160 },
  'icon-list': { items: [{ iconClass: 'fas fa-check', text: 'Item 1' }], color: '#1fa675', textColor: '#243746', fontSize: 14, gap: 8, width: 280 },
};

function clone(value) {
  return JSON.parse(JSON.stringify(value));
}

function finiteNumber(value, fallback = 0) {
  const number = Number(value);
  return Number.isFinite(number) ? number : fallback;
}

function bounded(value, minimum, maximum, fallback) {
  return Math.min(maximum, Math.max(minimum, Math.round(finiteNumber(value, fallback))));
}

export function isVisualNode(node) {
  return VISUAL_TYPES.includes(node?.type);
}

export function tableConfig(config = {}) {
  const rows = bounded(config.rows, 1, 20, 3);
  const columns = bounded(config.columns, 1, 20, 3);
  return {
    ...config, rows, columns,
    cells: Array.from({ length: rows }, (_, row) => Array.from({ length: columns }, (_, column) => String(config.cells?.[row]?.[column] ?? ''))),
    columnWidths: Array.from({ length: columns }, (_, index) => bounded(config.columnWidths?.[index], 60, 600, 120)),
    rowHeights: Array.from({ length: rows }, (_, index) => bounded(config.rowHeights?.[index], 28, 240, 42)),
    header: config.header ?? true, fontSize: bounded(config.fontSize, 10, 32, 14), align: config.align || 'left',
    textColor: config.textColor || '#243746', background: config.background || '#ffffff',
    headerBackground: config.headerBackground || '#e8f5ef', borderColor: config.borderColor || '#b7c9c0',
  };
}

export function resizeTableGrid(config, rows, columns) {
  return tableConfig({ ...config, rows, columns });
}

export function insertTableRow(config, index) {
  const next = tableConfig(config);
  if (next.rows >= 20) return next;
  const position = bounded(index, 0, next.rows, next.rows);
  next.cells.splice(position, 0, Array(next.columns).fill(''));
  next.rowHeights.splice(position, 0, 42);
  return tableConfig({ ...next, rows: next.rows + 1 });
}

export function deleteTableRow(config, index) {
  const next = tableConfig(config);
  if (next.rows <= 1) return next;
  const position = bounded(index, 0, next.rows - 1, next.rows - 1);
  next.cells.splice(position, 1);
  next.rowHeights.splice(position, 1);
  return tableConfig({ ...next, rows: next.rows - 1 });
}

export function insertTableColumn(config, index) {
  const next = tableConfig(config);
  if (next.columns >= 20) return next;
  const position = bounded(index, 0, next.columns, next.columns);
  next.cells.forEach(row => row.splice(position, 0, ''));
  next.columnWidths.splice(position, 0, 120);
  return tableConfig({ ...next, columns: next.columns + 1 });
}

export function deleteTableColumn(config, index) {
  const next = tableConfig(config);
  if (next.columns <= 1) return next;
  const position = bounded(index, 0, next.columns - 1, next.columns - 1);
  next.cells.forEach(row => row.splice(position, 1));
  next.columnWidths.splice(position, 1);
  return tableConfig({ ...next, columns: next.columns - 1 });
}

export function resizeTableColumn(config, index, width) {
  const next = tableConfig(config);
  if (index >= 0 && index < next.columns) next.columnWidths[index] = bounded(width, 60, 600, 120);
  return next;
}

export function resizeTableRow(config, index, height) {
  const next = tableConfig(config);
  if (index >= 0 && index < next.rows) next.rowHeights[index] = bounded(height, 28, 240, 42);
  return next;
}

export function resizeTableTo(config, width, height) {
  const next = tableConfig(config);
  const oldWidth = next.columnWidths.reduce((sum, value) => sum + value, 0);
  const oldHeight = next.rowHeights.reduce((sum, value) => sum + value, 0);
  next.columnWidths = next.columnWidths.map(value => bounded(value * Math.max(1, width - 2) / oldWidth, 60, 600, value));
  next.rowHeights = next.rowHeights.map(value => bounded(value * Math.max(1, height - TABLE_CHROME_HEIGHT - 2) / oldHeight, 28, 240, value));
  return next;
}

export function nodeSize(node) {
  const config = node?.config || {};
  if (node?.type === 'table') {
    const table = tableConfig(config);
    return { width: table.columnWidths.reduce((sum, value) => sum + value, 2), height: table.rowHeights.reduce((sum, value) => sum + value, TABLE_CHROME_HEIGHT + 2) };
  }
  if (node?.type === 'icon') return { width: finiteNumber(config.width, 160), height: finiteNumber(config.height, 160) };
  if (node?.type === 'icon-list') return { width: finiteNumber(config.width, 280), height: 60 + (config.items?.length || 1) * (Math.max(24, finiteNumber(config.fontSize, 14) + 8) + finiteNumber(config.gap, 8)) };
  return { width: finiteNumber(node?.width, NODE_WIDTH), height: finiteNumber(node?.height, NODE_HEIGHT) };
}

function visualConfigErrors(node) {
  const config = node.config || {};
  const errors = [];
  const number = (value, min, max) => Number.isInteger(Number(value)) && Number(value) >= min && Number(value) <= max;
  const color = value => /^#[0-9a-f]{6}$/i.test(String(value));
  const icon = value => /^(?:fas|far|fab|fal|fad) fa-[a-z0-9-]+$/.test(String(value)) && String(value).length <= 120;
  if (node.type === 'table') {
    if (!number(config.rows,1,20) || !number(config.columns,1,20)) errors.push('Table membutuhkan 1 sampai 20 row/column.');
    if (!Array.isArray(config.cells) || config.cells.length !== Number(config.rows) || config.cells.some(row => !Array.isArray(row) || row.length !== Number(config.columns) || row.some(cell => typeof cell !== 'string' || cell.length > 500))) errors.push('Isi sel Table tidak sesuai ukuran grid.');
    if (!Array.isArray(config.columnWidths) || config.columnWidths.length !== Number(config.columns) || config.columnWidths.some(value => !number(value,60,600))) errors.push('Lebar column harus 60 sampai 600 px.');
    if (!Array.isArray(config.rowHeights) || config.rowHeights.length !== Number(config.rows) || config.rowHeights.some(value => !number(value,28,240))) errors.push('Tinggi row harus 28 sampai 240 px.');
    if (typeof config.header !== 'boolean' || !number(config.fontSize,10,32) || !['left','center','right'].includes(config.align)) errors.push('Style Table tidak valid.');
    for (const key of ['textColor','background','headerBackground','borderColor']) if (!color(config[key])) errors.push('Warna Table tidak valid.');
  } else if (node.type === 'icon') {
    if (!icon(config.iconClass) || !color(config.color) || !number(config.size,12,256) || !number(config.width,80,1000) || !number(config.height,80,1000)) errors.push('Pengaturan Icon tidak valid.');
  } else if (node.type === 'icon-list') {
    if (!Array.isArray(config.items) || config.items.length < 1 || config.items.length > 30 || config.items.some(item => !item || !icon(item.iconClass) || typeof item.text !== 'string' || item.text.length > 500)) errors.push('Icon List membutuhkan 1 sampai 30 item yang valid.');
    if (!color(config.color) || !color(config.textColor) || !number(config.width,160,1000) || !number(config.fontSize,10,32) || !number(config.gap,0,40)) errors.push('Style Icon List tidak valid.');
  }
  return errors;
}

function safeLabel(type, label) {
  const value = String(label ?? '').trim();
  return value || NODE_CATALOG[type]?.label || 'Node';
}

export function createNode(type, id, x = 0, y = 0, config = {}) {
  const safeType = NODE_TYPES.includes(type) ? type : 'end';
  return {
    id: String(id || `${safeType}-${Date.now()}`),
    type: safeType,
    label: safeLabel(safeType, config.label),
    x: finiteNumber(x),
    y: finiteNumber(y),
    config: safeType === 'table' ? tableConfig(config) : { ...clone(DEFAULT_CONFIG[safeType] || {}), ...clone(config) },
  };
}

function edge(id, source, target, port = 'out') {
  return { id, source, target, port };
}

export function createDefaultGraph(template = 'blank') {
  const trigger = createNode('trigger', 'trigger-1', 72, 168, { event: template === 'form' ? 'form.submitted' : template === 'automation' ? 'data.updated' : 'manual' });
  const end = createNode('end', 'end-1', 708, 168);

  if (template === 'approval') {
    const approval = createNode('approval', 'approval-1', 390, 168, { assignee_role: 'Manager' });
    return { nodes: [trigger, approval, end], edges: [edge('edge-1', trigger.id, approval.id), edge('edge-2', approval.id, end.id, 'approved'), edge('edge-3', approval.id, end.id, 'rejected')], viewport: { x: 0, y: 0, zoom: 1 } };
  }

  if (template === 'automation') {
    const update = createNode('update', 'update-1', 390, 168, { field: 'status', value: 'processed' });
    return { nodes: [trigger, update, end], edges: [edge('edge-1', trigger.id, update.id), edge('edge-2', update.id, end.id)], viewport: { x: 0, y: 0, zoom: 1 } };
  }

  return { nodes: [trigger, end], edges: [edge('edge-1', trigger.id, end.id)], viewport: { x: 0, y: 0, zoom: 1 } };
}

export function normalizeGraph(input = {}) {
  const source = input && typeof input === 'object' ? input : {};
  const nodes = Array.isArray(source.nodes) ? source.nodes.map((node, index) => {
    const raw = node && typeof node === 'object' ? node : {};
    const type = String(raw.type || 'end');
    const config = raw.config && typeof raw.config === 'object' && !Array.isArray(raw.config) ? raw.config : {};
    return {
      id: String(raw.id ?? `node-${index + 1}`),
      type,
      label: safeLabel(type, raw.label),
      x: finiteNumber(raw.x),
      y: finiteNumber(raw.y),
      config: type === 'table' ? tableConfig(config) : { ...(clone(DEFAULT_CONFIG[type] || {})), ...clone(config) },
    };
  }) : [];
  const edges = Array.isArray(source.edges) ? source.edges.map((item, index) => {
    const raw = item && typeof item === 'object' ? item : {};
    return {
      id: String(raw.id ?? `edge-${index + 1}`),
      source: String(raw.source ?? ''),
      target: String(raw.target ?? ''),
      port: String(raw.port || 'out'),
    };
  }) : [];
  const viewport = source.viewport && typeof source.viewport === 'object' ? source.viewport : {};

  return {
    nodes,
    edges,
    viewport: {
      x: finiteNumber(viewport.x),
      y: finiteNumber(viewport.y),
      zoom: finiteNumber(viewport.zoom, 1),
    },
  };
}

export function cloneGraph(graph) {
  return normalizeGraph(clone(graph || {}));
}

export function allowedPorts(type) {
  return [...(PORTS_BY_TYPE[type] || [])];
}

export function validateGraphShape(graph) {
  const errors = [];
  if (!graph || typeof graph !== 'object') return { valid: false, errors: ['Graph harus berupa object.'] };
  if (!Array.isArray(graph.nodes)) errors.push('Nodes harus berupa array.');
  if (!Array.isArray(graph.edges)) errors.push('Edges harus berupa array.');
  if (graph.viewport !== undefined) {
    if (!graph.viewport || typeof graph.viewport !== 'object' || Array.isArray(graph.viewport)) errors.push('Viewport tidak valid.');
    else if (!Number.isFinite(Number(graph.viewport.x)) || !Number.isFinite(Number(graph.viewport.y)) || !Number.isFinite(Number(graph.viewport.zoom)) || Number(graph.viewport.zoom) < 0.1 || Number(graph.viewport.zoom) > 4) errors.push('Viewport harus memiliki x, y, dan zoom 0.1 sampai 4.');
  }
  if (errors.length) return { valid: false, errors };

  const nodeIds = new Set();
  graph.nodes.forEach((node, index) => {
    if (!node || typeof node !== 'object') {
      errors.push(`Node ${index + 1} tidak valid.`);
      return;
    }
    if (!String(node.id || '').trim()) errors.push(`Node ${index + 1} membutuhkan id.`);
    else if (nodeIds.has(String(node.id))) errors.push(`Id node duplikat: ${node.id}.`);
    else nodeIds.add(String(node.id));
    if (!NODE_TYPES.includes(node.type)) errors.push(`Tipe node tidak didukung: ${node.type || '(kosong)'}.`);
    if (!Number.isFinite(Number(node.x)) || !Number.isFinite(Number(node.y))) errors.push(`Posisi node ${node.id || index + 1} tidak valid.`);
    if (!node.config || typeof node.config !== 'object' || Array.isArray(node.config)) errors.push(`Config node ${node.id || index + 1} tidak valid.`);
    if (isVisualNode(node)) errors.push(...visualConfigErrors(node));
  });

  const edgeIds = new Set();
  graph.edges.forEach((item, index) => {
    if (!item || typeof item !== 'object') {
      errors.push(`Edge ${index + 1} tidak valid.`);
      return;
    }
    if (!String(item.id || '').trim()) errors.push(`Edge ${index + 1} membutuhkan id.`);
    else if (edgeIds.has(String(item.id))) errors.push(`Id edge duplikat: ${item.id}.`);
    else edgeIds.add(String(item.id));
    if (!nodeIds.has(String(item.source || ''))) errors.push(`Source edge ${item.id || index + 1} tidak ditemukan.`);
    if (!nodeIds.has(String(item.target || ''))) errors.push(`Target edge ${item.id || index + 1} tidak ditemukan.`);
    if (!['out', 'true', 'false', 'approved', 'rejected'].includes(item.port)) errors.push(`Port edge ${item.id || index + 1} tidak valid.`);
    if (graph.nodes.some(node => isVisualNode(node) && [item.source,item.target].includes(node.id))) errors.push('Elemen visual tidak dapat menjadi langkah eksekusi workflow.');
  });

  return { valid: errors.length === 0, errors };
}

function hasText(value) {
  return String(value ?? '').trim().length > 0;
}

function validUrl(value) {
  try {
    const url = new URL(String(value));
    return ['http:', 'https:'].includes(url.protocol);
  } catch {
    return false;
  }
}

function validEmail(value) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value ?? '').trim());
}

function configErrors(node) {
  const config = node.config || {};
  switch (node.type) {
    case 'trigger':
      return EVENT_VALUES.includes(config.event) ? [] : [`Trigger ${node.label} membutuhkan event yang valid.`];
    case 'condition':
      return [
        ...(hasText(config.field) ? [] : [`Condition ${node.label} membutuhkan field.`]),
        ...(CONDITION_OPERATORS.includes(config.operator) ? [] : [`Condition ${node.label} membutuhkan operator yang valid.`]),
        ...(config.operator === 'exists' || hasText(config.value) ? [] : [`Condition ${node.label} membutuhkan value.`]),
      ];
    case 'approval':
      return ((Number.isInteger(Number(config.assignee_id)) && Number(config.assignee_id) > 0) || hasText(config.assignee_role)) ? [] : [`Approval ${node.label} membutuhkan assignee user atau role.`];
    case 'delay':
      return Number.isInteger(Number(config.minutes)) && Number(config.minutes) >= 0 && Number(config.minutes) <= 43200 ? [] : [`Delay ${node.label} harus 0 sampai 43200 menit.`];
    case 'email':
      return [
        ...(validEmail(config.to) ? [] : [`Email ${node.label} membutuhkan alamat penerima yang valid.`]),
        ...(hasText(config.subject) ? [] : [`Email ${node.label} membutuhkan subject.`]),
      ];
    case 'webhook':
      return validUrl(config.url) && METHOD_VALUES.includes(config.method || 'POST') ? [] : [`Webhook ${node.label} membutuhkan URL HTTP/HTTPS dan method POST.`];
    case 'update':
      return hasText(config.field) ? [] : [`Update ${node.label} membutuhkan field.`];
    default:
      return [];
  }
}

export function validatePublishGraph(graph) {
  const shape = validateGraphShape(graph);
  if (!shape.valid) return shape;
  const errors = [];
  const nodes = graph.nodes.filter(node => !isVisualNode(node));
  if (!nodes.length && graph.nodes.length) return {valid:true,errors:[]};
  const byId = new Map(nodes.map((node) => [String(node.id), node]));
  const outgoing = new Map(nodes.map((node) => [String(node.id), []]));
  const incoming = new Map(nodes.map((node) => [String(node.id), []]));

  graph.edges.forEach((item) => {
    const source = byId.get(String(item.source));
    const target = byId.get(String(item.target));
    outgoing.get(String(item.source)).push(item);
    incoming.get(String(item.target)).push(item);
    if (source && !allowedPorts(source.type).includes(item.port)) errors.push(`${source.label} tidak mendukung port ${item.port}.`);
    if (source && source.type === 'end') errors.push('End tidak boleh memiliki output.');
    if (target && target.type === 'trigger') errors.push('Trigger tidak boleh memiliki input.');
  });

  const triggers = nodes.filter((node) => node.type === 'trigger');
  if (triggers.length !== 1) errors.push('Workflow harus memiliki tepat satu Trigger.');
  if (!nodes.some((node) => node.type === 'end')) errors.push('Workflow membutuhkan setidaknya satu End.');
  nodes.forEach((node) => errors.push(...configErrors(node)));

  outgoing.forEach((items, id) => {
    const ports = new Set();
    items.forEach((item) => {
      if (ports.has(item.port)) errors.push(`Node ${byId.get(id)?.label || id} memiliki lebih dari satu output pada port ${item.port}.`);
      ports.add(item.port);
    });
    const node = byId.get(id);
    allowedPorts(node?.type).forEach((port) => {
      if (!ports.has(port)) errors.push(`Node ${node?.label || id} membutuhkan koneksi pada port ${port}.`);
    });
  });

  const visitState = new Map();
  const visit = (id) => {
    if (visitState.get(id) === 'visiting') return true;
    if (visitState.get(id) === 'visited') return false;
    visitState.set(id, 'visiting');
    const cycle = (outgoing.get(id) || []).some((item) => visit(String(item.target)));
    visitState.set(id, 'visited');
    return cycle;
  };
  if (nodes.some((node) => visit(String(node.id)))) errors.push('Workflow tidak boleh memiliki cycle.');

  if (triggers.length === 1) {
    const reachable = new Set();
    const stack = [String(triggers[0].id)];
    while (stack.length) {
      const id = stack.pop();
      if (reachable.has(id)) continue;
      reachable.add(id);
      (outgoing.get(id) || []).forEach((item) => stack.push(String(item.target)));
    }
    nodes.filter((node) => !reachable.has(String(node.id))).forEach((node) => errors.push(`Node ${node.label} tidak terhubung ke Trigger.`));
  }

  return { valid: errors.length === 0, errors };
}

function round(value) {
  return Math.round(Number(value) * 100) / 100;
}

export function portPoint(node, port = 'out', role = 'source') {
  const { width, height } = nodeSize(node);
  const x = finiteNumber(node?.x);
  const y = finiteNumber(node?.y);
  const branch = port === 'true' || port === 'approved' ? 0.3 : port === 'false' || port === 'rejected' ? 0.7 : 0.5;
  return { x: role === 'target' ? x : x + width, y: y + height * branch };
}

export function bezierPath(source, target, port = 'out') {
  const from = portPoint(source, port, 'source');
  const to = portPoint(target, 'in', 'target');
  const distance = Math.max(56, Math.abs(to.x - from.x) * 0.45);
  return `M ${round(from.x)} ${round(from.y)} C ${round(from.x + distance)} ${round(from.y)}, ${round(to.x - distance)} ${round(to.y)}, ${round(to.x)} ${round(to.y)}`;
}

export function graphBounds(graph) {
  if (!graph?.nodes?.length) return { minX: 0, minY: 0, maxX: NODE_WIDTH, maxY: NODE_HEIGHT, width: NODE_WIDTH, height: NODE_HEIGHT };
  const xs = graph.nodes.map((node) => finiteNumber(node.x));
  const ys = graph.nodes.map((node) => finiteNumber(node.y));
  const minX = Math.min(...xs);
  const minY = Math.min(...ys);
  const maxX = Math.max(...graph.nodes.map((node) => finiteNumber(node.x) + nodeSize(node).width));
  const maxY = Math.max(...graph.nodes.map((node) => finiteNumber(node.y) + nodeSize(node).height));
  return { minX, minY, maxX, maxY, width: maxX - minX, height: maxY - minY };
}

export function createId(prefix = 'node') {
  const random = Math.random().toString(36).slice(2, 8);
  return `${prefix}-${Date.now().toString(36)}-${random}`;
}

export function isKnownGraph(graph) {
  return validateGraphShape(graph).valid;
}

if (typeof window !== 'undefined') {
  window.WorkflowGraph = {
    NODE_TYPES,
    CATEGORIES,
    NODE_WIDTH,
    NODE_HEIGHT,
    NODE_CATALOG,
    createNode,
    createDefaultGraph,
    normalizeGraph,
    cloneGraph,
    allowedPorts,
    validateGraphShape,
    validatePublishGraph,
    portPoint,
    bezierPath,
    graphBounds,
    createId,
    isKnownGraph,
  };
}
