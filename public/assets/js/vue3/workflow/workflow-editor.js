import {
  NODE_TYPES,
  NODE_CATALOG,
  VISUAL_TYPES,
  createDefaultGraph,
  createNode,
  normalizeGraph,
  cloneGraph,
  allowedPorts,
  isVisualNode,
  nodeSize,
  tableConfig,
  resizeTableGrid,
  insertTableRow,
  deleteTableRow,
  insertTableColumn,
  deleteTableColumn,
  resizeTableColumn,
  resizeTableRow,
  resizeTableTo,
  validateGraphShape,
  validatePublishGraph,
  bezierPath,
  graphBounds,
  createId,
  portPoint,
} from './workflow-graph.js?v=20261005-table-icons';
import {
  FONT_AWESOME_ICON_GROUPS,
  loadFontAwesomeIconLibrary,
  filterIconLibrary,
} from './workflow-icons.js?v=20261005-table-icons';

(function () {
  if (!window.Vue) return;

  const root = document.getElementById('ph-app-workflow-editor');
  if (!root) return;

  const initial = window.WorkflowEditorBootstrap || {};
  const existing = initial.workflow && typeof initial.workflow === 'object' ? initial.workflow : null;
  const initialGraph = normalizeGraph(existing?.graph || createDefaultGraph('blank'));
  const { createApp } = window.Vue;

  const app = createApp({
    data() {
      return {
        workflow: {
          id: existing?.id || null,
          name: existing?.name || 'Untitled workflow',
          description: existing?.description || '',
          category: existing?.category || 'general',
          status: existing?.status || 'draft',
          draft_revision: Number(existing?.draft_revision || 0),
          published_version: existing?.published_version || null,
          published_revision: Number(existing?.published_revision || 0),
          updated_at: existing?.updated_at || null,
        },
        graph: initialGraph,
        permissions: initial.permissions || {},
        urls: initial.urls || {},
        selectedNodeId: null,
        selectedEdgeId: null,
        connecting: null,
        connectionPoint: null,
        panning: false,
        panState: null,
        nodeDrag: null,
        tableResize: null,
        tableSelection: { nodeId: null, row: 0, column: 0 },
        dirty: false,
        ready: false,
        saving: false,
        publishing: false,
        duplicating: false,
        archiving: false,
        simulating: false,
        saveState: existing?.id ? 'saved' : 'unsaved',
        history: [],
        future: [],
        notice: { message: '', type: 'danger' },
        templates: [
          { key: 'blank', label: 'Blank', description: 'Trigger to end', icon: 'fa-square' },
          { key: 'approval', label: 'Approval', description: 'Manager review', icon: 'fa-check-circle' },
          { key: 'form', label: 'Form', description: 'Form submitted', icon: 'fa-list-alt' },
          { key: 'automation', label: 'Automation', description: 'Update a record', icon: 'fa-bolt' },
        ],
        simulationContext: '{}',
        lastRun: null,
        runs: [],
        runsLoaded: false,
        runsLoading: false,
        editorFieldSnapshot: false,
        paletteClickSuppressedUntil: 0,
        beforeUnloadHandler: null,
        canvasPointer: null,
        iconLibrary: { icons: [], loaded: false, loading: false, error: '' },
        iconPicker: { open: false, group: 'all', query: '', visibleCount: 100, selected: null, target: null, returnFocus: null },
      };
    },
    computed: {
      nodeTypes() {
        return NODE_TYPES;
      },
      workflowStepTypes() {
        return NODE_TYPES.filter((type) => !VISUAL_TYPES.includes(type));
      },
      diagramTypes() {
        return VISUAL_TYPES;
      },
      canWrite() {
        return this.permissions.write === true && this.workflow.status !== 'archived';
      },
      canPublish() {
        return this.permissions.publish === true && this.workflow.status !== 'archived';
      },
      canDuplicate() {
        return this.permissions.duplicate === undefined ? this.canWrite : this.permissions.duplicate;
      },
      canArchive() {
        return this.permissions.archive === undefined ? this.canWrite : this.permissions.archive;
      },
      hasExecutableNodes() {
        return this.graph.nodes.some((node) => !isVisualNode(node) && node.type === 'trigger');
      },
      canSimulate() {
        return this.canWrite && Boolean(this.workflow.id) && this.hasExecutableNodes && !this.simulating;
      },
      selectedNode() {
        return this.graph.nodes.find((node) => node.id === this.selectedNodeId) || null;
      },
      saveStateLabel() {
        return { saved: 'Saved', unsaved: 'Unsaved changes', saving: 'Saving', error: 'Save failed' }[this.saveState] || 'Ready';
      },
      saveStateIcon() {
        return { saved: 'fa-check-circle', unsaved: 'fa-circle', saving: 'fa-sync-alt fa-spin', error: 'fa-exclamation-circle' }[this.saveState] || 'fa-circle';
      },
      worldStyle() {
        const viewport = this.graph.viewport || { x: 0, y: 0, zoom: 1 };
        return { transform: `translate(${viewport.x}px, ${viewport.y}px) scale(${viewport.zoom})` };
      },
      connectionPreviewPath() {
        if (!this.connecting || !this.connectionPoint) return '';
        const node = this.graph.nodes.find((item) => item.id === this.connecting.sourceId);
        if (!node) return '';
        const from = portPoint(node, this.connecting.port, 'source');
        const to = this.connectionPoint;
        const distance = Math.max(56, Math.abs(to.x - from.x) * 0.45);
        return `M ${from.x} ${from.y} C ${from.x + distance} ${from.y}, ${to.x - distance} ${to.y}, ${to.x} ${to.y}`;
      },
      selectedEdge() {
        return this.graph.edges.find((edge) => edge.id === this.selectedEdgeId) || null;
      },
      hasUnpublishedChanges() {
        return Boolean(this.workflow.published_version && Number(this.workflow.draft_revision || 0) > Number(this.workflow.published_revision || 0));
      },
      iconLibraryGroups() {
        return FONT_AWESOME_ICON_GROUPS;
      },
      filteredIconLibrary() {
        return filterIconLibrary(this.iconLibrary.icons, {
          group: this.iconPicker.group,
          query: this.iconPicker.query,
          limit: this.iconPicker.visibleCount,
        });
      },
      selectedTableCell() {
        const node = this.selectedNode;
        if (!node || node.type !== 'table' || this.tableSelection.nodeId !== node.id) return null;
        const config = tableConfig(node.config);
        return { row: Math.min(this.tableSelection.row, config.rows - 1), column: Math.min(this.tableSelection.column, config.columns - 1) };
      },
    },
    methods: {
      nodeMeta(type) {
        return NODE_CATALOG[type] || NODE_CATALOG.end;
      },
      isVisualType(type) {
        return VISUAL_TYPES.includes(type);
      },
      isVisualNode,
      statusClass(status) {
        return { published: 'text-bg-success', draft: 'text-bg-secondary', archived: 'text-bg-dark' }[status] || 'text-bg-secondary';
      },
      outputPorts(node) {
        return allowedPorts(node.type);
      },
      portLabel(port) {
        return { out: 'out', true: 'true', false: 'false', approved: 'approved', rejected: 'rejected' }[port] || port;
      },
      nodeStyle(node) {
        const size = nodeSize(node);
        return { left: `${Number(node.x) || 0}px`, top: `${Number(node.y) || 0}px`, width: `${size.width}px`, height: `${size.height}px` };
      },
      edgePath(edge) {
        const source = this.graph.nodes.find((node) => node.id === edge.source);
        const target = this.graph.nodes.find((node) => node.id === edge.target);
        return source && target ? bezierPath(source, target, edge.port) : '';
      },
      edgeLabel(edge) {
        const source = this.graph.nodes.find((node) => node.id === edge.source);
        const target = this.graph.nodes.find((node) => node.id === edge.target);
        return `${source?.label || edge.source} ${this.portLabel(edge.port)} ${target?.label || edge.target}`;
      },
      nodeAriaLabel(node) {
        return `${this.nodeMeta(node.type).label}: ${node.label}. ${this.nodeSummary(node)}`;
      },
      nodeSummary(node) {
        const config = node.config || {};
        switch (node.type) {
          case 'trigger': return config.event || 'manual';
          case 'condition': return config.field ? `${config.field} ${config.operator || 'equals'} ${config.value || ''}`.trim() : 'Configure condition';
          case 'approval': return config.assignee_role || (config.assignee_id ? `User ${config.assignee_id}` : 'Configure assignee');
          case 'delay': return `${config.minutes ?? 0} minutes`;
          case 'email': return config.to || 'Configure recipient';
          case 'webhook': return config.url || 'Configure URL';
          case 'update': return config.field ? `${config.field} = ${config.value || ''}` : 'Configure field';
          case 'table': return `${config.rows || 0} × ${config.columns || 0} table`;
          case 'icon': return config.iconClass || 'fas fa-star';
          case 'icon-list': return `${Array.isArray(config.items) ? config.items.length : 0} items`;
          default: return 'Workflow endpoint';
        }
      },
      safeIconClass(value, fallback = 'fas fa-star') {
        const raw = String(value || '').trim();
        return /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(raw) ? raw : fallback;
      },
      markDirty() {
        if (!this.ready || !this.canWrite) return;
        this.dirty = true;
        this.saveState = 'unsaved';
      },
      recordHistory() {
        if (!this.canWrite) return;
        this.history.push(cloneGraph(this.graph));
        if (this.history.length > 50) this.history.shift();
        this.future = [];
      },
      beginInspectorEdit() {
        if (!this.editorFieldSnapshot) {
          this.recordHistory();
          this.editorFieldSnapshot = true;
          window.setTimeout(() => { this.editorFieldSnapshot = false; }, 0);
        }
      },
      undo() {
        if (!this.history.length || !this.canWrite) return;
        this.future.push(cloneGraph(this.graph));
        this.graph = this.history.pop();
        this.selectedEdgeId = null;
        this.markDirty();
      },
      redo() {
        if (!this.future.length || !this.canWrite) return;
        this.history.push(cloneGraph(this.graph));
        this.graph = this.future.pop();
        this.selectedEdgeId = null;
        this.markDirty();
      },
      applyTemplate(key) {
        if (!this.canWrite) return;
        if (this.dirty && !window.confirm('Replace the current draft with this template? Unsaved changes will be lost.')) return;
        this.recordHistory();
        this.graph = createDefaultGraph(key);
        this.workflow.category = key === 'approval' ? 'approval' : key === 'form' ? 'form' : key === 'automation' ? 'automation' : 'general';
        this.selectedNodeId = null;
        this.selectedEdgeId = null;
        this.markDirty();
      },
      addNode(type, position = null) {
        if (!this.canWrite || !NODE_TYPES.includes(type)) return;
        this.recordHistory();
        const index = this.graph.nodes.length;
        const node = createNode(type, createId(type), position?.x ?? 110 + (index % 3) * 300, position?.y ?? 100 + Math.floor(index / 3) * 150);
        const size = nodeSize(node);
        while (!position && this.graph.nodes.some((item) => {
          const itemSize = nodeSize(item);
          return node.x < item.x + itemSize.width + 24 && node.x + size.width + 24 > item.x && node.y < item.y + itemSize.height + 24 && node.y + size.height + 24 > item.y;
        })) {
          node.y += size.height + 54;
        }
        this.graph.nodes.push(node);
        this.selectedNodeId = node.id;
        this.selectedEdgeId = null;
        this.markDirty();
      },
      normalizeTableNode(node) {
        return node?.config || {};
      },
      tableCellStyle(node, row, column) {
        const config = this.normalizeTableNode(node);
        return {
          width: `${config.columnWidths?.[column] || 120}px`,
          height: `${config.rowHeights?.[row] || 42}px`,
          color: config.textColor,
          backgroundColor: config.header && row === 0 ? config.headerBackground : config.background,
          borderColor: config.borderColor,
          fontSize: `${config.fontSize || 14}px`,
          textAlign: config.align || 'left',
        };
      },
      selectTableCell(event, node, row, column) {
        event.stopPropagation();
        this.selectNode(node.id);
        this.tableSelection = { nodeId: node.id, row, column };
      },
      tableSelectionFor(node) {
        const config = this.normalizeTableNode(node);
        const selection = this.tableSelection.nodeId === node.id ? this.tableSelection : { row: 0, column: 0 };
        return { row: Math.min(Math.max(0, selection.row), config.rows - 1), column: Math.min(Math.max(0, selection.column), config.columns - 1) };
      },
      setTableRows(value, node = this.selectedNode) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        const config = this.normalizeTableNode(node);
        const rows = Math.min(20, Math.max(1, Math.round(Number(value) || config.rows)));
        if (rows === config.rows) return;
        if (rows < config.rows && !window.confirm('Mengurangi row dapat menghapus isi di bagian bawah tabel. Lanjutkan?')) return;
        this.beginInspectorEdit();
        node.config = resizeTableGrid(config, rows, config.columns);
        this.tableSelection = { nodeId: node.id, row: Math.min(this.tableSelectionFor(node).row, rows - 1), column: this.tableSelectionFor(node).column };
        this.markDirty();
      },
      setTableColumns(value, node = this.selectedNode) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        const config = this.normalizeTableNode(node);
        const columns = Math.min(20, Math.max(1, Math.round(Number(value) || config.columns)));
        if (columns === config.columns) return;
        if (columns < config.columns && !window.confirm('Mengurangi column dapat menghapus isi di sisi kanan tabel. Lanjutkan?')) return;
        this.beginInspectorEdit();
        node.config = resizeTableGrid(config, config.rows, columns);
        this.tableSelection = { nodeId: node.id, row: this.tableSelectionFor(node).row, column: Math.min(this.tableSelectionFor(node).column, columns - 1) };
        this.markDirty();
      },
      insertTableRowAt(node, after = false) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        const config = this.normalizeTableNode(node);
        const row = this.tableSelectionFor(node).row;
        const index = Math.min(config.rows, Math.max(0, row + (after ? 1 : 0)));
        this.recordHistory();
        node.config = insertTableRow(config, index);
        this.tableSelection = { nodeId: node.id, row: Math.min(index, node.config.rows - 1), column: this.tableSelectionFor(node).column };
        this.markDirty();
      },
      deleteSelectedTableRow(node = this.selectedNode) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        const config = this.normalizeTableNode(node);
        const row = this.tableSelectionFor(node).row;
        if (config.rows <= 1) return;
        if (config.cells[row]?.some((cell) => String(cell || '').length > 0) && !window.confirm('Hapus row terpilih beserta isinya?')) return;
        this.recordHistory();
        node.config = deleteTableRow(config, row);
        this.tableSelection = { nodeId: node.id, row: Math.min(row, node.config.rows - 1), column: this.tableSelectionFor(node).column };
        this.markDirty();
      },
      insertTableColumnAt(node, after = false) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        const config = this.normalizeTableNode(node);
        const column = this.tableSelectionFor(node).column;
        const index = Math.min(config.columns, Math.max(0, column + (after ? 1 : 0)));
        this.recordHistory();
        node.config = insertTableColumn(config, index);
        this.tableSelection = { nodeId: node.id, row: this.tableSelectionFor(node).row, column: Math.min(index, node.config.columns - 1) };
        this.markDirty();
      },
      deleteSelectedTableColumn(node = this.selectedNode) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        const config = this.normalizeTableNode(node);
        const column = this.tableSelectionFor(node).column;
        if (config.columns <= 1) return;
        if (config.cells.some((row) => String(row?.[column] || '').length > 0) && !window.confirm('Hapus column terpilih beserta isinya?')) return;
        this.recordHistory();
        node.config = deleteTableColumn(config, column);
        this.tableSelection = { nodeId: node.id, row: this.tableSelectionFor(node).row, column: Math.min(column, node.config.columns - 1) };
        this.markDirty();
      },
      resizeTableNumber(node, kind, index, value) {
        if (!node || node.type !== 'table' || !this.canWrite) return;
        this.beginInspectorEdit();
        node.config = kind === 'column' ? resizeTableColumn(node.config, index, value) : resizeTableRow(node.config, index, value);
        this.markDirty();
      },
      startTableResize(event, node, kind, index = null) {
        if (!this.canWrite || event.button !== 0 || !node || node.type !== 'table') return;
        event.preventDefault();
        event.stopPropagation();
        this.recordHistory();
        const config = this.normalizeTableNode(node);
        const size = nodeSize(node);
        this.tableResize = { nodeId: node.id, kind, index, startX: event.clientX, startY: event.clientY, zoom: this.graph.viewport.zoom || 1, config: JSON.parse(JSON.stringify(config)), size };
        window.addEventListener('pointermove', this.moveTableResize);
        window.addEventListener('pointerup', this.endTableResize, { once: true });
        window.addEventListener('pointercancel', this.endTableResize, { once: true });
      },
      moveTableResize(event) {
        if (!this.tableResize) return;
        const state = this.tableResize;
        const node = this.graph.nodes.find((item) => item.id === state.nodeId);
        if (!node) return;
        const dx = (event.clientX - state.startX) / state.zoom;
        const dy = (event.clientY - state.startY) / state.zoom;
        if (state.kind === 'column') node.config = resizeTableColumn(state.config, state.index, state.config.columnWidths[state.index] + dx);
        else if (state.kind === 'row') node.config = resizeTableRow(state.config, state.index, state.config.rowHeights[state.index] + dy);
        else node.config = resizeTableTo(state.config, state.size.width + dx, state.size.height + dy);
        this.markDirty();
      },
      endTableResize() {
        window.removeEventListener('pointermove', this.moveTableResize);
        window.removeEventListener('pointerup', this.endTableResize);
        window.removeEventListener('pointercancel', this.endTableResize);
        this.tableResize = null;
      },
      addIconListItem(node = this.selectedNode) {
        if (!node || node.type !== 'icon-list' || !this.canWrite) return;
        const items = Array.isArray(node.config.items) ? node.config.items : [];
        if (items.length >= 30) return;
        this.recordHistory();
        items.push({ iconClass: 'fas fa-check', text: `Item ${items.length + 1}` });
        node.config.items = items;
        this.markDirty();
      },
      removeIconListItem(index, node = this.selectedNode) {
        if (!node || node.type !== 'icon-list' || !this.canWrite || node.config.items.length <= 1) return;
        if (String(node.config.items[index]?.text || '').trim() && !window.confirm('Hapus item Icon List terpilih?')) return;
        this.recordHistory();
        node.config.items.splice(index, 1);
        this.markDirty();
      },
      moveIconListItem(index, direction, node = this.selectedNode) {
        if (!node || node.type !== 'icon-list' || !this.canWrite) return;
        const target = index + direction;
        if (target < 0 || target >= node.config.items.length) return;
        this.recordHistory();
        const items = node.config.items;
        [items[index], items[target]] = [items[target], items[index]];
        this.markDirty();
      },
      async openIconPicker(node = this.selectedNode, itemIndex = null) {
        if (!this.canWrite || !node || !['icon', 'icon-list'].includes(node.type)) return;
        this.iconPicker = { open: true, group: 'all', query: '', visibleCount: 100, selected: null, target: { nodeId: node.id, itemIndex }, returnFocus: document.activeElement };
        const current = itemIndex === null ? node.config.iconClass : node.config.items?.[itemIndex]?.iconClass;
        this.$nextTick?.(() => {
          const dialog = this.$refs.iconDialog;
          if (dialog && !dialog.open) dialog.showModal();
          this.$refs.iconSearch?.focus();
        });
        if (!this.iconLibrary.loaded && !this.iconLibrary.loading) {
          this.iconLibrary.loading = true;
          this.iconLibrary.error = '';
          try {
            this.iconLibrary.icons = await loadFontAwesomeIconLibrary();
            this.iconLibrary.loaded = true;
          } catch (error) {
            this.iconLibrary.error = 'Library Font Awesome lokal tidak berhasil dimuat.';
          } finally {
            this.iconLibrary.loading = false;
          }
        }
        this.iconPicker.selected = this.iconLibrary.icons.find((item) => item.className === current) || null;
      },
      closeIconPicker() {
        const dialog = this.$refs.iconDialog;
        if (dialog?.open) dialog.close();
        const focusTarget = this.iconPicker.returnFocus;
        this.iconPicker.open = false;
        this.iconPicker.target = null;
        this.iconPicker.selected = null;
        if (focusTarget && typeof focusTarget.focus === 'function') window.setTimeout(() => focusTarget.focus(), 0);
      },
      selectIconPickerItem(item) {
        this.iconPicker.selected = item;
      },
      loadMoreIcons() {
        this.iconPicker.visibleCount += 100;
      },
      applyIconPicker() {
        const item = this.iconPicker.selected;
        const target = this.iconPicker.target;
        if (!item || !target || !this.canWrite) return;
        const node = this.graph.nodes.find((entry) => entry.id === target.nodeId);
        if (!node) return;
        this.beginInspectorEdit();
        if (node.type === 'icon') node.config.iconClass = item.className;
        else if (node.type === 'icon-list' && node.config.items?.[target.itemIndex]) node.config.items[target.itemIndex].iconClass = item.className;
        this.markDirty();
        this.closeIconPicker();
      },
      startPaletteDrag(event, type) {
        if (!this.canWrite) return;
        this.paletteClickSuppressedUntil = 0;
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('application/x-workflow-node', type);
        event.dataTransfer.setData('text/plain', type);
      },
      dropPalette(event) {
        const type = event.dataTransfer.getData('application/x-workflow-node') || event.dataTransfer.getData('text/plain');
        if (!NODE_TYPES.includes(type)) return;
        this.paletteClickSuppressedUntil = Date.now() + 300;
        this.addNode(type, this.screenToGraph(event.clientX, event.clientY));
      },
      endPaletteDrag() {
        this.paletteClickSuppressedUntil = Date.now() + 300;
      },
      addPaletteNode(type) {
        if (Date.now() < this.paletteClickSuppressedUntil) return;
        this.addNode(type);
      },
      selectNode(id) {
        if (this.connecting && this.connecting.sourceId !== id) {
          const target = this.graph.nodes.find((node) => node.id === id);
          if (isVisualNode(target)) {
            this.showNotice('Diagram elements cannot receive workflow connections.', 'warning');
            this.cancelConnection();
          } else {
            this.finishConnection(id);
            return;
          }
        }
        this.selectedNodeId = id;
        this.selectedEdgeId = null;
      },
      selectEdge(id) {
        this.selectedEdgeId = id;
        this.selectedNodeId = null;
      },
      clearSelection() {
        this.selectedNodeId = null;
        this.selectedEdgeId = null;
      },
      startConnection(event, node, port) {
        if (!this.canWrite || node.type === 'end' || isVisualNode(node)) return;
        const existing = this.graph.edges.find((edge) => edge.source === node.id && edge.port === port);
        if (existing && !window.confirm('Replace the existing connection for this port?')) return;
        this.connecting = { sourceId: node.id, port };
        this.connectionPoint = portPoint(node, port, 'source');
        this.selectedNodeId = node.id;
        this.selectedEdgeId = null;
        event.currentTarget?.focus();
      },
      finishConnection(targetId) {
        if (!this.connecting || !this.canWrite) return;
        if (targetId === this.connecting.sourceId) {
          this.showNotice('A node cannot connect to itself.', 'warning');
          this.cancelConnection();
          return;
        }
        const source = this.graph.nodes.find((node) => node.id === this.connecting.sourceId);
        const target = this.graph.nodes.find((node) => node.id === targetId);
        if (!source || !target || target.type === 'trigger' || isVisualNode(source) || isVisualNode(target)) {
          this.showNotice(isVisualNode(source) || isVisualNode(target) ? 'Diagram elements do not use workflow connections.' : 'Trigger cannot receive an input connection.', 'warning');
          this.cancelConnection();
          return;
        }
        this.recordHistory();
        this.graph.edges = this.graph.edges.filter((edge) => !(edge.source === source.id && edge.port === this.connecting.port));
        this.graph.edges.push({ id: createId('edge'), source: source.id, target: target.id, port: this.connecting.port });
        this.markDirty();
        this.cancelConnection();
      },
      cancelConnection() {
        this.connecting = null;
        this.connectionPoint = null;
      },
      removeNode(id) {
        if (!this.canWrite || !window.confirm('Delete this node and its connections?')) return;
        this.recordHistory();
        this.graph.nodes = this.graph.nodes.filter((node) => node.id !== id);
        this.graph.edges = this.graph.edges.filter((edge) => edge.source !== id && edge.target !== id);
        if (this.selectedNodeId === id) this.clearSelection();
        this.markDirty();
      },
      removeEdge(id) {
        if (!this.canWrite || !window.confirm('Delete this connection?')) return;
        this.recordHistory();
        this.graph.edges = this.graph.edges.filter((edge) => edge.id !== id);
        if (this.selectedEdgeId === id) this.clearSelection();
        this.markDirty();
      },
      deleteSelected() {
        if (this.selectedNodeId) this.removeNode(this.selectedNodeId);
        else if (this.selectedEdgeId) this.removeEdge(this.selectedEdgeId);
      },
      handleNodeKeydown(event, node) {
        if (event.target !== event.currentTarget) return;
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          this.selectNode(node.id);
          return;
        }
        if (event.key === 'Delete' || event.key === 'Backspace') {
          event.preventDefault();
          this.removeNode(node.id);
          return;
        }
        const delta = event.shiftKey ? 40 : 12;
        const movement = { ArrowLeft: [-delta, 0], ArrowRight: [delta, 0], ArrowUp: [0, -delta], ArrowDown: [0, delta] }[event.key];
        if (!movement || !this.canWrite) return;
        event.preventDefault();
        this.recordHistory();
        node.x = Number(node.x) + movement[0];
        node.y = Number(node.y) + movement[1];
        this.markDirty();
      },
      screenToGraph(clientX, clientY) {
        const rect = this.$refs.canvas?.getBoundingClientRect();
        const viewport = this.graph.viewport || { x: 0, y: 0, zoom: 1 };
        return { x: (clientX - (rect?.left || 0) - viewport.x) / viewport.zoom, y: (clientY - (rect?.top || 0) - viewport.y) / viewport.zoom };
      },
      beginNodeDrag(event, node) {
        if (!this.canWrite || event.button !== 0 || event.target.closest('.workflow-port, button, input, textarea, select, .workflow-table-resizer, .workflow-visual-control')) return;
        this.recordHistory();
        const point = this.screenToGraph(event.clientX, event.clientY);
        this.nodeDrag = { id: node.id, offsetX: point.x - node.x, offsetY: point.y - node.y };
        window.addEventListener('pointermove', this.moveNode);
        window.addEventListener('pointerup', this.endNodeDrag, { once: true });
      },
      moveNode(event) {
        if (!this.nodeDrag) return;
        const node = this.graph.nodes.find((item) => item.id === this.nodeDrag.id);
        if (!node) return;
        const point = this.screenToGraph(event.clientX, event.clientY);
        node.x = Math.round(point.x - this.nodeDrag.offsetX);
        node.y = Math.round(point.y - this.nodeDrag.offsetY);
        this.selectedNodeId = node.id;
        this.markDirty();
      },
      endNodeDrag() {
        window.removeEventListener('pointermove', this.moveNode);
        this.nodeDrag = null;
      },
      startPan(event) {
        if (event.button !== 0 || event.target.closest('.workflow-node, .workflow-edge, button')) return;
        this.panning = true;
        this.panState = { startX: event.clientX, startY: event.clientY, x: this.graph.viewport.x, y: this.graph.viewport.y };
        window.addEventListener('pointermove', this.movePan);
        window.addEventListener('pointerup', this.finishPan, { once: true });
      },
      movePan(event) {
        if (!this.panState) return;
        this.graph.viewport.x = this.panState.x + event.clientX - this.panState.startX;
        this.graph.viewport.y = this.panState.y + event.clientY - this.panState.startY;
      },
      finishPan() {
        window.removeEventListener('pointermove', this.movePan);
        this.panState = null;
        this.panning = false;
      },
      trackCanvasPointer(event) {
        if (this.connecting) this.connectionPoint = this.screenToGraph(event.clientX, event.clientY);
        this.canvasPointer = { x: event.clientX, y: event.clientY };
      },
      clearCanvasPointer() {
        if (this.connecting) this.connectionPoint = null;
        this.canvasPointer = null;
      },
      zoomAt(event) {
        const current = this.graph.viewport.zoom;
        const next = Math.min(2.5, Math.max(0.1, current * (event.deltaY > 0 ? 0.9 : 1.1)));
        if (next === current) return;
        const before = this.screenToGraph(event.clientX, event.clientY);
        this.graph.viewport.zoom = next;
        const rect = this.$refs.canvas?.getBoundingClientRect();
        this.graph.viewport.x = event.clientX - (rect?.left || 0) - before.x * next;
        this.graph.viewport.y = event.clientY - (rect?.top || 0) - before.y * next;
      },
      zoomIn() {
        this.graph.viewport.zoom = Math.min(2.5, Math.round((this.graph.viewport.zoom + 0.1) * 100) / 100);
      },
      zoomOut() {
        this.graph.viewport.zoom = Math.max(0.1, Math.round((this.graph.viewport.zoom - 0.1) * 100) / 100);
      },
      fitCanvas() {
        const rect = this.$refs.canvas?.getBoundingClientRect();
        const bounds = graphBounds(this.graph);
        const width = Math.max(320, rect?.width || 800);
        const height = Math.max(240, rect?.height || 560);
        const zoom = Math.min(1.2, Math.max(0.1, Math.min((width - 80) / bounds.width, (height - 80) / bounds.height)));
        this.graph.viewport.zoom = Number(zoom.toFixed(2));
        this.graph.viewport.x = Math.round((width - bounds.width * zoom) / 2 - bounds.minX * zoom);
        this.graph.viewport.y = Math.round((height - bounds.height * zoom) / 2 - bounds.minY * zoom);
      },
      resourceUrl(key) {
        const template = this.urls[key] || '';
        if (!this.workflow.id) return template;
        return String(template).replace(/\{workflow\}|\{id\}|__WORKFLOW__|:id/g, encodeURIComponent(this.workflow.id));
      },
      payload() {
        const data = { name: this.workflow.name || 'Untitled workflow', description: this.workflow.description || '', category: this.workflow.category || 'general', graph: cloneGraph(this.graph) };
        if (this.workflow.id) data.draft_revision = Number(this.workflow.draft_revision || 0);
        return data;
      },
      async save() {
        if (!this.canWrite || this.saving) return false;
        if (this.workflow.id && !this.dirty) return true;
        this.saving = true;
        this.saveState = 'saving';
        this.notice.message = '';
        try {
          const existingWorkflow = Boolean(this.workflow.id);
          const response = existingWorkflow ? await axios.put(this.resourceUrl('update'), this.payload()) : await axios.post(this.resourceUrl('store'), this.payload());
          const data = response.data?.data || response.data || {};
          this.workflow = { ...this.workflow, ...data, id: data.id || this.workflow.id, graph: undefined };
          this.graph = normalizeGraph(data.graph || this.graph);
          this.workflow.draft_revision = Number(data.draft_revision ?? this.workflow.draft_revision ?? 0);
          this.workflow.published_version = data.published_version ?? this.workflow.published_version;
          this.workflow.published_revision = Number(data.published_revision ?? this.workflow.published_revision ?? 0);
          this.workflow.updated_at = data.updated_at || this.workflow.updated_at;
          this.dirty = false;
          this.saveState = 'saved';
          if (!existingWorkflow && response.data?.edit_url) {
            const editUrl = response.data.edit_url;
            window.history.replaceState({}, '', editUrl);
            const updateUrl = String(editUrl).replace(/\/edit\/?$/, '');
            this.urls.update = updateUrl;
            this.urls.publish = `${updateUrl}/publish`;
            this.urls.duplicate = `${updateUrl}/duplicate`;
            this.urls.archive = `${updateUrl}/archive`;
            this.urls.simulate = `${updateUrl}/simulate`;
            this.urls.runs = `${updateUrl}/runs`;
          }
          this.showNotice(existingWorkflow ? 'Workflow saved.' : 'Workflow created and saved.', 'success');
          return true;
        } catch (error) {
          this.saveState = 'error';
          this.showNotice(this.errorMessage(error, 'Unable to save this workflow.'), 'danger');
          return false;
        } finally {
          this.saving = false;
        }
      },
      async publish() {
        if (!this.canPublish || this.publishing || !this.workflow.id) return;
        if (this.dirty && !(await this.save())) return;
        const validation = validatePublishGraph(this.graph);
        if (!validation.valid) {
          this.showNotice(validation.errors.slice(0, 6).join(' '), 'warning');
          return;
        }
        this.publishing = true;
        try {
          const response = await axios.post(this.resourceUrl('publish'), { draft_revision: Number(this.workflow.draft_revision || 0) });
          const data = response.data?.data || {};
          this.workflow = { ...this.workflow, ...data };
          this.showNotice('Workflow published.', 'success');
        } catch (error) {
          this.showNotice(this.errorMessage(error, 'Unable to publish this workflow.'), 'danger');
        } finally {
          this.publishing = false;
        }
      },
      async duplicate() {
        if (!this.canDuplicate || this.duplicating || !this.workflow.id) return;
        if (this.dirty && !(await this.save())) return;
        this.duplicating = true;
        try {
          const response = await axios.post(this.resourceUrl('duplicate'));
          if (response.data?.edit_url) window.location.href = response.data.edit_url;
          else this.showNotice('Workflow duplicated.', 'success');
        } catch (error) {
          this.showNotice(this.errorMessage(error, 'Unable to duplicate this workflow.'), 'danger');
        } finally {
          this.duplicating = false;
        }
      },
      async archive() {
        if (!this.canArchive || this.archiving || !this.workflow.id || !window.confirm('Archive this workflow? It will no longer appear as active.')) return;
        if (this.dirty && !(await this.save())) return;
        this.archiving = true;
        try {
          await axios.post(this.resourceUrl('archive'), { draft_revision: Number(this.workflow.draft_revision || 0) });
          window.location.href = this.urls.index || '/workflow';
        } catch (error) {
          this.showNotice(this.errorMessage(error, 'Unable to archive this workflow.'), 'danger');
        } finally {
          this.archiving = false;
        }
      },
      openSimulation() {
        document.getElementById('workflow-simulation')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      },
      async simulate() {
        if (!this.canSimulate) return;
        if (this.dirty && !(await this.save())) return;
        let context;
        try {
          context = JSON.parse(this.simulationContext || '{}');
        } catch {
          this.showNotice('Context JSON is not valid.', 'warning');
          return;
        }
        this.simulating = true;
        try {
          const response = await axios.post(this.resourceUrl('simulate'), { draft_revision: Number(this.workflow.draft_revision || 0), context });
          this.lastRun = response.data?.data || response.data;
          this.runsLoaded = false;
          this.showNotice('Simulation completed. No external integrations were called.', 'success');
        } catch (error) {
          this.showNotice(this.errorMessage(error, 'Unable to simulate this workflow.'), 'danger');
        } finally {
          this.simulating = false;
        }
      },
      async loadRuns(event) {
        if (event && !event.target.open) return;
        if (this.runsLoaded || this.runsLoading || !this.workflow.id || !this.resourceUrl('runs')) return;
        this.runsLoading = true;
        try {
          const response = await axios.get(this.resourceUrl('runs'));
          const payload = response.data || {};
          this.runs = Array.isArray(payload.data) ? payload.data : [];
          this.runsLoaded = true;
        } catch (error) {
          this.showNotice(this.errorMessage(error, 'Unable to load simulation runs.'), 'danger');
        } finally {
          this.runsLoading = false;
        }
      },
      logIcon(level) {
        return { success: 'fa-check-circle', warning: 'fa-exclamation-triangle', error: 'fa-times-circle' }[level] || 'fa-info-circle';
      },
      triggerImport() {
        this.$refs.importInput?.click();
      },
      async importGraph(event) {
        const file = event.target.files?.[0];
        event.target.value = '';
        if (!file || !this.canWrite) return;
        try {
          const parsed = JSON.parse(await file.text());
          const raw = parsed?.graph || parsed;
          const validation = validateGraphShape(raw);
          if (!validation.valid) {
            this.showNotice(`Invalid graph: ${validation.errors.slice(0, 5).join(' ')}`, 'warning');
            return;
          }
          const candidate = normalizeGraph(raw);
          if (this.dirty && !window.confirm('Replace the current draft with this imported graph?')) return;
          this.recordHistory();
          this.graph = candidate;
          this.selectedNodeId = null;
          this.selectedEdgeId = null;
          this.markDirty();
          this.showNotice('Graph imported. Save to persist it.', 'success');
        } catch {
          this.showNotice('The selected file is not valid JSON.', 'warning');
        }
      },
      exportGraph() {
        const data = { version: 1, workflow: { name: this.workflow.name, description: this.workflow.description, category: this.workflow.category }, graph: cloneGraph(this.graph) };
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `${(this.workflow.name || 'workflow').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'workflow'}.json`;
        link.click();
        URL.revokeObjectURL(link.href);
      },
      goBack() {
        if (this.dirty && !window.confirm('Leave without saving your changes?')) return;
        window.location.href = this.urls.index || '/workflow';
      },
      showNotice(message, type = 'danger') {
        this.notice = { message, type };
      },
      errorMessage(error, fallback) {
        if (error?.response?.status === 409) return error.response.data?.message || 'This workflow changed elsewhere. Reload it before saving again.';
        const payload = error?.response?.data;
        if (typeof payload?.message === 'string') return payload.message;
        if (payload?.errors && typeof payload.errors === 'object') return Object.values(payload.errors).flat().join(' ');
        return fallback;
      },
      formatDate(value) {
        if (!value) return '-';
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? String(value) : new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
      },
    },
    mounted() {
      this.ready = true;
      this.beforeUnloadHandler = (event) => {
        if (!this.dirty) return;
        event.preventDefault();
        event.returnValue = '';
      };
      window.addEventListener('beforeunload', this.beforeUnloadHandler);
      window.setTimeout(() => this.fitCanvas(), 0);
    },
    beforeUnmount() {
      window.removeEventListener('pointermove', this.moveNode);
      window.removeEventListener('pointermove', this.movePan);
      window.removeEventListener('pointermove', this.moveTableResize);
      window.removeEventListener('pointerup', this.endTableResize);
      window.removeEventListener('pointercancel', this.endTableResize);
      this.tableResize = null;
      window.removeEventListener('beforeunload', this.beforeUnloadHandler);
    },
  });

  window.WorkflowEditorVue = app.mount(root);
})();
