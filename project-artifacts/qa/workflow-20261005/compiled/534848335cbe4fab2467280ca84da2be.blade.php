@extends('workflow_qa::layout')

@php
    $workflowEditorBootstrap = $bootstrap ?? ['workflow' => null, 'urls' => ['index' => url('workflow'), 'store' => url('workflow'), 'update' => url('workflow'), 'publish' => url('workflow'), 'duplicate' => url('workflow'), 'archive' => url('workflow'), 'simulate' => url('workflow'), 'runs' => url('workflow')], 'permissions' => ['write' => false, 'publish' => false, 'duplicate' => false, 'archive' => false]];
@endphp

@section('title')
    {{ $qaTranslate('Workflow editor') }}
@endsection

@section('content')
    <div id="ph-app-workflow-editor" class="workflow-page workflow-editor-page" :class="{ 'workflow-editor--readonly': !canWrite }">
        <a class="visually-hidden-focusable workflow-skip-link" href="#workflow-canvas-main">{{ $qaTranslate('Skip to workflow canvas') }}</a>

        <header class="workflow-editor-header">
            <div class="workflow-editor-header__top">{{ Breadcrumbs::render('workflow.editor') }}</div>
            <div class="workflow-editor-header__main">
                <div class="workflow-editor-heading">
                    <a class="btn btn-sm ph-btn-theme-outline workflow-back-button" href="{{ $workflowEditorBootstrap['urls']['index'] ?? url('workflow') }}" @click.prevent="goBack" aria-label="{{ $qaTranslate('Back to workflows') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i><span class="d-none d-sm-inline ms-1">{{ $qaTranslate('Workflows') }}</span></a>
                    <div class="workflow-editor-heading__copy">
                        <span class="workflow-eyebrow">{{ $qaTranslate('Workflow editor') }}</span>
                        <input v-model.trim="workflow.name" @input="markDirty" @focus="beginInspectorEdit" class="workflow-name-input" type="text" maxlength="120" aria-label="{{ $qaTranslate('Workflow name') }}" :readonly="!canWrite" placeholder="{{ $qaTranslate('Untitled workflow') }}">
                        <div class="workflow-editor-heading__meta">
                            <span class="badge" :class="statusClass(workflow.status)">@{{ workflow.status || 'draft' }}</span>
                            <span v-if="workflow.published_version" class="workflow-published-label"><i class="fas fa-check-circle me-1" aria-hidden="true"></i>{{ $qaTranslate('Published') }} v@{{ workflow.published_version }}</span>
                            <span v-if="hasUnpublishedChanges" class="workflow-draft-label"><i class="fas fa-pencil-alt me-1" aria-hidden="true"></i>{{ $qaTranslate('Draft changes') }} v@{{ workflow.draft_revision }}</span>
                            <span class="workflow-save-state" :class="`workflow-save-state--${saveState}`" role="status" aria-live="polite"><i class="fas" :class="saveStateIcon" aria-hidden="true"></i>@{{ saveStateLabel }}</span>
                        </div>
                    </div>
                </div>
                <div class="workflow-editor-actions" role="group" aria-label="{{ $qaTranslate('Workflow actions') }}">
                    <button type="button" class="btn btn-sm ph-btn-theme-outline" :disabled="!canWrite || saving" @click="save"><span v-if="saving" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span><i v-else class="fas fa-save me-1" aria-hidden="true"></i>{{ $qaTranslate('Save') }}</button>
                    <button type="button" class="btn btn-sm ph-btn-theme" :disabled="!canPublish || publishing || !workflow.id" @click="publish"><span v-if="publishing" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span><i v-else class="fas fa-rocket me-1" aria-hidden="true"></i>{{ $qaTranslate('Publish') }}</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{{ $qaTranslate('More') }}</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button type="button" class="dropdown-item" :disabled="!canDuplicate || !workflow.id || duplicating" @click="duplicate"><i class="fas fa-copy fa-fw me-2" aria-hidden="true"></i>{{ $qaTranslate('Duplicate') }}</button></li>
                            <li><button type="button" class="dropdown-item" :disabled="!canWrite || !workflow.id || simulating" @click="openSimulation"><i class="fas fa-flask fa-fw me-2" aria-hidden="true"></i>{{ $qaTranslate('Simulate') }}</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button type="button" class="dropdown-item text-danger" :disabled="!canArchive || !workflow.id || archiving" @click="archive"><i class="fas fa-archive fa-fw me-2" aria-hidden="true"></i>{{ $qaTranslate('Archive') }}</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div v-if="notice.message" class="alert workflow-notice" :class="notice.type === 'success' ? 'alert-success' : (notice.type === 'warning' ? 'alert-warning' : 'alert-danger')" role="alert">
            <div class="d-flex gap-2 align-items-start"><i class="fas mt-1" :class="notice.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'" aria-hidden="true"></i><span class="flex-grow-1">@{{ notice.message }}</span><button type="button" class="btn-close" @click="notice.message = ''" aria-label="{{ $qaTranslate('Close') }}"></button></div>
        </div>

        <section class="ph-content rounded workflow-meta-panel mb-3" aria-label="{{ $qaTranslate('Workflow details') }}">
            <div class="workflow-meta-panel__fields">
                <div class="workflow-field workflow-field--name"><label for="workflow-description" class="form-label">{{ $qaTranslate('Description') }}</label><textarea id="workflow-description" v-model.trim="workflow.description" @input="markDirty" @focus="beginInspectorEdit" class="form-control" rows="2" maxlength="500" :readonly="!canWrite" placeholder="{{ $qaTranslate('Describe what this workflow is for') }}"></textarea></div>
                <div class="workflow-field"><label for="workflow-category" class="form-label">{{ $qaTranslate('Category') }}</label><select id="workflow-category" v-model="workflow.category" @change="markDirty" class="form-select" :disabled="!canWrite"><option value="general">{{ $qaTranslate('General') }}</option><option value="approval">{{ $qaTranslate('Approval') }}</option><option value="form">{{ $qaTranslate('Form') }}</option><option value="automation">{{ $qaTranslate('Automation') }}</option><option value="diagram">{{ $qaTranslate('Diagram') }}</option></select></div>
                <div class="workflow-meta-panel__tools">
                    <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="!canWrite" @click="triggerImport"><i class="fas fa-file-import me-1" aria-hidden="true"></i>{{ $qaTranslate('Import') }}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="exportGraph"><i class="fas fa-file-export me-1" aria-hidden="true"></i>{{ $qaTranslate('Export') }}</button>
                    <input ref="importInput" type="file" class="d-none" accept="application/json,.json" @change="importGraph">
                </div>
            </div>
            <div v-if="!workflow.id" class="workflow-template-row">
                <span class="workflow-template-row__label">{{ $qaTranslate('Start from a template') }}</span>
                <div class="workflow-template-row__choices" role="group" aria-label="{{ $qaTranslate('Workflow templates') }}">
                    <button v-for="template in templates" :key="template.key" type="button" class="workflow-template-choice" :disabled="!canWrite" @click="applyTemplate(template.key)"><i class="fas" :class="template.icon" aria-hidden="true"></i><span>@{{ template.label }}</span><small>@{{ template.description }}</small></button>
                </div>
            </div>
        </section>

        <main id="workflow-canvas-main" class="workflow-editor-layout" tabindex="-1">
            <aside class="workflow-toolbox ph-content rounded" aria-label="{{ $qaTranslate('Workflow toolbox') }}">
                <div class="workflow-panel-heading"><div><span class="workflow-panel-kicker">{{ $qaTranslate('Build') }}</span><h2 class="h6 mb-0">{{ $qaTranslate('Node palette') }}</h2></div><span class="workflow-panel-count">@{{ graph.nodes.length }}</span></div>
                <p class="small text-body-secondary workflow-toolbox__hint">{{ $qaTranslate('Drag a step to the canvas or use Add.') }}</p>
                <div class="workflow-palette">
                    <button v-for="type in nodeTypes" :key="type" type="button" class="workflow-palette-item" draggable="true" :disabled="!canWrite" @dragstart="startPaletteDrag($event, type)" @dragend="endPaletteDrag" @click="addPaletteNode(type)"><span class="workflow-palette-item__icon" :class="`workflow-tone--${nodeMeta(type).tone}`"><i class="fas" :class="nodeMeta(type).icon" aria-hidden="true"></i></span><span class="workflow-palette-item__copy"><strong>@{{ nodeMeta(type).label }}</strong><small>@{{ nodeMeta(type).description }}</small></span><span class="workflow-palette-item__add" aria-hidden="true"><i class="fas fa-plus"></i></span></button>
                </div>
                <div class="workflow-toolbox__footer"><span class="small text-body-secondary"><i class="fas fa-keyboard me-1" aria-hidden="true"></i>{{ $qaTranslate('Keyboard: arrows move selected node, Delete removes it.') }}</span></div>
            </aside>

            <section class="workflow-canvas-panel ph-content rounded" aria-labelledby="workflow-canvas-heading">
                <div class="workflow-canvas-toolbar">
                    <div class="workflow-canvas-toolbar__title"><span class="workflow-panel-kicker">{{ $qaTranslate('Canvas') }}</span><h2 id="workflow-canvas-heading" class="h6 mb-0">{{ $qaTranslate('Flow map') }}</h2></div>
                    <div class="workflow-canvas-toolbar__actions" role="group" aria-label="{{ $qaTranslate('Canvas controls') }}">
                        <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="history.length === 0" @click="undo" aria-label="{{ $qaTranslate('Undo') }}"><i class="fas fa-undo" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="future.length === 0" @click="redo" aria-label="{{ $qaTranslate('Redo') }}"><i class="fas fa-redo" aria-hidden="true"></i></button>
                        <span class="workflow-toolbar-divider" aria-hidden="true"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="zoomOut" aria-label="{{ $qaTranslate('Zoom out') }}"><i class="fas fa-minus" aria-hidden="true"></i></button>
                        <span class="workflow-zoom-label" aria-live="polite">@{{ Math.round(graph.viewport.zoom * 100) }}%</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="zoomIn" aria-label="{{ $qaTranslate('Zoom in') }}"><i class="fas fa-plus" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="fitCanvas" aria-label="{{ $qaTranslate('Fit') }}"><i class="fas fa-expand-arrows-alt me-1" aria-hidden="true"></i><span class="d-none d-md-inline">{{ $qaTranslate('Fit') }}</span></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" :disabled="!canWrite || (!selectedNodeId && !selectedEdgeId)" @click="deleteSelected" aria-label="{{ $qaTranslate('Delete') }}"><i class="fas fa-trash me-1" aria-hidden="true"></i><span class="d-none d-md-inline">{{ $qaTranslate('Delete') }}</span></button>
                    </div>
                </div>

                <div class="workflow-connection-banner" :class="{ 'is-active': connecting }" role="status" aria-live="polite">
                    <i class="fas" :class="connecting ? 'fa-link' : 'fa-info-circle'" aria-hidden="true"></i><span v-if="connecting">{{ $qaTranslate('Choose a target input to connect this step.') }}</span><span v-else>{{ $qaTranslate('Click an output port, then click a target input.') }}</span><button v-if="connecting" type="button" class="btn btn-sm btn-link p-0 ms-auto" @click="cancelConnection">{{ $qaTranslate('Cancel') }}</button>
                </div>

                <div ref="canvas" class="workflow-canvas" :class="{ 'is-panning': panning, 'is-connecting': connecting }" @dragover.prevent @drop="dropPalette" @pointerdown="startPan" @pointermove="trackCanvasPointer" @pointerup="finishPan" @pointerleave="clearCanvasPointer" @wheel.prevent="zoomAt" @contextmenu.prevent="cancelConnection" aria-describedby="workflow-canvas-help">
                    <div id="workflow-canvas-help" class="visually-hidden">{{ $qaTranslate('Workflow canvas. Use the palette Add buttons or drag steps here. Use keyboard controls on a selected node to move it.') }}</div>
                    <div v-if="graph.nodes.length === 0" class="workflow-canvas-empty"><span class="workflow-canvas-empty__icon" aria-hidden="true"><i class="fas fa-route"></i></span><strong>{{ $qaTranslate('Your canvas is empty') }}</strong><span>{{ $qaTranslate('Add a Trigger to begin building this workflow.') }}</span><button type="button" class="btn btn-sm ph-btn-theme" :disabled="!canWrite" @click="addNode('trigger')"><i class="fas fa-plus me-1" aria-hidden="true"></i>{{ $qaTranslate('Add Trigger') }}</button></div>
                    <div class="workflow-canvas-grid" aria-hidden="true"></div>
                    <div class="workflow-canvas-world" :style="worldStyle">
                        <svg class="workflow-edges" width="2400" height="1600" viewBox="0 0 2400 1600" aria-label="{{ $qaTranslate('Workflow connections') }}" role="img">
                            <defs><marker id="workflow-arrow" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z" fill="currentColor"></path></marker></defs>
                            <path v-for="edge in graph.edges" :key="edge.id" class="workflow-edge" :class="{ selected: selectedEdgeId === edge.id }" :d="edgePath(edge)" marker-end="url(#workflow-arrow)" tabindex="0" role="button" :aria-label="edgeLabel(edge)" @click.stop="selectEdge(edge.id)" @keydown.delete.prevent="removeEdge(edge.id)" @keydown.backspace.prevent="removeEdge(edge.id)"></path>
                            <path v-if="connectionPreviewPath" class="workflow-edge workflow-edge--preview" :d="connectionPreviewPath"></path>
                        </svg>
                        <div v-for="node in graph.nodes" :key="node.id" class="workflow-node" :class="[{ selected: selectedNodeId === node.id, 'is-source': connecting?.sourceId === node.id }, `workflow-node--${node.type}`]" :style="nodeStyle(node)" tabindex="0" role="button" :aria-label="nodeAriaLabel(node)" @pointerdown.stop="beginNodeDrag($event, node)" @click.stop="selectNode(node.id)" @keydown="handleNodeKeydown($event, node)">
                            <div class="workflow-node__header"><span class="workflow-node__icon" :class="`workflow-tone--${nodeMeta(node.type).tone}`"><i class="fas" :class="nodeMeta(node.type).icon" aria-hidden="true"></i></span><span class="workflow-node__type">@{{ nodeMeta(node.type).label }}</span><button v-if="canWrite" type="button" class="workflow-node__delete" @click.stop="removeNode(node.id)" :aria-label="'Delete ' + node.label"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            <div class="workflow-node__label">@{{ node.label }}</div>
                            <div class="workflow-node__summary">@{{ nodeSummary(node) }}</div>
                            <button v-if="node.type !== 'trigger'" type="button" class="workflow-port workflow-port--input" :class="{ active: connecting }" @click.stop="finishConnection(node.id)" :disabled="!connecting" :aria-label="'Connect to ' + node.label"><span aria-hidden="true"></span></button>
                            <button v-for="port in outputPorts(node)" :key="port" type="button" class="workflow-port workflow-port--output" :class="[`workflow-port--${port}`, { active: connecting?.sourceId === node.id && connecting.port === port }]" @click.stop="startConnection($event, node, port)" :disabled="!canWrite" :aria-label="'Start connection from ' + node.label + ' ' + port"><span class="workflow-port__label">@{{ portLabel(port) }}</span><span aria-hidden="true"></span></button>
                        </div>
                    </div>
                </div>
                <div class="workflow-canvas-footer"><span id="workflow-canvas-status" class="small text-body-secondary">@{{ graph.nodes.length }} {{ $qaTranslate('nodes') }} · @{{ graph.edges.length }} {{ $qaTranslate('connections') }}</span><span class="small text-body-secondary">{{ $qaTranslate('Scroll to pan, wheel to zoom, or use the controls.') }}</span></div>
            </section>

            <aside class="workflow-inspector ph-content rounded" aria-label="{{ $qaTranslate('Workflow inspector') }}">
                <div class="workflow-panel-heading"><div><span class="workflow-panel-kicker">{{ $qaTranslate('Configure') }}</span><h2 class="h6 mb-0">{{ $qaTranslate('Inspector') }}</h2></div><button v-if="selectedNode" type="button" class="btn btn-sm btn-link p-0" @click="clearSelection">{{ $qaTranslate('Close') }}</button></div>
                <div v-if="!selectedNode" class="workflow-inspector-empty"><span class="workflow-inspector-empty__icon" aria-hidden="true"><i class="fas fa-sliders-h"></i></span><strong>{{ $qaTranslate('Select a node') }}</strong><span>{{ $qaTranslate('Choose a step on the canvas to edit its label and configuration.') }}</span></div>
                <div v-else class="workflow-inspector__body">
                    <div class="workflow-inspector__node-title"><span class="workflow-node__icon" :class="`workflow-tone--${nodeMeta(selectedNode.type).tone}`"><i class="fas" :class="nodeMeta(selectedNode.type).icon" aria-hidden="true"></i></span><div><strong>@{{ nodeMeta(selectedNode.type).label }}</strong><small>@{{ selectedNode.id }}</small></div></div>
                    <div class="mb-3"><label for="workflow-node-label" class="form-label">{{ $qaTranslate('Label') }}</label><input id="workflow-node-label" v-model.trim="selectedNode.label" @input="markDirty" @focus="beginInspectorEdit" class="form-control" type="text" maxlength="120" :readonly="!canWrite"></div>
                    <div v-if="selectedNode.type === 'trigger'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Trigger settings') }}</h3><label for="workflow-trigger-event" class="form-label">{{ $qaTranslate('Event') }}</label><select id="workflow-trigger-event" v-model="selectedNode.config.event" @change="markDirty" class="form-select" :disabled="!canWrite"><option value="manual">{{ $qaTranslate('Manual') }}</option><option value="form.submitted">{{ $qaTranslate('Form submitted') }}</option><option value="data.created">{{ $qaTranslate('Data created') }}</option><option value="data.updated">{{ $qaTranslate('Data updated') }}</option></select><label for="workflow-trigger-form" class="form-label mt-3">{{ $qaTranslate('Form ID') }} <span class="text-body-secondary">({{ $qaTranslate('optional') }})</span></label><input id="workflow-trigger-form" v-model.trim="selectedNode.config.form_id" @input="markDirty" class="form-control" type="text" :disabled="!canWrite || selectedNode.config.event !== 'form.submitted'"></div>
                    <div v-if="selectedNode.type === 'condition'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Condition settings') }}</h3><label for="workflow-condition-field" class="form-label">{{ $qaTranslate('Field') }}</label><input id="workflow-condition-field" v-model.trim="selectedNode.config.field" @input="markDirty" @focus="beginInspectorEdit" class="form-control" type="text" :readonly="!canWrite"><label for="workflow-condition-operator" class="form-label mt-3">{{ $qaTranslate('Operator') }}</label><select id="workflow-condition-operator" v-model="selectedNode.config.operator" @change="markDirty" class="form-select" :disabled="!canWrite"><option value="eq">equals</option><option value="neq">does not equal</option><option value="gt">greater than</option><option value="gte">greater than or equal</option><option value="lt">less than</option><option value="lte">less than or equal</option><option value="contains">contains</option><option value="exists">exists</option></select><label for="workflow-condition-value" class="form-label mt-3">{{ $qaTranslate('Value') }}</label><input id="workflow-condition-value" v-model.trim="selectedNode.config.value" @input="markDirty" class="form-control" type="text" :disabled="!canWrite || selectedNode.config.operator === 'exists'"></div>
                    <div v-if="selectedNode.type === 'approval'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Approval settings') }}</h3><p class="small text-body-secondary">{{ $qaTranslate('Approval is simulated during a test run and is always marked approved.') }}</p><label for="workflow-approval-user" class="form-label">{{ $qaTranslate('Assignee user ID') }} <span class="text-body-secondary">({{ $qaTranslate('optional') }})</span></label><input id="workflow-approval-user" v-model.number="selectedNode.config.assignee_id" @input="markDirty" class="form-control" type="number" min="1" step="1" :readonly="!canWrite"><label for="workflow-approval-role" class="form-label mt-3">{{ $qaTranslate('Assignee role') }} <span class="text-body-secondary">({{ $qaTranslate('optional') }})</span></label><input id="workflow-approval-role" v-model.trim="selectedNode.config.assignee_role" @input="markDirty" class="form-control" type="text" :readonly="!canWrite"></div>
                    <div v-if="selectedNode.type === 'delay'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Delay settings') }}</h3><label for="workflow-delay-minutes" class="form-label">{{ $qaTranslate('Minutes') }}</label><input id="workflow-delay-minutes" v-model.number="selectedNode.config.minutes" @input="markDirty" class="form-control" type="number" min="0" max="43200" step="1" :readonly="!canWrite"><small class="form-text text-body-secondary">{{ $qaTranslate('Maximum 30 days.') }}</small></div>
                    <div v-if="selectedNode.type === 'email'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Email settings') }}</h3><p class="small text-body-secondary">{{ $qaTranslate('Simulation only. No email will be sent.') }}</p><label for="workflow-email-to" class="form-label">{{ $qaTranslate('To') }}</label><input id="workflow-email-to" v-model.trim="selectedNode.config.to" @input="markDirty" @focus="beginInspectorEdit" class="form-control" type="email" :readonly="!canWrite"><label for="workflow-email-subject" class="form-label mt-3">{{ $qaTranslate('Subject') }}</label><input id="workflow-email-subject" v-model.trim="selectedNode.config.subject" @input="markDirty" class="form-control" type="text" :readonly="!canWrite"></div>
                    <div v-if="selectedNode.type === 'webhook'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Webhook settings') }}</h3><p class="small text-body-secondary">{{ $qaTranslate('Simulation only. No external request will be sent.') }}</p><label for="workflow-webhook-url" class="form-label">{{ $qaTranslate('URL') }}</label><input id="workflow-webhook-url" v-model.trim="selectedNode.config.url" @input="markDirty" @focus="beginInspectorEdit" class="form-control" type="url" placeholder="https://example.test/hook" :readonly="!canWrite"><label for="workflow-webhook-method" class="form-label mt-3">{{ $qaTranslate('Method') }}</label><select id="workflow-webhook-method" v-model="selectedNode.config.method" @change="markDirty" class="form-select" :disabled="!canWrite"><option value="POST">POST</option></select></div>
                    <div v-if="selectedNode.type === 'update'" class="workflow-inspector-section"><h3>{{ $qaTranslate('Update settings') }}</h3><label for="workflow-update-field" class="form-label">{{ $qaTranslate('Field') }}</label><input id="workflow-update-field" v-model.trim="selectedNode.config.field" @input="markDirty" @focus="beginInspectorEdit" class="form-control" type="text" :readonly="!canWrite"><label for="workflow-update-value" class="form-label mt-3">{{ $qaTranslate('Value') }}</label><input id="workflow-update-value" v-model.trim="selectedNode.config.value" @input="markDirty" class="form-control" type="text" :readonly="!canWrite"></div>
                    <div v-if="selectedNode.type === 'end'" class="workflow-inspector-section"><h3>{{ $qaTranslate('End step') }}</h3><p class="small text-body-secondary mb-0">{{ $qaTranslate('This step completes the workflow. It does not have an output port.') }}</p></div>
                    <div class="workflow-inspector__coordinates"><span>{{ $qaTranslate('Position') }}</span><span>@{{ Math.round(selectedNode.x) }}, @{{ Math.round(selectedNode.y) }}</span></div>
                </div>
            </aside>
        </main>

        <section id="workflow-simulation" class="ph-content rounded workflow-simulation-panel mt-3" aria-labelledby="workflow-simulation-heading">
            <div class="workflow-simulation-panel__header"><div><span class="workflow-panel-kicker">{{ $qaTranslate('Test') }}</span><h2 id="workflow-simulation-heading" class="h6 mb-1">{{ $qaTranslate('Simulation') }}</h2><p class="small text-body-secondary mb-0">{{ $qaTranslate('Run the saved graph with sample context. Integrations remain simulated.') }}</p></div><button type="button" class="btn btn-sm ph-btn-theme" :disabled="!canWrite || !workflow.id || simulating" @click="simulate"><span v-if="simulating" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span><i v-else class="fas fa-play me-1" aria-hidden="true"></i>{{ $qaTranslate('Run simulation') }}</button></div>
            <div class="workflow-simulation-panel__body"><div class="workflow-simulation-context"><label for="workflow-simulation-context" class="form-label">{{ $qaTranslate('Context JSON') }}</label><textarea id="workflow-simulation-context" v-model="simulationContext" class="form-control font-monospace" rows="5" spellcheck="false">{}</textarea><small class="form-text text-body-secondary">{{ $qaTranslate('Example: {"amount": 120, "status": "new"}') }}</small></div><div class="workflow-simulation-result" aria-live="polite"><div v-if="!lastRun" class="workflow-simulation-empty"><i class="fas fa-flask mb-2" aria-hidden="true"></i><span>{{ $qaTranslate('No simulation run yet.') }}</span></div><div v-else><div class="workflow-run-summary"><span class="badge text-bg-success">@{{ lastRun.status }}</span><span class="workflow-run-mode">@{{ lastRun.mode }}</span><span class="small text-body-secondary">@{{ formatDate(lastRun.created_at || lastRun.updated_at) }}</span></div><ul class="workflow-run-logs list-unstyled mb-0"><li v-for="(log, index) in (lastRun.logs || [])" :key="`${log.node_id}-${index}`" :class="`workflow-log--${log.level || 'info'}`"><i class="fas" :class="logIcon(log.level)" aria-hidden="true"></i><span><strong>@{{ log.node_id }}</strong> @{{ log.message }}</span></li></ul></div></div></div>
            <details class="workflow-runs" @toggle="loadRuns"><summary>{{ $qaTranslate('Recent simulation runs') }} <span v-if="runs.length" class="badge text-bg-light ms-1">@{{ runs.length }}</span></summary><div v-if="runsLoading" class="small text-body-secondary p-3">{{ $qaTranslate('Loading runs') }}...</div><div v-else-if="runs.length === 0" class="small text-body-secondary p-3">{{ $qaTranslate('No saved runs') }}</div><div v-else class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>{{ $qaTranslate('Status') }}</th><th>{{ $qaTranslate('Mode') }}</th><th>{{ $qaTranslate('Logs') }}</th><th>{{ $qaTranslate('Created') }}</th></tr></thead><tbody><tr v-for="run in runs" :key="run.id"><td><span class="badge" :class="run.status === 'completed' ? 'text-bg-success' : 'text-bg-warning'">@{{ run.status }}</span></td><td>@{{ run.mode }}</td><td>@{{ (run.logs || []).length }}</td><td class="text-body-secondary">@{{ formatDate(run.created_at) }}</td></tr></tbody></table></div></details>
        </section>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ url('assets/css/workflow-editor.css?v=').time() }}">
@endpush

@push('js')
    <script>window.WorkflowEditorBootstrap = {{ Js::from($workflowEditorBootstrap) }};</script>
    <script type="module" src="{{ url('assets/js/vue3/workflow/workflow-editor.js?v=').time() }}"></script>
@endpush
