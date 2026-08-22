<template>
    <div
        class="pb-widget-settings pb-widget-settings--general-new pb-widget-settings--pro"
        :class="'pb-widget-settings--' + type"
    >
        <div class="pb-tab-nav">
            <button
                type="button"
                class="pb-tab-btn pb-tab-btn-icon"
                :class="{ active: editor.settingsTab === 'content' }"
                @click="editor.settingsTab = 'content'"
            >
                <i class="fas fa-edit"></i><span>Content</span></button
            ><button
                type="button"
                class="pb-tab-btn pb-tab-btn-icon"
                :class="{ active: editor.settingsTab === 'style' }"
                @click="editor.settingsTab = 'style'"
            >
                <i class="fas fa-adjust"></i><span>Style</span></button
            ><button
                type="button"
                class="pb-tab-btn pb-tab-btn-icon"
                :class="{ active: editor.settingsTab === 'advanced' }"
                @click="editor.settingsTab = 'advanced'"
            >
                <i class="fas fa-gear"></i><span>Advanced</span>
            </button>
        </div>

        <div v-if="editor.settingsTab === 'content'" class="pb-tab-content">
            

            

            

            

            
                <section-box title="List" :open="true"
                    ><repeater-list
                        :items="s.items"
                        item-label="List Item"
                        @add="addItem('items')"
                        @remove="removeItem('items', $event)"
                        @move="moveItem('items', $event)"
                        ><template #default="{ item }"
                            ><text-control
                                label="Title"
                                v-model="item.title" /><text-control
                                label="Price"
                                v-model="item.price" /><textarea-control
                                label="Description"
                                v-model="item.description" /><media-control
                                label="Image"
                                v-model="item.imageUrl"
                                :editor="editor"
                                :settings="item"
                                setting-key="imageUrl" /><link-field
                                label="Link"
                                :entry="item"
                                :editor="editor" /></template></repeater-list
                    ><select-control
                        label="Title HTML Tag"
                        v-model="s.titleTag"
                        :options="tagOptions" /><select-control
                        label="Description HTML Tag"
                        v-model="s.descriptionTag"
                        :options="tagOptions"
                /></section-box>
            

            

            

            

            

            

            

            

            

            

            
            
            

            
        </div>

        <div v-if="editor.settingsTab === 'style'" class="pb-tab-content">
            
            
            
            
            
                <section-box title="List" :open="true"
                    ><size-control
                        label="Rows Gap"
                        base="rowGap"
                        fallback="20px"
                        :node="node"
                        :editor="editor" /><select-control
                        label="Vertical Align"
                        v-model="s.verticalAlign"
                        :options="verticalOptions"
                /></section-box>
                <section-box title="Title"
                    ><color-control
                        label="Color"
                        v-model="s.titleColor" /><component
                        :is="editor.typographyControl"
                        prefix="priceListTitle"
                        :settings="s"
                        :responsive-device="editor.responsiveDevice"
                        :font-families="editor.fontFamilies"
                        font-size-mode-key="priceListTitleFontSizeMode"
                        @responsive-device="editor.setResponsiveDevice"
                /></section-box>
                <section-box title="Price"
                    ><color-control
                        label="Color"
                        v-model="s.priceColor" /><component
                        :is="editor.typographyControl"
                        prefix="priceListPrice"
                        :settings="s"
                        :responsive-device="editor.responsiveDevice"
                        :font-families="editor.fontFamilies"
                        @responsive-device="editor.setResponsiveDevice"
                /></section-box>
                <section-box title="Description"
                    ><color-control
                        label="Color"
                        v-model="s.descriptionColor" /><component
                        :is="editor.typographyControl"
                        prefix="priceListDescription"
                        :settings="s"
                        :responsive-device="editor.responsiveDevice"
                        :font-families="editor.fontFamilies"
                        @responsive-device="editor.setResponsiveDevice"
                /></section-box>
                <section-box title="Separator"
                    ><select-control
                        label="Style"
                        v-model="s.separatorStyle"
                        :options="separatorOptions" /><size-control
                        label="Weight"
                        base="separatorWeight"
                        fallback="1px"
                        :node="node"
                        :editor="editor" /><color-control
                        label="Color"
                        v-model="s.separatorColor" /><size-control
                        label="Spacing"
                        base="separatorSpacing"
                        fallback="8px"
                        :node="node"
                        :editor="editor"
                /></section-box>
                <section-box title="Image"
                    ><size-control
                        label="Size"
                        base="imageSize"
                        fallback="56px"
                        :node="node"
                        :editor="editor" /><size-control
                        label="Border Radius"
                        base="imageRadius"
                        fallback="4px"
                        :node="node"
                        :editor="editor" /><size-control
                        label="Spacing"
                        base="imageSpacing"
                        fallback="16px"
                        :node="node"
                        :editor="editor"
                /></section-box>
            
            
            
            
            
            
            
            
            
            
            
            
            
            
        </div>

        <div v-if="editor.settingsTab === 'advanced'" class="pb-tab-content">
            <component
                :is="editor.widgetAdvancedControls"
                :node="node"
                :responsive-device="editor.responsiveDevice"
                :show-display-conditions="true"
                :show-cache-settings="true"
                :elementor-choices="true"
                @responsive-device="editor.setResponsiveDevice"
                @choose-media="editor.chooseMedia(node.settings, $event)"
                @clear-media="editor.clearMedia(node.settings, $event)"
                @unavailable-ai="
                    editor.showUnsupportedControlNotice(
                        'Animate With AI',
                        'AI service is not connected to this page builder.',
                    )
                "
            />
        </div>

        
    </div>
</template>

<script>
const SectionBox = {
    props: { title: String, open: Boolean },
    template: `<details class="pb-collapsible" :open="open"><summary>{{title}}</summary><div class="pb-collapsible-body"><slot/></div></details>`,
};
const TextControl = {
    props: ["label", "modelValue", "inputType"],
    emits: ["update:modelValue"],
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><input class="pb-input" :type="inputType||'text'" :value="modelValue" @input="$emit('update:modelValue',$event.target.value)"></div>`,
};
const TextareaControl = {
    props: ["label", "modelValue"],
    emits: ["update:modelValue"],
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><textarea class="pb-textarea" rows="3" :value="modelValue" @input="$emit('update:modelValue',$event.target.value)"></textarea></div>`,
};
const NumberControl = {
    props: ["label", "modelValue", "min", "max", "step"],
    emits: ["update:modelValue"],
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><input class="pb-input" type="number" :min="min" :max="max" :step="step" :value="modelValue" @input="$emit('update:modelValue',Number($event.target.value))"></div>`,
};
const SelectControl = {
    props: ["label", "modelValue", "options"],
    emits: ["update:modelValue"],
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><select class="pb-select" :value="modelValue" @change="$emit('update:modelValue',$event.target.value)"><option v-for="option in options" :key="option.value" :value="option.value">{{option.label}}</option></select></div>`,
};
let toggleControlId = 0;
const ToggleControl = {
    props: ["label", "modelValue"],
    emits: ["update:modelValue"],
    data() {
        return { toggleId: `pro-toggle-${++toggleControlId}` };
    },
    template: `<div class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0" :for="toggleId">{{label}}</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="toggleId" class="pb-toggle" type="checkbox" :checked="modelValue" @change="$emit('update:modelValue',$event.target.checked)"><label :for="toggleId"></label></div><span class="pb-toggle-state">{{modelValue?'On':'Off'}}</span></div></div>`,
};
const ColorControl = {
    props: ["label", "modelValue"],
    emits: ["update:modelValue"],
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><input class="pb-input coloris pb-coloris-input" :value="modelValue" @input="$emit('update:modelValue',$event.target.value)"></div>`,
};
const MediaControl = {
    props: ["label", "modelValue", "editor", "settings", "settingKey"],
    emits: ["update:modelValue"],
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><div class="pb-bg-media-field pb-widget-settings__media-field" :class="{'has-image':!!modelValue}"><div class="pb-bg-media-preview" :style="modelValue?{backgroundImage:'url('+modelValue+')'}:{}"><button type="button" class="pb-bg-media-center-btn" :title="modelValue?'Change Image':'Choose Image'" @click="editor.chooseMedia(settings,settingKey)"><i :class="modelValue?'fas fa-pen':'fas fa-plus'"></i></button></div><div class="pb-bg-media-actions"><button type="button" class="pb-bg-media-choose" @click="editor.chooseMedia(settings,settingKey)">{{modelValue?'Change Image':'Choose Image'}}</button><button type="button" class="pb-bg-media-remove" :disabled="!modelValue" title="Remove Image" @click="$emit('update:modelValue','')"><i class="fas fa-trash-alt"></i></button></div></div></div>`,
};
const LinkField = {
        props: {
        label: { type: String, default: "Link" },
        entry: { type: Object, required: true },
        editor: { type: Object, required: true },
        urlKey: { type: String, default: "linkUrl" },
        metaPrefix: { type: String, default: "link" },
    },
    computed: {
        targetKey() {
            return this.metaPrefix + "Target";
        },
        nofollowKey() {
            return this.metaPrefix + "Nofollow";
        },
        attributesKey() {
            return this.metaPrefix + "CustomAttributes";
        },
    },
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><component :is="editor.linkControl" :url="entry[urlKey]||''" :target="entry[targetKey]||''" :nofollow="Boolean(entry[nofollowKey])" :custom-attributes="entry[attributesKey]||[]" @update:url="entry[urlKey]=$event" @update:target="entry[targetKey]=$event" @update:nofollow="entry[nofollowKey]=$event" @update:customAttributes="entry[attributesKey]=$event" /></div>`,
};
const ResponsiveMenu = {
    props: ["editor", "id"],
    template: `<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(id)" :title="'Responsive: '+editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id+'-'+device.value" type="button" class="pb-control-device-item" :class="{active:editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(id,device.value)"><i :class="device.icon"></i><span>{{editor.deviceOptionLabel(device)}}</span></button></div></div>`,
};
const ResponsiveSelect = {
    components: { ResponsiveMenu },
    props: ["label", "base", "controlId", "node", "editor", "options"],
    computed: {
        value() {
            return this.node.settings[this.editor.activeResponsiveKey(this.base)] || this.node.settings[this.base] || this.options?.[0]?.value || "";
        },
    },
    template: `<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><select class="pb-select" :value="value" @change="editor.setResponsiveSetting(node.settings,base,$event.target.value)"><option v-for="option in options" :key="base+'-'+option.value" :value="option.value">{{option.label}}</option></select></div>`,
};
const SizeControl = {
    components: { ResponsiveMenu },
    props: {
        label: String,
        base: String,
        fallback: String,
        node: Object,
        editor: Object,
        min: { type: Number, default: 0 },
        max: { type: Number, default: 600 },
        allowedUnits: { type: Array, default: () => ["px"] },
    },
    computed: {
        options() {
            return {
                fallback: this.fallback,
                min: this.min,
                max: this.max,
                allowedUnits: this.allowedUnits,
                fallbackUnit: this.allowedUnits[0] || "px",
            };
        },
    },
    template: `<div class="pb-form-group pb-pro-responsive-unit"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="'pro-'+base"/></div><div class="pb-range-value-row"><input class="pb-range" type="range" :min="min" :max="max" :value="editor.sizeControlDisplayValue(node,base,fallback,options)" @input="editor.onSizeControlInput(node,base,$event,options)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :value="editor.sizeControlDisplayValue(node,base,fallback,options)" @input="editor.onSizeControlInput(node,base,$event,options)"><select class="pb-mini-unit" :value="editor.sizeControlUnit(node,base,fallback,options)" @change="editor.setSizeControlUnit(node,base,$event.target.value,options)"><option v-for="unit in allowedUnits" :key="unit" :value="unit">{{unit}}</option></select></div></div></div>`,
};
const SidesControl = {
    components: { ResponsiveMenu },
    props: ["label", "base", "controlId", "node", "editor"],
    data() {
        return {
            linked: true,
            sides: ["Top", "Right", "Bottom", "Left"],
            units: ["px", "%", "em", "rem"],
        };
    },
    methods: {
        unit() {
            return this.editor.sizeControlUnit(this.node, this.base + "Top", "0px", {
                allowedUnits: this.units,
            });
        },
        value(side) {
            return this.editor.sizeControlDisplayValue(
                this.node,
                this.base + side,
                "0px",
                { allowedUnits: this.units },
            );
        },
        setSide(side, event) {
            const number = Number(event.target.value);
            const token = `${Number.isFinite(number) ? Math.max(0, number) : 0}${this.unit()}`;
            (this.linked ? this.sides : [side]).forEach((target) =>
                this.editor.setResponsiveSetting(this.node.settings, this.base + target, token),
            );
        },
        setUnit(event) {
            const next = this.units.includes(event.target.value) ? event.target.value : "px";
            this.sides.forEach((side) =>
                this.editor.setResponsiveSetting(
                    this.node.settings,
                    this.base + side,
                    `${this.value(side)}${next}`,
                ),
            );
        },
    },
    template: `<div class="pb-form-group pb-icon-box-sides-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><div class="pb-label-tools"><responsive-menu :editor="editor" :id="controlId"/><select class="pb-mini-unit" :value="unit()" @change="setUnit($event)"><option v-for="option in units" :key="base+'-'+option" :value="option">{{option}}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="side in sides" :key="side" class="pb-side-input"><input class="pb-input" type="number" min="0" :value="value(side)" @input="setSide(side,$event)"><span>{{side}}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:linked}" @click="linked=!linked" :title="linked?'Unlink values':'Link values'"><i :class="linked?'fas fa-link':'fas fa-unlink'"></i></button></div></div></div>`,
};
const ResponsiveNumber = {
    components: { ResponsiveMenu },
    props: ["label", "base", "node", "editor", "min", "max"],
    computed: {
        key() {
            return this.editor.activeResponsiveKey(this.base);
        },
    },
    template: `<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="'pro-'+base"/></div><input class="pb-input" type="number" :min="min" :max="max" v-model.number="node.settings[key]"></div>`,
};
const ResponsiveChoice = {
    components: { ResponsiveMenu },
    props: ["label", "base", "controlId", "node", "editor", "options"],
    computed: {
        settingKey() {
            return this.editor.activeResponsiveKey(this.base);
        },
        value() {
            return this.node.settings[this.settingKey] || this.node.settings[this.base] || this.options?.[0]?.value || "";
        },
    },
    template: `<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-btn-group pb-compact-choice"><button v-for="option in options" :key="base+'-'+option.value" type="button" class="pb-seg-btn" :class="{active:value===option.value}" :title="option.label" :aria-label="option.label" @click="editor.setResponsiveSetting(node.settings,base,option.value)"><i :class="option.icon"></i><span class="sr-only">{{option.label}}</span></button></div></div>`,
};
const RepeaterList = {
    props: {
        items: { type: Array, default: () => [] },
        itemLabel: { type: String, default: "Item" },
        duplicate: { type: Boolean, default: false },
        reorder: { type: Boolean, default: true },
    },
    emits: ["add", "duplicate", "remove", "move"],
    data() {
        return { expandedIndex: 0 };
    },
    methods: {
        toggle(index) {
            this.expandedIndex = this.expandedIndex === index ? -1 : index;
        },
    },
    template: `<div class="pb-pro-repeater"><div v-for="(item,index) in items" :key="item.id||index" class="pb-pro-repeater__item" :class="{'is-open':expandedIndex===index}"><div class="pb-pro-repeater__header" role="button" tabindex="0" :aria-expanded="expandedIndex===index?'true':'false'" @click="toggle(index)" @keydown.enter.prevent="toggle(index)" @keydown.space.prevent="toggle(index)"><button type="button" class="pb-pro-repeater__disclosure" :title="expandedIndex===index?'Collapse '+itemLabel:'Expand '+itemLabel" :aria-label="expandedIndex===index?'Collapse '+itemLabel:'Expand '+itemLabel" @click.stop="toggle(index)"><i class="fas" :class="expandedIndex===index?'fa-chevron-up':'fa-chevron-down'"></i></button><span class="pb-pro-repeater__label"><i class="fas fa-grip-vertical" aria-hidden="true"></i><strong>{{item.label||item.name||item.title||item.text||itemLabel+' #'+(index+1)}}</strong></span><span class="pb-pro-repeater__summary-actions"><button v-if="reorder" type="button" title="Move Up" aria-label="Move item up" :disabled="index===0" @click.stop="$emit('move',{index,direction:-1})"><i class="fas fa-arrow-up"></i></button><button v-if="reorder" type="button" title="Move Down" aria-label="Move item down" :disabled="index===items.length-1" @click.stop="$emit('move',{index,direction:1})"><i class="fas fa-arrow-down"></i></button><button v-if="duplicate" type="button" title="Duplicate" aria-label="Duplicate item" @click.stop="$emit('duplicate',index)"><i class="far fa-copy"></i></button><button type="button" title="Remove" aria-label="Remove item" :disabled="items.length<=1" @click.stop="$emit('remove',index)"><i class="fas fa-times"></i></button></span></div><div v-show="expandedIndex===index" class="pb-pro-repeater__body"><slot :item="item" :index="index"/></div></div><button type="button" class="pb-pro-repeater__add" @click="$emit('add')"><i class="fas fa-plus"></i> Add Item</button></div>`,
};
const FormRowGridEditor = {
    props: {
        layout: { type: Object, default: () => ({ steps: [] }) },
        nodeId: { type: String, default: "" },
        editor: { type: Object, default: () => ({}) },
        buttonWidth: { type: [String, Number], default: "100" },
    },
    emits: ["sync", "remove-field", "update:button-width"],
    data() {
        return {
            device: "desktop",
            expandedItemId: "",
            expandedRowId: "",
        };
    },
    computed: {
        api() {
            return window.PageBuilderElementorV24FormRowGrid || {};
        },
        layoutSteps() {
            return Array.isArray(this.layout?.steps) ? this.layout.steps : [];
        },
        normalizedButtonWidth() {
            return String(Math.max(20, Math.min(100, Number(this.buttonWidth) || 100)));
        },
        fieldEditRequest() {
            return this.editor?.formFieldEditRequest || null;
        },
    },
    mounted() {
        for (const step of this.layoutSteps) {
            for (const row of step.rows || []) {
                if (!this.expandedRowId) this.expandedRowId = String(row.id);
                const item = (row.columns || []).flatMap((column) => column.items || []).find((entry) => entry?.kind === "field");
                if (item) {
                    this.expandedItemId = String(item.id);
                    return;
                }
            }
        }
    },
    watch: {
        fieldEditRequest: {
            immediate: true,
            deep: true,
            handler(request) {
                this.applyFieldEditRequest(request);
            },
        },
    },
    methods: {
        applyFieldEditRequest(request) {
            if (!request || String(request.nodeId || "") !== String(this.nodeId || "")) return false;
            const itemId = String(request.itemId || "");
            const fieldId = String(request.fieldId || "");
            for (const step of this.layout?.steps || []) {
                for (const row of step?.rows || []) {
                    for (const column of row?.columns || []) {
                        const item = (column?.items || []).find((entry) => entry?.kind === "field" && (
                            (itemId && String(entry.id) === itemId)
                            || (fieldId && String(entry.field?.id) === fieldId)
                        ));
                        if (!item) continue;
                        this.expandedRowId = String(row.id);
                        this.expandedItemId = String(item.id);
                        this.$nextTick?.(() => {
                            const cards = Array.from(this.$el?.querySelectorAll?.("[data-form-field-item-id]") || []);
                            cards.find((card) => String(card.dataset?.formFieldItemId || "") === String(item.id))
                                ?.scrollIntoView?.({ block: "nearest" });
                        });
                        return true;
                    }
                }
            }
            return false;
        },
        toggleRow(rowId) {
            const id = String(rowId || "");
            this.expandedRowId = this.expandedRowId === id ? "" : id;
        },
        toggleItem(itemId) {
            const id = String(itemId || "");
            this.expandedItemId = this.expandedItemId === id ? "" : id;
        },
        itemTypeLabel(item) {
            const type = String(item?.field?.type || "Field");
            return type.charAt(0).toUpperCase() + type.slice(1).replaceAll("_", " ");
        },
        rowItems(row) {
            const columns = row?.columns || [];
            const length = Math.max(0, ...columns.map((column) => (column.items || []).length));
            const records = [];
            for (let itemIndex = 0; itemIndex < length; itemIndex += 1) {
                columns.forEach((column) => {
                    const item = column.items?.[itemIndex];
                    if (item?.kind === "field") records.push({ item, column });
                });
            }
            return records;
        },
        columnLabel(row, column) {
            const columns = row?.columns || [];
            const index = columns.findIndex((entry) => String(entry.id) === String(column?.id));
            return `Column ${Math.max(0, index) + 1}`;
        },
        addField(step, row) {
            if (!this.api.createField || !this.api.fieldItem || !this.api.appendFieldToRow) return;
            const field = this.api.createField({
                id: `field-${Date.now()}`,
                label: "New Field",
                type: "text",
            });
            const item = this.api.fieldItem(field);
            if (!this.api.appendFieldToRow(this.layout, step.id, row.id, item)) return;
            this.expandedRowId = String(row.id);
            this.$emit("sync");
        },
        addStep() {
            const step = this.api.appendStep?.(this.layout, {
                title: `Step ${this.layoutSteps.length + 1}`,
            });
            if (!step) return;
            this.expandedRowId = String(step.rows?.[0]?.id || "");
            this.expandedItemId = "";
            this.$emit("sync");
        },
        deleteStep(step) {
            if (!this.api.deleteStep?.(this.layout, step.id)) return;
            this.expandedRowId = String(this.layoutSteps[0]?.rows?.[0]?.id || "");
            this.expandedItemId = "";
            this.$emit("sync");
        },
        addRow(step) {
            if (!this.api.createRow) return;
            const row = this.api.createRow();
            step.rows.push(row);
            this.expandedRowId = String(row.id);
            this.$emit("sync");
        },
        deleteRow(step, row) {
            if (!this.api.deleteRow?.(this.layout, step.id, row.id)) return;
            if (this.expandedRowId === String(row.id)) {
                this.expandedRowId = String(step.rows?.[0]?.id || "");
            }
            this.$emit("sync");
        },
        setColumnCount(row, device, value) {
            row.columnCounts ||= { desktop: 1, tablet: 1, mobile: 1 };
            row.columnCounts[device] = Math.min(4, Math.max(1, Number(value) || 1));
            this.api.ensureColumns?.(row);
            this.$emit("sync");
        },
        setRowSpan(field, value) {
            field.rowSpan ||= { desktop: 1, tablet: 1, mobile: 1 };
            field.rowSpan[this.device] = Math.min(4, Math.max(1, Number(value) || 1));
            this.$emit("sync");
        },
        cycleRowSpanDevice() {
            const devices = ["desktop", "tablet", "mobile"];
            this.device = devices[(devices.indexOf(this.device) + 1) % devices.length];
        },
        rowSpanDeviceIcon() {
            return {
                desktop: "fas fa-desktop",
                tablet: "fas fa-tablet-alt",
                mobile: "fas fa-mobile-alt",
            }[this.device];
        },
        itemLabel(item) {
            return item?.field?.label || item?.field?.id || "Field";
        },
        setButtonWidth(value) {
            const width = String(Math.max(20, Math.min(100, Number(value) || 100)));
            this.$emit("update:button-width", width);
        },
    },
    template: `
        <div class="pb-form-row-grid-editor">
            <div class="pb-form-row-grid-editor__toolbar">
                <strong>Row Grid</strong>
                <div class="pb-form-row-grid-editor__devices" aria-label="Responsive layout device">
                    <button
                        v-for="option in [{value:'desktop',label:'Desktop'},{value:'tablet',label:'Tablet'},{value:'mobile',label:'Mobile'}]"
                        :key="option.value"
                        type="button"
                        :class="{active:device===option.value}"
                        @click="device=option.value"
                    >{{ option.label }}</button>
                </div>
            </div>

            <div v-for="(step,stepIndex) in layoutSteps" :key="step.id" class="pb-form-row-grid-editor__step">
                <button
                    v-if="layoutSteps.length>1"
                    type="button"
                    class="pb-form-row-grid-editor__step-delete"
                    :title="'Delete Step ' + (stepIndex + 1)"
                    :aria-label="'Delete Step ' + (stepIndex + 1)"
                    @click="deleteStep(step)"
                ><i class="far fa-trash-alt"></i></button>
                <details class="pb-form-row-grid-editor__step-settings" :class="{'has-delete':layoutSteps.length>1}">
                    <summary>
                        <span>
                            <strong>Step {{ stepIndex + 1 }}</strong>
                            <small v-if="step.title">{{ step.title }}</small>
                        </span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </summary>
                    <div class="pb-form-row-grid-editor__step-content">
                        <input class="pb-input" v-model="step.title" placeholder="Step title" @input="$emit('sync')"/>
                        <textarea class="pb-textarea" rows="2" v-model="step.description" placeholder="Step description" @input="$emit('sync')"></textarea>
                        <input class="pb-input" v-model="step.nextButton" placeholder="Next button" @input="$emit('sync')"/>
                        <input class="pb-input" v-model="step.previousButton" placeholder="Previous button" @input="$emit('sync')"/>
                        <slot name="step" :step="step"/>
                    </div>
                </details>

                <section v-for="(row,rowIndex) in step.rows" :key="row.id" class="pb-form-row-grid-editor__row">
                    <div class="pb-form-row-grid-editor__row-heading">
                        <button
                            type="button"
                            class="pb-form-row-grid-editor__row-toggle"
                            :aria-expanded="expandedRowId===String(row.id)"
                            @click="toggleRow(row.id)"
                        >
                            <i class="fas fa-th-large" aria-hidden="true"></i>
                            <strong>Row {{ rowIndex + 1 }}</strong>
                            <i
                                class="fas pb-form-row-grid-editor__row-chevron"
                                :class="expandedRowId===String(row.id)?'fa-chevron-up':'fa-chevron-down'"
                                aria-hidden="true"
                            ></i>
                        </button>
                        <select
                            class="pb-form-row-grid-editor__row-count"
                            :value="row.columnCounts[device]"
                            :aria-label="'Columns for ' + device"
                            @change="setColumnCount(row,device,$event.target.value)"
                        >
                            <option v-for="count in [1,2,3,4]" :key="count" :value="count">
                                {{ count }} {{ count === 1 ? 'column' : 'columns' }}
                            </option>
                        </select>
                        <button
                            type="button"
                            class="pb-form-row-grid-editor__delete"
                            title="Delete row"
                            aria-label="Delete row"
                            :disabled="step.rows.length<=1"
                            @click="deleteRow(step,row)"
                        ><i class="far fa-trash-alt"></i></button>
                    </div>

                    <div
                        v-show="expandedRowId===String(row.id)"
                        class="pb-form-row-grid-editor__row-body"
                    >
                        <div class="pb-form-row-grid-editor__subheading">Fields in this row</div>
                        <div class="pb-form-row-grid-editor__field-list">
                            <div
                                v-for="record in rowItems(row)"
                                :key="record.item.id"
                                class="pb-form-row-grid-editor__field-list-item"
                                :data-form-field-item-id="record.item.id"
                                :class="{'is-open':expandedItemId===String(record.item.id)}"
                            >
                                <div class="pb-form-row-grid-editor__field-header">
                                    <button
                                        type="button"
                                        class="pb-form-row-grid-editor__field-select"
                                        :aria-expanded="expandedItemId===String(record.item.id)"
                                        @click="toggleItem(record.item.id)"
                                    >
                                        <i
                                            class="fas pb-form-row-grid-editor__field-chevron"
                                            :class="expandedItemId===String(record.item.id)?'fa-chevron-up':'fa-chevron-down'"
                                            aria-hidden="true"
                                        ></i>
                                        <strong>{{ itemLabel(record.item) }}</strong>
                                        <span class="pb-form-row-grid-editor__type">{{ itemTypeLabel(record.item) }}</span>
                                        <span class="pb-form-row-grid-editor__column-badge">{{ columnLabel(row,record.column) }}</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="pb-form-row-grid-editor__remove"
                                        :title="'Remove ' + itemLabel(record.item)"
                                        :aria-label="'Remove ' + itemLabel(record.item)"
                                        @click.stop="$emit('remove-field',record.item.id)"
                                    ><i class="fas fa-times"></i></button>
                                </div>
                                <div
                                    v-show="expandedItemId===String(record.item.id)"
                                    class="pb-form-row-grid-editor__field-body"
                                >
                                    <slot
                                        name="field"
                                        :item="record.item.field"
                                        :item-record="record.item"
                                        :device="device"
                                        :set-row-span="setRowSpan"
                                        :cycle-row-span-device="cycleRowSpanDevice"
                                        :row-span-device-icon="rowSpanDeviceIcon()"
                                    />
                                </div>
                            </div>
                            <div v-if="!rowItems(row).length" class="pb-form-row-grid-editor__field-list-empty">
                                No fields in this row
                            </div>
                        </div>
                        <button
                            type="button"
                            class="pb-form-row-grid-editor__row-add"
                            @click="addField(step,row)"
                        ><i class="fas fa-plus"></i> Add Field</button>
                    </div>
                </section>

                <div v-if="stepIndex===layoutSteps.length-1" class="pb-form-row-grid-editor__form-actions">
                    <i class="far fa-paper-plane" aria-hidden="true"></i>
                    <span><strong>Submit button</strong><small>Final step footer</small></span>
                    <select
                        class="pb-form-row-grid-editor__footer-width"
                        aria-label="Submit button width"
                        :value="normalizedButtonWidth"
                        @change="setButtonWidth($event.target.value)"
                    >
                        <option v-for="width in [20,25,30,33,40,50,60,66,70,75,80,100]" :key="width" :value="width">{{ width }}%</option>
                    </select>
                </div>

                <div class="pb-form-row-grid-editor__actions is-single">
                    <button type="button" @click="addRow(step)">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
            </div>
            <div class="pb-form-row-grid-editor__actions is-single pb-form-row-grid-editor__add-step">
                <button type="button" @click="addStep">
                    <i class="fas fa-plus"></i> Add Step
                </button>
            </div>
        </div>
    `,
};
const ProIconPicker = {
    props: {
        label: { type: String, default: "Icon" },
        prefix: { type: String, required: true },
        targetKey: { type: String, required: true },
        itemId: { type: [String, Number], default: "" },
        entry: { type: Object, required: true },
        node: { type: Object, required: true },
        editor: { type: Object, required: true },
        fallbackSource: { type: String, default: "library" },
        fallbackClass: { type: String, default: "" },
        fallbackName: { type: String, default: "" },
        fallbackStyle: { type: String, default: "solid" },
    },
    computed: {
        classKey() {
            return this.prefix + "Class";
        },
        sourceKey() {
            return this.prefix + "Source";
        },
        styleKey() {
            return this.prefix + "Style";
        },
        nameKey() {
            return this.prefix + "Name";
        },
        svgKey() {
            return this.prefix + "Svg";
        },
        source() {
            return this.entry[this.sourceKey] || this.fallbackSource;
        },
        previewClass() {
            return this.entry[this.classKey] || this.fallbackClass || "far fa-circle";
        },
        iconLabel() {
            if (this.source === "svg") return "Uploaded SVG";
            const name = this.entry[this.nameKey] || this.fallbackName;
            if (!name) return "No Icon";
            return String(name).replace(/(^|[-_\s])(\w)/g, (_, space, letter) =>
                space + letter.toUpperCase(),
            );
        },
        styleLabel() {
            if (this.source === "svg") return "Custom SVG";
            if (this.source === "none") return "None";
            return this.editor.fontAwesomeStyleLabel(
                this.entry[this.styleKey] || this.fallbackStyle,
            );
        },
    },
    methods: {
        reset() {
            this.entry[this.sourceKey] = this.fallbackSource;
            this.entry[this.classKey] = this.fallbackClass;
            this.entry[this.nameKey] = this.fallbackName;
            this.entry[this.styleKey] = this.fallbackStyle;
            this.entry[this.svgKey] = "";
        },
    },
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><div class="pb-pro-icon-picker"><button type="button" class="pb-icon-picker-field" title="Open Icon Library" @click="editor.openProIconLibrary(targetKey,itemId,node)"><div class="pb-icon-picker-preview"><i :class="source==='svg'?'fas fa-file-code':previewClass"></i></div><div class="pb-icon-picker-copy"><div class="pb-icon-picker-name">{{iconLabel}}</div><div class="pb-icon-picker-style">{{styleLabel}}</div></div><i class="fas fa-chevron-right"></i></button><div class="pb-pro-icon-picker__actions"><button type="button" title="Upload SVG" aria-label="Upload SVG" @click="editor.chooseProIconSvg(targetKey,itemId,node)"><i class="fas fa-upload"></i></button><button type="button" title="Reset Icon" aria-label="Reset Icon" @click="reset"><i class="fas fa-undo-alt"></i></button></div></div></div>`,
};
const ArrowIconPicker = {
    props: ["label", "settingKey", "node", "editor", "fallback"],
    computed: {
        value() { return String(this.node.settings?.[this.settingKey] || this.fallback); },
        source() { return this.node.settings?.[this.settingKey + "Source"] === "svg" ? "svg" : "library"; },
        svgMarkup() { return String(this.node.settings?.[this.settingKey + "Svg"] || "").trim(); },
        svgDataUri() { return this.svgMarkup.startsWith("<svg") ? "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(this.svgMarkup) : ""; },
    },
    methods: {
        reset() {
            this.node.settings[this.settingKey] = this.fallback;
            this.node.settings[this.settingKey + "Source"] = "library";
            this.node.settings[this.settingKey + "Svg"] = "";
        },
    },
    template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><div class="pb-image-carousel-icon-picker"><button type="button" class="pb-image-carousel-icon-picker__button" :class="{'is-current':source==='library'&&value===fallback}" title="Default" @click="reset"><i :class="fallback"></i></button><button type="button" class="pb-image-carousel-icon-picker__button" title="Upload SVG" @click="editor.chooseImageCarouselArrowSvg(settingKey,node)"><i class="fas fa-upload"></i></button><button type="button" class="pb-image-carousel-icon-picker__button" :class="{'is-current':source==='library'&&value!==fallback}" title="Icon Library" @click="editor.openImageCarouselArrowIconLibrary(settingKey,node)"><img v-if="source==='svg'&&svgDataUri" :src="svgDataUri" alt=""><i v-else :class="value"></i></button></div></div>`,
};
const FormDatasetManager = {
    name: "FormDatasetManager",
    props: {
        open: { type: Boolean, default: false },
        field: { type: Object, default: null },
        fields: { type: Array, default: () => [] },
        datasets: { type: Array, default: () => [] },
        endpoints: { type: Object, default: () => ({}) },
        notice: { type: String, default: "" },
        serverError: { type: String, default: "" },
        loading: { type: Boolean, default: false },
    },
    emits: ["close", "save", "deleted", "clear-notice"],
    components: { TextControl, SelectControl },
    data() {
        return {
            mode: "visual",
            draftField: null,
            draftDataset: null,
            selectedNodeId: "",
            jsonText: "",
            error: "",
            conditionHelpOpen: false,
            datasetMenuOpen: false,
            deleteConfirmationOpen: false,
            deleteLoading: false,
            deleteError: "",
            conditionDeleteConfirmationOpen: false,
            conditionDeleteIndex: null,
            conditionDeleteNotice: "",
        };
    },
    mounted() {
        if (this.type === "form") this.loadFormDatasets();
    },
    computed: {
        parentFields() {
            return (this.fields || []).filter((field) => field && field.id !== this.field?.id && !["step", "html"].includes(field.type));
        },
        selectedNode() {
            return this.draftDataset?.nodes?.find((node) => node.id === this.selectedNodeId) || null;
        },
        selectedDataset() {
            return (this.datasets || []).find((entry) => String(entry.id) === String(this.draftField?.datasetId || "")) || null;
        },
        selectedDatasetLabel() {
            if (!this.selectedDataset) return "Create or select dataset";
            return `${this.selectedDataset.name} · ${this.selectedDataset.nodes?.length || 0} nodes`;
        },
        sortedNodes() {
            const ordered = [];
            const visited = new Set();
            const visit = (node) => {
                if (!node || visited.has(node.id)) return;
                visited.add(node.id);
                ordered.push(node);
                this.children(node.id).forEach(visit);
            };
            this.children(null).forEach(visit);
            (this.draftDataset?.nodes || []).forEach(visit);
            return ordered;
        },
    },
    watch: {
        open(value) {
            if (value) this.resetDraft();
        },
        field: {
            deep: true,
            handler() {
                if (this.open) this.resetDraft();
            },
        },
    },
    methods: {
        clone(value) {
            return JSON.parse(JSON.stringify(value));
        },
        emptyDataset() {
            return { id: null, name: "New Dataset", schemaVersion: 1, nodes: [] };
        },
        resetDraft() {
            const field = this.clone(this.field || {
                id: "",
                datasetMode: "static",
                datasetId: "",
                datasetParentFieldId: "",
                conditionalLogic: { enabled: false, relation: "all", rules: [] },
            });
            const logic = field.conditionalLogic && typeof field.conditionalLogic === "object"
                ? field.conditionalLogic
                : {};
            const rules = Array.isArray(logic.rules)
                ? logic.rules
                      .map((rule) => this.normalizeConditionRule(rule, field.datasetParentFieldId))
                      .filter((rule) => rule.fieldId && rule.fieldId !== field.id)
                : [];
            field.conditionalLogic = {
                enabled: logic.enabled === true && rules.length > 0,
                relation: logic.relation === "any" ? "any" : "all",
                rules,
            };
            const dataset = (this.datasets || []).find((entry) => String(entry.id) === String(field.datasetId));
            this.draftField = field;
            this.draftDataset = dataset ? this.clone(dataset) : this.emptyDataset();
            this.selectedNodeId = this.sortedNodes[0]?.id || "";
            this.jsonText = JSON.stringify(this.draftDataset, null, 2);
            this.error = "";
            this.conditionHelpOpen = false;
            this.datasetMenuOpen = false;
            this.deleteConfirmationOpen = false;
            this.deleteLoading = false;
            this.deleteError = "";
            this.conditionDeleteConfirmationOpen = false;
            this.conditionDeleteIndex = null;
            this.conditionDeleteNotice = "";
            this.mode = "visual";
        },
        selectDataset() {
            const dataset = (this.datasets || []).find((entry) => String(entry.id) === String(this.draftField.datasetId));
            this.draftDataset = dataset ? this.clone(dataset) : this.emptyDataset();
            this.selectedNodeId = this.sortedNodes[0]?.id || "";
            this.jsonText = JSON.stringify(this.draftDataset, null, 2);
            this.error = "";
            this.datasetMenuOpen = false;
            this.deleteError = "";
        },
        createDataset() {
            this.draftField.datasetMode = "dataset";
            this.draftField.datasetId = "";
            this.draftDataset = this.emptyDataset();
            this.selectedNodeId = "";
            this.jsonText = JSON.stringify(this.draftDataset, null, 2);
            this.datasetMenuOpen = false;
        },
        toggleDatasetMenu() {
            this.datasetMenuOpen = !this.datasetMenuOpen;
            if (this.datasetMenuOpen) this.deleteError = "";
        },
        closeDatasetMenu() {
            this.datasetMenuOpen = false;
        },
        chooseDataset(datasetId) {
            this.draftField.datasetId = String(datasetId || "");
            this.selectDataset();
        },
        openDeleteConfirmation() {
            if (!this.selectedDataset || this.deleteLoading) return;
            this.datasetMenuOpen = false;
            this.deleteError = "";
            this.deleteConfirmationOpen = true;
        },
        closeDeleteConfirmation() {
            if (!this.deleteLoading) this.deleteConfirmationOpen = false;
        },
        async confirmDeleteDataset() {
            const datasetId = String(this.draftField?.datasetId || "");
            const endpoint = String(this.endpoints?.destroy || "").replace("__DATASET_ID__", encodeURIComponent(datasetId));
            if (!datasetId) {
                this.deleteError = "Select a dataset before deleting it.";
                return;
            }
            if (!endpoint) {
                this.deleteError = "The delete endpoint is unavailable. Refresh the editor and try again.";
                return;
            }

            this.deleteLoading = true;
            this.deleteError = "";
            try {
                const csrfToken = window.PAGE_BUILDER_ELEMENTOR_V24_CONTEXT?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
                const response = await fetch(endpoint, {
                    method: "DELETE",
                    cache: "no-store",
                    credentials: "same-origin",
                    redirect: "manual",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                });
                const payload = await response.json().catch(() => null);
                if (!response.ok || payload?.success !== true) {
                    throw new Error(payload?.message || (response.redirected || response.type === "opaqueredirect" ? "Your session expired. Refresh the editor and try again." : "Could not delete the dataset."));
                }
                this.$emit("deleted", { datasetId, payload });
                this.deleteConfirmationOpen = false;
                this.datasetMenuOpen = false;
            } catch (error) {
                this.deleteError = error.message || "Could not delete the dataset.";
            } finally {
                this.deleteLoading = false;
            }
        },
        children(parentId) {
            return (this.draftDataset?.nodes || [])
                .filter((node) => (node.parentId || null) === (parentId || null))
                .sort((left, right) => (Number(left.sortOrder) || 0) - (Number(right.sortOrder) || 0));
        },
        nodeDepth(node) {
            let depth = 0;
            let parentId = node?.parentId || null;
            const seen = new Set();
            while (parentId && !seen.has(parentId)) {
                seen.add(parentId);
                depth++;
                parentId = this.draftDataset?.nodes?.find((item) => item.id === parentId)?.parentId || null;
            }
            return depth;
        },
        nodeCanAddChild(node) {
            return !node?.parentId || this.children(node.id).length > 0;
        },
        selectedNodeParentLabel() {
            const parentId = this.selectedNode?.parentId;
            if (!parentId) return "Root item";
            return this.draftDataset?.nodes?.find((node) => node.id === parentId)?.label || parentId;
        },
        descendants(nodeId) {
            const ids = new Set();
            const visit = (id) => this.children(id).forEach((node) => {
                ids.add(node.id);
                visit(node.id);
            });
            visit(nodeId);
            return ids;
        },
        addNode(parentId = null) {
            const base = parentId ? "child" : "root";
            let id = `${base}-${Date.now()}`;
            let suffix = 1;
            while ((this.draftDataset.nodes || []).some((node) => node.id === id)) id = `${base}-${Date.now()}-${suffix++}`;
            const node = {
                id,
                parentId,
                label: parentId ? "New child" : "New root item",
                name: parentId ? "New child" : "New root item",
                code: id.toUpperCase(),
                value: id,
                kind: "item",
                sortOrder: this.children(parentId).length + 1,
                active: true,
            };
            this.draftDataset.nodes.push(node);
            this.selectedNodeId = id;
        },
        removeSelectedNode() {
            if (!this.selectedNode) return;
            const removed = new Set([this.selectedNode.id, ...this.descendants(this.selectedNode.id)]);
            this.draftDataset.nodes = this.draftDataset.nodes.filter((node) => !removed.has(node.id));
            this.selectedNodeId = this.sortedNodes[0]?.id || "";
        },
        validateJson() {
            try {
                const parsed = JSON.parse(this.jsonText || "{}");
                if (!Array.isArray(parsed.nodes)) throw new Error("nodes must be an array.");
                this.draftDataset = {
                    id: parsed.id || null,
                    name: parsed.name || "New Dataset",
                    schemaVersion: Number(parsed.schemaVersion) || 1,
                    nodes: parsed.nodes,
                };
                this.selectedNodeId = this.sortedNodes[0]?.id || "";
                this.error = "";
            } catch (error) {
                this.error = error.message || "Invalid JSON.";
            }
        },
        normalizeConditionRule(rule = {}, selectedParentFieldId = "") {
            const valueSource = rule.valueSource === "selectedParent" ? "selectedParent" : "manual";
            const parentFieldId = valueSource === "selectedParent"
                ? String(rule.parentFieldId || selectedParentFieldId || "").trim()
                : String(rule.parentFieldId || "").trim();
            const selectedParent = valueSource === "selectedParent" && parentFieldId;
            return {
                fieldId: selectedParent ? parentFieldId : String(rule.fieldId || "").trim(),
                operator: ["equals", "not_equals", "contains", "empty", "not_empty"].includes(rule.operator) ? rule.operator : "equals",
                valueSource: selectedParent ? "selectedParent" : "manual",
                parentFieldId: selectedParent ? parentFieldId : "",
                parentValue: selectedParent ? String(rule.parentValue ?? rule.value ?? "").trim() : "",
                value: selectedParent ? "" : String(rule.value ?? ""),
            };
        },
        setConditionValueSource(rule) {
            if (rule.valueSource === "selectedParent") {
                const parentFieldId = String(this.draftField?.datasetParentFieldId || "");
                rule.fieldId = parentFieldId;
                rule.parentFieldId = parentFieldId;
                const entries = this.parentValueEntries(rule);
                if (rule.parentValue && !entries.some((entry) => entry.value === rule.parentValue)) rule.parentValue = "";
                rule.value = "";
            } else {
                rule.parentFieldId = "";
                rule.parentValue = "";
                if (!rule.fieldId) rule.fieldId = this.parentFields[0]?.id || "";
            }
        },
        syncSelectedParentRules() {
            const parentFieldId = String(this.draftField?.datasetParentFieldId || "");
            (this.draftField?.conditionalLogic?.rules || []).forEach((rule) => {
                if (rule.valueSource !== "selectedParent") return;
                rule.fieldId = parentFieldId;
                rule.parentFieldId = parentFieldId;
                const entries = this.parentValueEntries(rule);
                if (rule.parentValue && !entries.some((entry) => entry.value === rule.parentValue)) rule.parentValue = "";
            });
        },
        setConditionParentField(rule, fieldId) {
            const parentFieldId = String(fieldId || "").trim();
            this.draftField.datasetParentFieldId = parentFieldId;
            this.syncSelectedParentRules();
            rule.fieldId = parentFieldId;
            rule.parentFieldId = parentFieldId;
            const entries = this.parentValueEntries(rule);
            if (rule.parentValue && !entries.some((entry) => entry.value === rule.parentValue)) rule.parentValue = "";
        },
        selectParentValue(rule, value) {
            rule.parentValue = String(value ?? "");
            rule.fieldId = String(this.draftField?.datasetParentFieldId || "");
            rule.parentFieldId = rule.fieldId;
        },
        parentFieldOptionEntries(field) {
            if (!field) return [];
            const type = String(field.type || "").toLowerCase();
            if (!["select", "radio", "checkbox"].includes(type)) return [];
            if (field.datasetMode === "dataset" && field.datasetId) {
                const dataset = (this.datasets || []).find((entry) => String(entry.id) === String(field.datasetId));
                return (dataset?.nodes || [])
                    .filter((node) => node && node.active !== false && (node.parentId ?? null) === null)
                    .map((node) => ({ label: String(node.label || node.name || node.value || node.id), value: String(node.value ?? node.code ?? node.id ?? "") }));
            }
            return String(field.optionsText || "")
                .split(/\r?\n/)
                .map((line) => line.trim())
                .filter(Boolean)
                .map((line) => {
                    const [label, ...rest] = line.split("|");
                    const value = rest.length ? rest.join("|").trim() : label.trim();
                    return { label: label.trim(), value };
                });
        },
        parentFieldForRule(rule) {
            const parentFieldId = String(rule?.parentFieldId || this.draftField?.datasetParentFieldId || "").trim();
            return this.parentFields.find((field) => field.id === parentFieldId) || null;
        },
        parentValueEntries(rule) {
            const parent = this.parentFieldForRule(rule);
            return parent ? this.parentFieldOptionEntries(parent) : [];
        },
        parentValueSourceLabel(rule) {
            const parent = this.parentFieldForRule(rule);
            return parent ? `${parent.label} (${parent.id})` : "Select Parent field above";
        },
        previewParentValue(rule) {
            const parent = this.parentFieldForRule(rule);
            if (!parent) return "Select a parent field";
            const entries = this.parentFieldOptionEntries(parent);
            const option = entries.find((entry) => entry.value === String(rule.parentValue || ""));
            if (option) return `${option.label} · ${option.value}`;
            return "Select a parent value";
        },
        addConditionRule() {
            this.draftField.conditionalLogic ||= { enabled: true, relation: "all", rules: [] };
            this.draftField.conditionalLogic.enabled = true;
            this.draftField.conditionalLogic.rules ||= [];
            this.draftField.conditionalLogic.rules.push({ fieldId: this.parentFields[0]?.id || "", operator: "equals", valueSource: "manual", parentFieldId: "", parentValue: "", value: "" });
        },
        removeConditionRule(index) {
            this.draftField.conditionalLogic?.rules?.splice(index, 1);
        },
        openConditionDeleteConfirmation(index) {
            const parsedIndex = Number(index);
            if (!Number.isInteger(parsedIndex) || !this.draftField?.conditionalLogic?.rules?.[parsedIndex]) return;
            this.conditionDeleteIndex = parsedIndex;
            this.conditionDeleteConfirmationOpen = true;
        },
        closeConditionDeleteConfirmation() {
            this.conditionDeleteConfirmationOpen = false;
            this.conditionDeleteIndex = null;
        },
        confirmConditionDelete() {
            if (this.conditionDeleteIndex === null) return;
            this.removeConditionRule(this.conditionDeleteIndex);
            this.closeConditionDeleteConfirmation();
            this.conditionDeleteNotice = "Condition removed. Click Apply dataset to save.";
        },
        clearNotice() {
            this.conditionDeleteNotice = "";
            this.$emit("clear-notice");
        },
        apply() {
            if (!this.draftField || this.loading || this.deleteLoading) return;
            if (this.draftField.datasetMode === "dataset" && !this.draftDataset) {
                this.error = "Select or create a dataset first.";
                return;
            }
            this.$emit("save", { field: this.clone(this.draftField), dataset: this.clone(this.draftDataset) });
        },
    },
    template: `<div v-if="open" class="pb-modal-backdrop pb-form-dataset-modal-backdrop" @click.self="$emit('close')"><div class="pb-modal pb-form-dataset-modal" role="dialog" aria-modal="true" aria-labelledby="pb-form-dataset-title"><div class="pb-form-dataset-modal__header"><div class="pb-form-dataset-modal__title"><i class="fas fa-database"></i><div><span class="pb-form-dataset-modal__eyebrow">FORM FIELD DATASET</span><h2 id="pb-form-dataset-title">Manage dataset</h2><p><strong>{{ field?.label || 'Select field' }}</strong> options · parent field {{ draftField?.datasetParentFieldId || 'Root options' }}</p></div></div><button type="button" class="pb-modal-close" aria-label="Close modal" @click="$emit('close')"><i class="fas fa-times"></i></button></div><div class="pb-form-dataset-modal__context"><div class="pb-form-group"><label class="pb-form-label">Data source</label><select class="pb-select" v-model="draftField.datasetMode"><option value="static">Static field options</option><option value="dataset">Shared dataset</option></select></div><div v-if="draftField.datasetMode === 'dataset'" class="pb-form-group"><label class="pb-form-label">Shared dataset</label><select class="pb-select" v-model="draftField.datasetId" @change="selectDataset"><option value="">Create or select dataset</option><option v-for="dataset in datasets" :key="dataset.id" :value="String(dataset.id)">{{ dataset.name }} · {{ dataset.nodes?.length || 0 }} nodes</option></select></div><div v-if="draftField.datasetMode === 'dataset'" class="pb-form-group"><label class="pb-form-label">Parent field</label><select class="pb-select" v-model="draftField.datasetParentFieldId" @change="syncSelectedParentRules"><option value="">Root options</option><option v-for="parent in parentFields" :key="parent.id" :value="parent.id">{{ parent.label }} ({{ parent.id }})</option></select></div><button v-if="draftField.datasetMode === 'dataset'" type="button" class="pb-btn" @click="createDataset"><i class="fas fa-plus"></i> New dataset</button></div><div v-if="draftField.datasetMode === 'static'" class="pb-modal-body pb-form-dataset-static"><div class="pb-form-group"><label class="pb-form-label">Options (Label|value per line)</label><textarea class="pb-textarea" rows="8" v-model="draftField.optionsText"></textarea></div></div><template v-else><div class="pb-state-tabs--pro pb-form-dataset-tabs"><button type="button" :class="{active:mode==='visual'}" @click="mode='visual'"><i class="fas fa-th"></i> Visual Manager</button><button type="button" :class="{active:mode==='json'}" @click="mode='json'"><i class="fas fa-code"></i> JSON Manager</button><button type="button" :class="{active:mode==='conditions'}" @click="mode='conditions'"><i class="fas fa-sitemap"></i> Conditional Logic</button></div><div v-if="mode==='visual'" class="pb-modal-body pb-form-dataset-body"><div class="pb-form-dataset-tree"><div class="pb-form-dataset-heading"><div><span class="pb-form-dataset__eyebrow">DATASET STRUCTURE</span><strong>{{ draftDataset?.name || 'New Dataset' }}</strong></div><button type="button" class="pb-btn primary" @click="addNode(null)"><i class="fas fa-plus"></i> Add root</button></div><div class="pb-form-dataset-search"><i class="fas fa-search"></i><input class="pb-input" placeholder="Search dataset..." /></div><div class="pb-form-dataset-node-list"><button v-for="node in sortedNodes" :key="node.id" type="button" class="pb-form-dataset-node" :class="{active:node.id===selectedNodeId}" :style="{paddingLeft:(8 + nodeDepth(node)*18)+'px'}" @click="selectedNodeId=node.id"><i class="fas" :class="children(node.id).length ? 'fa-chevron-down' : 'fa-circle'"></i><strong>{{ node.label }}</strong><small>{{ node.code }}</small><span class="pb-form-dataset-node__add" role="button" title="Add child node" aria-label="Add child node" @click.stop="addNode(node.id)"><i class="fas fa-plus"></i></span></button></div></div><div class="pb-form-dataset-editor"><div class="pb-form-dataset-heading"><div><span class="pb-form-dataset__eyebrow">SELECTED NODE</span><strong>Edit node</strong></div><span class="pb-form-dataset-context">parentId: {{ selectedNode?.parentId || 'root' }}</span></div><div v-if="selectedNode" class="pb-form-row pb-form-row--two"><text-control label="Label" v-model="selectedNode.label" /><text-control label="Name" v-model="selectedNode.name" /><text-control label="Code" v-model="selectedNode.code" /><text-control label="Value" v-model="selectedNode.value" /><text-control label="Kind" v-model="selectedNode.kind" /><select-control label="Parent" v-model="selectedNode.parentId" :options="[{value:'',label:'Root item'}, ...draftDataset.nodes.filter(item => item.id !== selectedNode.id && !descendants(selectedNode.id).has(item.id)).map(item => ({value:item.id,label:item.label}))]" /><button type="button" class="pb-btn" @click="addNode(selectedNode.id)"><i class="fas fa-plus"></i> Add child</button><button type="button" class="pb-btn" @click="removeSelectedNode"><i class="fas fa-trash"></i> Delete node</button></div><div v-else class="pb-form-note">Add a root item to begin building this dataset.</div></div></div><div v-else-if="mode==='json'" class="pb-modal-body pb-form-dataset-json"><div class="pb-form-group"><label class="pb-form-label">Dataset JSON</label><textarea class="pb-textarea pb-form-dataset-json__input" rows="16" v-model="jsonText"></textarea></div><button type="button" class="pb-btn" @click="validateJson"><i class="fas fa-check"></i> Validate JSON</button><p v-if="error" class="pb-form-dataset-error">{{ error }}</p></div><div v-else class="pb-form-dataset-conditions pb-form-dataset-conditions--tab"><div class="pb-form-dataset-heading"><div class="pb-form-dataset-condition-heading"><span class="pb-form-dataset__eyebrow">CONDITIONAL VISIBILITY</span><div class="pb-form-dataset-condition-title"><strong>Show this field when</strong><button type="button" class="pb-form-dataset-help" :aria-expanded="conditionHelpOpen ? 'true' : 'false'" aria-controls="pb-form-dataset-condition-help" aria-label="Conditional Logic help" @click.stop="conditionHelpOpen = !conditionHelpOpen"><i class="fas fa-question"></i></button></div><div v-if="conditionHelpOpen" id="pb-form-dataset-condition-help" class="pb-form-dataset-help-tooltip" role="tooltip">Field menentukan field yang nilainya diperiksa. Manual value memakai nilai tetap. Selected parent tidak memilih value manual; pilih Parent field dan runtime akan mengikuti pilihan user pada field tersebut. Preview value hanya contoh/default option. Untuk dependent select, gunakan Parent field di bagian atas.</div></div><label class="pb-form-dataset-condition-toggle"><input type="checkbox" v-model="draftField.conditionalLogic.enabled"><span class="pb-form-dataset-condition-toggle__track" aria-hidden="true"></span><span>Enabled</span></label></div><div v-if="draftField.conditionalLogic.enabled" class="pb-form-dataset-condition-list"><select class="pb-select pb-form-dataset-relation" v-model="draftField.conditionalLogic.relation"><option value="all">All rules match</option><option value="any">Any rule matches</option></select><div v-for="(rule,index) in draftField.conditionalLogic.rules" :key="index" class="pb-form-dataset-condition-row"><label v-if="rule.valueSource !== 'selectedParent'" class="pb-form-dataset-condition-control"><span>Check field</span><select class="pb-select" aria-label="Condition field" v-model="rule.fieldId"><option value="">Select field</option><option v-for="parent in parentFields" :key="parent.id" :value="parent.id">{{ parent.label }}</option></select></label><label class="pb-form-dataset-condition-control"><span>Operator</span><select class="pb-select" aria-label="Operator" v-model="rule.operator"><option value="equals">Equals</option><option value="not_equals">Does not equal</option><option value="contains">Contains</option><option value="empty">Is empty</option><option value="not_empty">Is not empty</option></select></label><label class="pb-form-dataset-condition-control"><span>Value source</span><select class="pb-select" aria-label="Value source" v-model="rule.valueSource" @change="setConditionValueSource(rule)"><option value="manual">Manual value</option><option value="selectedParent">Selected parent</option></select></label><button type="button" class="pb-btn icon" @click="removeConditionRule(index)" aria-label="Remove rule"><i class="fas fa-trash"></i></button><label v-if="rule.valueSource !== 'selectedParent'" class="pb-form-dataset-condition-control pb-form-dataset-condition-manual-value"><span>Manual value</span><input class="pb-input" v-model="rule.value" placeholder="Value" /></label><template v-else><div class="pb-form-dataset-parent-values"><div class="pb-form-dataset-parent-values__head"><span>Parent values from context</span><strong>{{ parentValueSourceLabel(rule) }}</strong></div><div v-if="parentValueEntries(rule).length" class="pb-form-dataset-parent-values__list"><span v-for="entry in parentValueEntries(rule)" :key="entry.value">{{ entry.label }} · {{ entry.value }}</span></div><div v-else class="pb-form-dataset-parent-values__empty">No selectable values found. Choose a select, radio, checkbox, or dataset parent above.</div><small>Runtime uses the value selected by the user in the parent field.</small></div></template></div><button type="button" class="pb-btn" @click="addConditionRule"><i class="fas fa-plus"></i> Add condition</button></div></div></template><div class="pb-form-dataset-modal__footer"><span><i class="fas fa-info-circle"></i> Changes apply after Apply dataset.</span><div><button type="button" class="pb-btn" @click="$emit('close')">Cancel</button><button type="button" class="pb-btn primary" @click="apply">Apply dataset</button></div></div></div></div>`,
};
FormDatasetManager.template = FormDatasetManager.template
    .replace(
        "Field menentukan field yang nilainya diperiksa. Manual value memakai nilai tetap. Selected parent tidak memilih value manual; pilih Parent field dan runtime akan mengikuti pilihan user pada field tersebut. Preview value hanya contoh/default option. Untuk dependent select, gunakan Parent field di bagian atas.",
        "Selected parent menampilkan daftar nilai dari Parent field di atas. Pilih satu nilai dari daftar tersebut. Equals menampilkan field saat parent memilih nilai itu; Does not equal menampilkan field saat parent memilih nilai lain. Parent field di bagian atas tetap mengatur filter pilihan dependent select.",
    )
    .replace(
        '<div v-if="draftField.datasetMode === \'dataset\'" class="pb-form-group"><label class="pb-form-label">Parent field</label>',
        '<div v-if="draftField.datasetMode === \'dataset\' && mode !== \'conditions\'" class="pb-form-group"><label class="pb-form-label">Parent field</label>',
    )
    .replace(
        '<span v-for="entry in parentValueEntries(rule)" :key="entry.value">{{ entry.label }} · {{ entry.value }}</span>',
        '<button v-for="entry in parentValueEntries(rule)" :key="entry.value" type="button" class="pb-form-dataset-parent-value" :class="{active:entry.value === rule.parentValue}" :aria-pressed="entry.value === rule.parentValue ? \'true\' : \'false\'" @click="selectParentValue(rule, entry.value)">{{ entry.label }} · {{ entry.value }}</button>',
    )
    .replace(
        '<button type="button" class="pb-btn icon" @click="removeConditionRule(index)" aria-label="Remove rule"><i class="fas fa-trash"></i></button>',
        '',
    )
    .replace(
        '<div v-for="(rule,index) in draftField.conditionalLogic.rules" :key="index" class="pb-form-dataset-condition-row">',
        `<div v-for="(rule,index) in draftField.conditionalLogic.rules" :key="index" class="pb-form-dataset-condition-row pb-form-dataset-condition-card" :class="{'pb-form-dataset-condition-card--selected-parent':rule.valueSource === 'selectedParent'}"><div class="pb-form-dataset-condition-card__header"><strong>Condition {{ index + 1 }}</strong><button type="button" class="pb-btn pb-form-dataset-condition-remove" @click="openConditionDeleteConfirmation(index)" aria-label="Remove condition"><i class="fas fa-trash"></i></button></div>`,
    )
    .replace(
        /<template v-else><div class="pb-form-dataset-parent-values">.*?<\/div><\/template>/,
        `<template v-else><div class="pb-form-dataset-selected-parent-grid"><label class="pb-form-dataset-condition-control"><span>Parent field</span><select class="pb-select" aria-label="Parent field" :value="rule.parentFieldId || draftField.datasetParentFieldId" @change="setConditionParentField(rule, $event.target.value)"><option value="">Select parent field</option><option v-for="parent in parentFields" :key="parent.id" :value="parent.id">{{ parent.label }} ({{ parent.id }})</option></select></label><label class="pb-form-dataset-condition-control"><span>Parent value</span><select class="pb-select" aria-label="Parent value" v-model="rule.parentValue"><option value="">Select parent value</option><option v-for="entry in parentValueEntries(rule)" :key="entry.value" :value="entry.value">{{ entry.label }} · {{ entry.value }}</option></select></label><small class="pb-form-dataset-selected-parent-help">Shows this field when the selected parent value matches.</small></div></template>`,
    )
    .replace(
        ':class="{active:node.id===selectedNodeId}"',
        ':class="{active:node.id===selectedNodeId,\'is-root\':!node.parentId,\'is-leaf\':!children(node.id).length}"',
    )
    .replace(
        ':style="{paddingLeft:(8 + nodeDepth(node)*18)+\'px\'}"',
        ':style="{paddingLeft:(8 + nodeDepth(node)*18)+\'px\',\'--node-indent\':(nodeDepth(node)*18)+\'px\'}"',
    )
    .replace(
        `<i class="fas" :class="children(node.id).length ? 'fa-chevron-down' : 'fa-circle'"></i>`,
        `<i class="fas" :class="children(node.id).length ? 'fa-chevron-down' : (node.parentId ? 'fa-circle' : 'fa-chevron-right')"></i>`,
    )
    .replace(
        '<span class="pb-form-dataset-node__add" role="button" title="Add child node" aria-label="Add child node" @click.stop="addNode(node.id)"><i class="fas fa-plus"></i></span>',
        '<span v-if="nodeCanAddChild(node)" class="pb-form-dataset-node__add" role="button" title="Add child node" aria-label="Add child node" @click.stop="addNode(node.id)"><i class="fas fa-plus"></i></span>',
    )
    .replace(
        'parentId: {{ selectedNode?.parentId || \'root\' }}',
        'Belongs to: {{ selectedNodeParentLabel() }}',
    )
    .replace(
        '<text-control label="Kind" v-model="selectedNode.kind" />',
        '',
    )
    .replace(
        '<select-control label="Parent" v-model="selectedNode.parentId"',
        '<select-control label="Belongs to" v-model="selectedNode.parentId"',
    )
    .replace(
        '<button type="button" class="pb-btn" @click="addNode(selectedNode.id)"><i class="fas fa-plus"></i> Add child</button><button type="button" class="pb-btn" @click="removeSelectedNode"><i class="fas fa-trash"></i> Delete node</button>',
        '<div class="pb-form-dataset-node-actions"><button type="button" class="pb-btn pb-form-dataset-node-actions__add" @click="addNode(selectedNode.id)"><i class="fas fa-plus"></i> Add child</button><button type="button" class="pb-btn pb-form-dataset-node-actions__delete" @click="removeSelectedNode"><i class="fas fa-trash"></i> Delete node</button></div>',
    )
    .replace(
        '<div v-if="draftField.datasetMode === \'dataset\'" class="pb-form-group"><label class="pb-form-label">Shared dataset</label><select class="pb-select" v-model="draftField.datasetId" @change="selectDataset"><option value="">Create or select dataset</option><option v-for="dataset in datasets" :key="dataset.id" :value="String(dataset.id)">{{ dataset.name }} · {{ dataset.nodes?.length || 0 }} nodes</option></select></div>',
        `<div v-if="draftField.datasetMode === 'dataset'" class="pb-form-group"><label class="pb-form-label">Shared dataset</label><div class="pb-form-dataset-picker" @click.stop><button type="button" class="pb-select pb-form-dataset-picker__trigger" aria-haspopup="listbox" :aria-expanded="datasetMenuOpen ? 'true' : 'false'" @click="toggleDatasetMenu"><span>{{ selectedDatasetLabel }}</span><i class="fas fa-chevron-down"></i></button><div v-if="datasetMenuOpen" class="pb-form-dataset-picker__menu" role="listbox" aria-label="Shared datasets"><button type="button" class="pb-form-dataset-picker__option" role="option" :aria-selected="!draftField.datasetId ? 'true' : 'false'" @click="chooseDataset('')">Create or select dataset</button><button v-for="dataset in datasets" :key="dataset.id" type="button" class="pb-form-dataset-picker__option" role="option" :aria-selected="String(dataset.id) === String(draftField.datasetId) ? 'true' : 'false'" @click="chooseDataset(dataset.id)">{{ dataset.name }} · {{ dataset.nodes?.length || 0 }} nodes</button><div v-if="selectedDataset" class="pb-form-dataset-picker__separator" role="separator"></div><button v-if="selectedDataset" type="button" class="pb-form-dataset-picker__delete" @click="openDeleteConfirmation"><i class="fas fa-trash-alt"></i><span>Delete selected dataset</span></button></div></div></div>`,
    )
    .replace(
        '<div class="pb-form-dataset-modal__header">',
        `<div v-if="notice || serverError || conditionDeleteNotice" class="ph-notice" v-cloak><div aria-live="polite" aria-atomic="true" class="position-relative"><div class="toast-container position-fixed top-0 end-0 p-3 pb-form-dataset-toast-container"><div class="toast ph-notice-toast ph-callout-no-border show" :class="serverError ? 'ph-callout-danger' : 'ph-callout-success'" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000"><div class="toast-header px-3 pt-3 pb-1 border-0" :class="serverError ? 'ph-callout-danger' : 'ph-callout-success'"><strong class="toast-header-title toast-header-icon me-auto">Notice</strong><small>just now</small><button type="button" class="btn-close" aria-label="Close notice" @click="clearNotice"></button></div><div class="toast-body p-3 text-start">{{ serverError || notice || conditionDeleteNotice }}</div></div></div></div></div><div class="pb-form-dataset-modal__header">`,
    )
    .replace(
        '<div class="pb-form-dataset-modal__footer">',
        `<div v-if="deleteConfirmationOpen" class="pb-modal-backdrop pb-form-dataset-delete-backdrop" @click.self="closeDeleteConfirmation" @keydown.esc.window="closeDeleteConfirmation"><div class="pb-modal pb-form-dataset-delete-modal" role="dialog" aria-modal="true" aria-labelledby="pb-form-dataset-delete-title" aria-describedby="pb-form-dataset-delete-message"><div class="pb-form-dataset-delete-modal__icon" aria-hidden="true"><i class="fas fa-trash-alt"></i></div><h2 id="pb-form-dataset-delete-title">Delete dataset?</h2><p id="pb-form-dataset-delete-message">This will disconnect every form field using <strong>{{ selectedDataset?.name }}</strong>.</p><p v-if="deleteError" class="pb-form-dataset-delete-modal__error" role="alert">{{ deleteError }}</p><div class="pb-form-dataset-delete-modal__actions"><button type="button" class="pb-btn" @click="closeDeleteConfirmation" :disabled="deleteLoading">Cancel</button><button type="button" class="pb-btn pb-form-dataset-delete-modal__confirm" @click="confirmDeleteDataset" :disabled="deleteLoading"><i v-if="deleteLoading" class="fas fa-spinner fa-spin"></i><i v-else class="fas fa-trash-alt"></i>{{ deleteLoading ? 'Deleting…' : 'Delete dataset' }}</button></div></div></div><div class="pb-form-dataset-modal__footer">`,
    )
    .replace(
        '<div class="pb-form-dataset-modal__footer">',
        `<div v-if="conditionDeleteConfirmationOpen" class="pb-modal-backdrop pb-form-dataset-delete-backdrop" @click.self="closeConditionDeleteConfirmation" @keydown.esc.window="closeConditionDeleteConfirmation"><div class="pb-modal pb-form-dataset-delete-modal" role="dialog" aria-modal="true" aria-labelledby="pb-form-condition-delete-title" aria-describedby="pb-form-condition-delete-message"><div class="pb-form-dataset-delete-modal__icon" aria-hidden="true"><i class="fas fa-trash-alt"></i></div><h2 id="pb-form-condition-delete-title">Delete condition?</h2><p id="pb-form-condition-delete-message">This will remove Condition {{ conditionDeleteIndex + 1 }} from this field.</p><div class="pb-form-dataset-delete-modal__actions"><button type="button" class="pb-btn" @click="closeConditionDeleteConfirmation">Cancel</button><button type="button" class="pb-btn pb-form-dataset-delete-modal__confirm" @click="confirmConditionDelete"><i class="fas fa-trash-alt"></i>Delete condition</button></div></div></div><div class="pb-form-dataset-modal__footer">`,
    )
    .replace(
        '<span><i class="fas fa-info-circle"></i> Changes apply after Apply dataset.</span>',
        '<span><i class="fas fa-info-circle"></i> Changes apply after Apply dataset.</span>',
    )
    .replace(
        '<button type="button" class="pb-btn primary" @click="apply">Apply dataset</button>',
        '<button type="button" class="pb-btn primary" @click="apply" :disabled="loading || deleteLoading"><i v-if="loading" class="fas fa-spinner fa-spin"></i>{{ loading ? \'Saving…\' : \'Apply dataset\' }}</button>',
    )
    .replace(
        '<div class="pb-modal pb-form-dataset-modal" role="dialog"',
        '<div class="pb-modal pb-form-dataset-modal" role="dialog" @click="datasetMenuOpen = false" @keydown.esc="closeDatasetMenu"',
    );
const option = (value, label = value) => ({ value, label });
export default {
    name: "ProWidgetSettings",
    props: {
        node: { type: Object, required: true },
        editor: { type: Object, required: true },
    },
    components: {
        SectionBox,
        ResponsiveSelect,
        TextControl,
        TextareaControl,
        NumberControl,
        SelectControl,
        ToggleControl,
        ColorControl,
        MediaControl,
        LinkField,
        SizeControl,
        SidesControl,
        ResponsiveNumber,
        ResponsiveChoice,
        RepeaterList,
        FormRowGridEditor,
        ProIconPicker,
        ArrowIconPicker,
        FormDatasetManager,
    },
    data() {
        return {
            buttonStates: {
                form: "normal",
                slides: "normal",
                priceTable: "normal",
                cta: "normal",
                flipBox: "normal",
            },
            formDatasetModalOpen: false,
            formDatasetField: null,
            formDatasets: [],
            formDatasetsLoaded: false,
            formDatasetLoading: false,
            formDatasetError: "",
            formDatasetNotice: "",
            formMessageEditorState: "success",
        };
    },
    created() {
        this.ensureFormRowGrid();
    },
    computed: {
        type() {
            return this.node.type;
        },
        s() {
            return this.node.settings || {};
        },
        formMessageTitleModel: {
            get() {
                return this.formMessageEditorState === "error"
                    ? this.s.errorTitle
                    : this.s.successTitle;
            },
            set(value) {
                if (this.formMessageEditorState === "error") this.s.errorTitle = value;
                else this.s.successTitle = value;
            },
        },
        formMessageTextModel: {
            get() {
                return this.formMessageEditorState === "error"
                    ? this.s.errorMessage
                    : this.s.successMessage;
            },
            set(value) {
                if (this.formMessageEditorState === "error") this.s.errorMessage = value;
                else this.s.successMessage = value;
            },
        },
        fieldTypes() {
            return [
                "text",
                "email",
                "textarea",
                "number",
                "tel",
                "url",
                "select",
                "checkbox",
                "radio",
                "date",
                "time",
                "file",
                "hidden",
                "acceptance",
                "html",
            ].map((v) => option(v, v[0].toUpperCase() + v.slice(1)));
        },
        formTypesWithPlaceholder() {
            return [
                "text",
                "email",
                "textarea",
                "number",
                "tel",
                "url",
                "select",
                "date",
                "time",
            ];
        },
        formTypesWithDefault() {
            return [
                "text",
                "email",
                "textarea",
                "number",
                "tel",
                "url",
                "date",
                "time",
                "hidden",
            ];
        },
        formTypesWithAutocomplete() {
            return ["text", "email", "tel", "url"];
        },
        formActionOptions() {
            return [
                option("message", "Message"),
                option("collect", "Collect Submissions"),
                option("email", "Email"),
                option("email2", "Email 2"),
                option("redirect", "Redirect"),
                option("webhook", "Webhook"),
            ];
        },
        emailContentTypeOptions() {
            return [option("html", "HTML"), option("plain", "Plain")];
        },
        iconPositionOptions() {
            return [option("before", "Before"), option("after", "After")];
        },
        sizeOptions() {
            return [
                "extra-small",
                "small",
                "medium",
                "large",
                "extra-large",
            ].map((v) => option(v, v.replace("-", " ")));
        },
        widthOptions() {
            return [20, 25, 30, 33, 40, 50, 60, 66, 70, 75, 80, 100].map((v) =>
                option(String(v), v + "%"),
            );
        },
        stepTypes() {
            return [
                option("none", "None"),
                option("text", "Text"),
                option("icon", "Icon"),
                option("number", "Number"),
                option("progress", "Progress Bar"),
                option("number-text", "Number & Text"),
                option("icon-text", "Icon & Text"),
            ];
        },
        shapeOptions() {
            return ["circle", "square", "rounded", "none"].map((v) =>
                option(v),
            );
        },
        validationOptions() {
            return [
                option("browser", "Browser Default"),
                option("custom", "Custom"),
            ];
        },
        tagOptions() {
            return ["h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p"].map(
                (v) => option(v, v.toUpperCase()),
            );
        },
        navigationOptions() {
            return [
                option("both", "Arrows and Dots"),
                option("arrows", "Arrows"),
                option("dots", "Dots"),
                option("none", "None"),
            ];
        },
        effectiveNavigation() {
            if (["both", "arrows", "dots", "none"].includes(this.s.navigation)) {
                return this.s.navigation;
            }
            const arrows = this.s.arrows !== false;
            return arrows
                ? this.s.pagination === "none" ? "arrows" : "both"
                : this.s.pagination === "none" ? "none" : "dots";
        },
        applyLinkOnOptions() {
            return [
                option("button", "Button Only"),
                option("whole-slide", "Whole Slide"),
            ];
        },
        insideOutsideOptions() {
            return [option("inside", "Inside"), option("outside", "Outside")];
        },
        backgroundSizeOptions() {
            return ["cover", "contain", "auto"].map((v) => option(v));
        },
        transitionOptions() {
            return [option("slide", "Slide"), option("fade", "Fade")];
        },
        animationOptions() {
            return ["none", "down", "up", "right", "left", "zoom"].map((v) =>
                option(v),
            );
        },
        headlineStyles() {
            return [
                option("highlighted", "Highlighted"),
                option("rotating", "Rotating"),
            ];
        },
        markerOptions() {
            return [
                "circle",
                "curly",
                "underline",
                "double",
                "double-underline",
                "underline-zigzag",
                "diagonal",
                "strikethrough",
                "x",
            ].map((v) => option(v));
        },
        rotationOptions() {
            return [
                "typing",
                "clip",
                "flip",
                "swirl",
                "blinds",
                "drop-in",
                "wave",
                "slide",
            ].map((v) => option(v));
        },
        alignmentOptions() {
            return [
                option("left", "Left"),
                option("center", "Center"),
                option("right", "Right"),
            ];
        },
        verticalOptions() {
            return [
                option("top", "Top"),
                option("middle", "Middle"),
                option("bottom", "Bottom"),
            ];
        },
        imageSizes() {
            return ["thumbnail", "medium", "large", "full"].map((v) =>
                option(v),
            );
        },
        objectFitOptions() {
            return ["cover", "contain", "fill", "none", "scale-down"].map((v) =>
                option(v),
            );
        },
        objectPositionOptions() {
            return [
                option("top left", "Top Left"),
                option("top center", "Top Center"),
                option("top right", "Top Right"),
                option("center left", "Center Left"),
                option("center center", "Center Center"),
                option("center right", "Center Right"),
                option("bottom left", "Bottom Left"),
                option("bottom center", "Bottom Center"),
                option("bottom right", "Bottom Right"),
            ];
        },
        borderTypeOptions() {
            return ["none", "solid", "double", "dotted", "dashed"].map((v) =>
                option(v),
            );
        },
        hotspotAnimations() {
            return ["soft-beat", "expand", "overlay", "none"].map((v) =>
                option(v),
            );
        },
        tooltipPositions() {
            return ["left", "top", "right", "bottom"].map((v) => option(v));
        },
        tooltipTriggers() {
            return ["hover", "click", "none"].map((v) => option(v));
        },
        tooltipAnimations() {
            return [
                "fade",
                "grow",
                "directional-fade",
                "directional-slide",
            ].map((v) => option(v));
        },
        separatorOptions() {
            return ["solid", "dotted", "dashed", "double", "none"].map((v) =>
                option(v),
            );
        },
        currencyOptions() {
            return ["", "$", "€", "£", "¥", "₹", "₽", "R$"].map((v) =>
                option(v || "none", v || "None"),
            );
        },
        currencyFormats() {
            return [option("comma", "1,234.56"), option("period", "1.234,56")];
        },
        leftRightOptions() {
            return [option("left", "Left"), option("right", "Right")];
        },
        skinOptions() {
            return [option("classic", "Classic"), option("cover", "Cover")];
        },
        imagePositions() {
            return ["left", "above", "right", "below"].map((v) => option(v));
        },
        countdownTypes() {
            return [
                option("due-date", "Due Date"),
                option("evergreen", "Evergreen Timer"),
            ];
        },
        progressTrackerTypeOptions() {
            return [option("horizontal", "Horizontal"), option("circular", "Circular")];
        },
        progressTrackerRelativeOptions() {
            return [
                option("page", "Entire Page"),
                option("post_content", "Post Content"),
                option("selector", "CSS Selector"),
            ];
        },
        progressTrackerAlignmentOptions() {
            return [
                { value: "left", label: "Left", icon: "fas fa-align-left" },
                { value: "center", label: "Center", icon: "fas fa-align-center" },
                { value: "right", label: "Right", icon: "fas fa-align-right" },
            ];
        },
        videoPlaylistTypeOptions() {
            return [
                option("youtube", "YouTube"),
                option("vimeo", "Vimeo"),
                option("self_hosted", "Self Hosted"),
                option("section", "Section"),
            ];
        },
        playlistTagOptions() {
            return ["h2", "h3", "h4", "h5", "h6", "div", "span"].map((v) =>
                option(v, v.toUpperCase()),
            );
        },
        playlistItemTagOptions() {
            return ["h3", "h4", "h5", "h6", "div", "span"].map((v) =>
                option(v, v.toUpperCase()),
            );
        },
        playlistImageResolutionOptions() {
            return [
                option("thumbnail", "Thumbnail"),
                option("medium", "Medium"),
                option("medium_large", "Medium Large"),
                option("large", "Large"),
                option("1536x1536", "1536 x 1536"),
                option("2048x2048", "2048 x 2048"),
                option("full", "Full"),
                option("custom", "Custom"),
            ];
        },
        videoPlaylistPositionOptions() {
            return [
                { value: "left", label: "Left", icon: "fas fa-columns" },
                { value: "right", label: "Right", icon: "fas fa-columns" },
            ];
        },
        videoPlaylistBackgroundTypeOptions() {
            return [option("classic", "Classic"), option("gradient", "Gradient"), option("none", "None")];
        },
        displayOptions() {
            return [option("block", "Block"), option("inline", "Inline")];
        },
        expireActions() {
            return [
                option("none", "None"),
                option("message", "Show Message"),
                option("hide", "Hide"),
                option("redirect", "Redirect"),
            ];
        },
        paginationOptions() {
            return [
                option("dots", "Dots"),
                option("fraction", "Fraction"),
                option("progress", "Progress"),
                option("none", "None"),
            ];
        },
        reviewImageSizes() {
            return [
                option("thumbnail", "Thumbnail - 150 x 150"),
                option("medium", "Medium - 300 x 300"),
                option("medium_large", "Medium Large - 768 x 0"),
                option("large", "Large - 1024 x 1024"),
                option("1536x1536", "1536 x 1536"),
                option("2048x2048", "2048 x 2048"),
                option("full", "Full"),
                option("custom", "Custom"),
            ];
        },
        testimonialSkinOptions() {
            return [option("default", "Default"), option("bubble", "Bubble")];
        },
        testimonialLayoutOptions() {
            return [
                option("image_inline", "Image Inline"),
                option("image_stacked", "Image Stacked"),
                option("image_above", "Image Above"),
                option("image_left", "Image Left"),
                option("image_right", "Image Right"),
            ];
        },
        testimonialAlignmentOptions() {
            return [
                { value: "left", label: "Left", icon: "fas fa-align-left" },
                { value: "center", label: "Center", icon: "fas fa-align-center" },
                { value: "right", label: "Right", icon: "fas fa-align-right" },
            ];
        },
        blockquoteSkinOptions() {
            return [
                option("border", "Border"),
                option("quotation", "Quotation"),
                option("boxed", "Boxed"),
                option("clean", "Clean"),
            ];
        },
        blockquoteAlignmentOptions() {
            return [
                { value: "left", label: "Left", icon: "fas fa-align-left" },
                { value: "center", label: "Center", icon: "fas fa-align-center" },
                { value: "right", label: "Right", icon: "fas fa-align-right" },
            ];
        },
        blockquoteTweetViewOptions() {
            return [
                option("icon_text", "Icon & Text"),
                option("icon", "Icon"),
                option("text", "Text only"),
            ];
        },
        blockquoteTweetSkinOptions() {
            return [
                option("classic", "Classic"),
                option("bubble", "Bubble"),
                option("link", "Link"),
            ];
        },
        blockquoteTweetTargetOptions() {
            return [
                option("current", "Current Page"),
                option("none", "None"),
                option("custom", "Custom Link"),
            ];
        },
        buttonColorOptions() {
            return [option("official", "Official"), option("custom", "Custom")];
        },
        shareNetworkOptions() {
            return [
                ["facebook", "Facebook"],
                ["twitter", "Twitter"],
                ["x", "X"],
                ["threads", "Threads"],
                ["linkedin", "LinkedIn"],
                ["pinterest", "Pinterest"],
                ["reddit", "Reddit"],
                ["whatsapp", "WhatsApp"],
                ["telegram", "Telegram"],
                ["email", "Email"],
                ["print", "Print"],
                ["copy", "Copy Link"],
                ["vk", "VK"],
                ["tumblr", "Tumblr"],
                ["skype", "Skype"],
                ["digg", "Digg"],
                ["stumbleupon", "StumbleUpon"],
                ["pocket", "Pocket"],
                ["flipboard", "Flipboard"],
                ["buffer", "Buffer"],
                ["weibo", "Weibo"],
                ["blogger", "Blogger"],
                ["odnoklassniki", "Odnoklassniki"],
            ].map(([value, label]) => option(value, label));
        },
        shareViewOptions() {
            return [
                option("icon_text", "Icon & Text"),
                option("icon", "Icon only"),
                option("text", "Text only"),
            ];
        },
        shareSkinOptions() {
            return [
                option("flat", "Flat"),
                option("gradient", "Gradient"),
                option("minimal", "Minimal"),
                option("framed", "Framed"),
                option("box", "Box"),
                option("3d", "3D"),
            ];
        },
        shareShapeOptions() {
            return [
                option("rounded", "Rounded"),
                option("square", "Square"),
                option("circle", "Circle"),
                option("none", "None"),
            ];
        },
        shareColumnsOptions() {
            return [
                option("auto", "Auto"),
                ...["1", "2", "3", "4", "5", "6"].map((value) => option(value, value)),
            ];
        },
        shareTargetOptions() {
            return [option("current", "Current Page"), option("custom", "Custom")];
        },
        codeLanguageOptions() {
            return [
                option("plain-text", "Plain Text"),
                option("markup", "Markup"),
                option("html", "HTML"),
                option("xml", "XML"),
                option("svg", "SVG"),
                option("mathml", "MathML"),
                option("ssml", "SSML"),
                option("atom", "Atom"),
                option("rss", "RSS"),
                option("css", "CSS"),
                option("less", "Less"),
                option("sass", "Sass"),
                option("scss", "SCSS"),
                option("javascript", "JavaScript"),
                option("typescript", "TypeScript"),
                option("actionscript", "ActionScript"),
                option("c", "C"),
                option("cpp", "C++"),
                option("csharp", "C#"),
                option("java", "Java"),
                option("kotlin", "Kotlin"),
                option("dart", "Dart"),
                option("go", "Go"),
                option("rust", "Rust"),
                option("swift", "Swift"),
                option("objectivec", "Objective-C"),
                option("php", "PHP"),
                option("python", "Python"),
                option("ruby", "Ruby"),
                option("perl", "Perl"),
                option("lua", "Lua"),
                option("r", "R"),
                option("matlab", "MATLAB"),
                option("sql", "SQL"),
                option("plsql", "PL/SQL"),
                option("json", "JSON"),
                option("json5", "JSON5"),
                option("yaml", "YAML"),
                option("toml", "TOML"),
                option("markdown", "Markdown"),
                option("mdx", "MDX"),
                option("bash", "Bash"),
                option("shell", "Shell"),
                option("powershell", "PowerShell"),
                option("batch", "Batch"),
                option("docker", "Docker"),
                option("git", "Git"),
                option("diff", "Diff"),
                option("http", "HTTP"),
                option("graphql", "GraphQL"),
                option("jsx", "JSX"),
                option("tsx", "TSX"),
                option("vue", "Vue"),
                option("twig", "Twig"),
                option("blade", "Blade"),
                option("pascal", "Pascal"),
                option("haskell", "Haskell"),
                option("scala", "Scala"),
                option("groovy", "Groovy"),
                option("elixir", "Elixir"),
                option("erlang", "Erlang"),
                option("clojure", "Clojure"),
                option("fsharp", "F#"),
                option("fortran", "Fortran"),
                option("cobol", "COBOL"),
                option("basic", "BASIC"),
                option("arduino", "Arduino"),
            ];
        },
        codeThemeOptions() {
            return [option("light", "Light"), option("dark", "Dark")];
        },
        mediaCarouselSkinOptions() {
            return [option("carousel", "Carousel"), option("slideshow", "Slideshow"), option("coverflow", "Coverflow")];
        },
        mediaCarouselTypeOptions() {
            return [option("image", "Image"), option("video", "Video")];
        },
        mediaCarouselLinkOptions() {
            return [option("none", "None"), option("media", "Media File"), option("custom", "Custom URL")];
        },
        mediaCarouselEffects() {
            return [option("slide", "Slide"), option("fade", "Fade"), option("cube", "Cube")];
        },
        mediaCarouselRatioOptions() {
            return ["1:1", "4:3", "16:9", "21:9"].map((value) => option(value));
        },
        mediaCarouselOverlayOptions() {
            return [option("none", "None"), option("text", "Text"), option("icon", "Icon")];
        },
        mediaCarouselCaptionOptions() {
            return [option("title", "Caption Title"), option("caption", "Caption"), option("description", "Description")];
        },
        mediaCarouselOverlayIconOptions() {
            return [option("search-plus", "Search Plus"), option("plus-circle", "Plus Circle"), option("eye", "Eye"), option("link", "Link")];
        },
        mediaCarouselOverlayAnimations() {
            return [option("fade", "Fade"), option("slide-up", "Slide Up"), option("slide-down", "Slide Down"), option("slide-right", "Slide Right"), option("slide-left", "Slide Left"), option("zoom-in", "Zoom In")];
        },
        mediaCarouselImageFitOptions() {
            return [option("cover", "Cover"), option("contain", "Contain"), option("auto", "Auto")];
        },
        reviewIconColorOptions() {
            return [option("official", "Official"), option("custom", "Custom")];
        },
        reviewRatingIconOptions() {
            return [
                option("fontawesome", "Font Awesome"),
                option("unicode", "Unicode"),
            ];
        },
        reviewUnmarkedOptions() {
            return [option("solid", "Solid"), option("outline", "Outline")];
        },
        graphicOptions() {
            return [
                option("none", "None"),
                option("image", "Image"),
                option("icon", "Icon"),
            ];
        },
        flipEffects() {
            return ["flip", "slide", "push", "zoom-in", "zoom-out", "fade"].map(
                (v) => option(v),
            );
        },
        directionOptions() {
            return ["left", "right", "up", "down"].map((v) => option(v));
        },
        hoverEffects() {
            return [
                "none",
                "zoom-in",
                "zoom-out",
                "move-left",
                "move-right",
                "move-up",
                "move-down",
            ].map((v) => option(v));
        },
    },
    methods: {
        ensureFormRowGrid() {
            if (this.type !== "form") return;
            const api = window.PageBuilderElementorV24FormRowGrid;
            if (!api || !this.s) return;
            if (!this.s.rowGrid || !Array.isArray(this.s.rowGrid.steps)) {
                const normalized = api.normalizeSettings(this.s);
                this.s.rowGrid = normalized.rowGrid;
                this.s.fields = normalized.fields;
            }
        },
        syncFormRowGrid() {
            const api = window.PageBuilderElementorV24FormRowGrid;
            if (!api || !this.s.rowGrid) return;
            this.s.fields = api.projectFields(this.s.rowGrid);
        },
        removeFormRowGridField(itemId) {
            const api = window.PageBuilderElementorV24FormRowGrid;
            if (!api || !this.s.rowGrid) return;
            api.removeItem(this.s.rowGrid, itemId);
            this.syncFormRowGrid();
        },
        previewFormMessage() {
            window.dispatchEvent(new CustomEvent("pagebuilder:v24-form-message-preview", {
                detail: {
                    nodeId: String(this.node.id),
                    state: this.formMessageEditorState === "error" ? "error" : "success",
                },
            }));
        },
        syncNavigation(value) {
            const navigation = ["both", "arrows", "dots", "none"].includes(value)
                ? value
                : "both";
            this.s.navigation = navigation;
            this.s.arrows = ["both", "arrows"].includes(navigation);
            if (["dots", "both"].includes(navigation) && this.s.pagination === "none") {
                this.s.pagination = "dots";
            }
            if (navigation === "none") this.s.pagination = "none";
        },
        addItem(key) {
            const id = key + "-" + Date.now();
            const templates = {
                fields: {
                    id,
                    label: "New Field",
                    type: "text",
                    placeholder: "",
                    defaultValue: "",
                    required: false,
                    width: 100,
                    optionsText: "Option 1|option-1\nOption 2|option-2",
                    multiple: false,
                    inlineList: false,
                    rows: 4,
                    acceptanceText: "I agree to the terms.",
                    html: "HTML content",
                    fileTypes: "",
                    stepTitle: "Step",
                    stepDescription: "",
                    nextButton: "Next",
                    previousButton: "Previous",
                    datasetMode: "static",
                    datasetId: "",
                    datasetParentFieldId: "",
                    conditionalLogic: { enabled: false, relation: "all", rules: [] },
                },
                slides: {
                    id,
                    title: "New Slide",
                    description: "Slide description",
                    buttonText: "Click Here",
                    linkUrl: "",
                    backgroundImage: "",
                },
                hotspots: {
                    id,
                    label: "+",
                    tooltip: "Hotspot item",
                    x: 50,
                    y: 50,
                    linkUrl: "",
                },
                items: {
                    id,
                    title: "New Item",
                    description: "Item description",
                    price: "$0",
                    imageUrl: "",
                    linkUrl: "",
                },
                features: {
                    id,
                    text: "Feature item",
                    iconSource: "library",
                    iconStyle: "solid",
                    iconName: "check",
                    iconClass: "fas fa-check",
                    iconSvg: "",
                },
                shareButtons: {
                    id,
                    network: "facebook",
                    customLabel: "Facebook",
                },
            };
            const item =
                key === "items" && this.type === "media_carousel"
                    ? {
                          id,
                          type: "image",
                          imageUrl: "https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png",
                          videoUrl: "",
                          linkType: "none",
                          linkUrl: "",
                          linkTarget: "",
                          linkNofollow: false,
                          linkCustomAttributes: [],
                          title: "",
                          caption: "",
                          description: "",
                      }
                    : key === "items" && this.type === "reviews"
                    ? {
                          id,
                          imageUrl:
                              "https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png",
                          name: "John Doe",
                          title: "@username",
                          rating: "",
                          review: "Write your review here.",
                          linkUrl: "",
                          linkTarget: "",
                          linkNofollow: false,
                          linkCustomAttributes: [],
                          iconSource: "library",
                          iconStyle: "brands",
                          iconName: "twitter",
                          iconClass: "fab fa-twitter",
                          iconSvg: "",
                      }
                    : key === "items" && this.type === "testimonial_carousel"
                    ? {
                          id,
                          imageUrl:
                              "https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png",
                          name: "John Doe",
                          title: "CEO",
                          content: "Write your testimonial here.",
                      }
                    : key === "items" && this.type === "share_buttons"
                    ? {
                          id,
                          network: "facebook",
                          customLabel: "Facebook",
                      }
                    : key === "items" && this.type === "video_playlist"
                    ? {
                          id,
                          type: "youtube",
                          link: "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
                          title: "Sample video",
                          titleTag: "h4",
                          duration: "0:16",
                          thumbnailUrl: "",
                          sectionContent: "",
                          showContentTabs: false,
                          contentTabOneTitle: "Overview",
                          contentTabOneContent: "",
                          contentTabTwoTitle: "Notes",
                          contentTabTwoContent: "",
                      }
                    : templates[key];
            (this.s[key] ||= []).push({ ...item });
        },
        getVideoData(item) {
            if (!item || !["youtube", "vimeo"].includes(item.type)) return;
            const raw = String(item.link || "").trim();
            const youtube = raw.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/i);
            if (youtube && !item.thumbnailUrl) {
                item.thumbnailUrl = `https://img.youtube.com/vi/${youtube[1]}/hqdefault.jpg`;
            }
            if (!item.title) item.title = item.type === "vimeo" ? "Vimeo Video" : "YouTube Video";
            return item;
        },
        removeItem(key, index) {
            if (Array.isArray(this.s[key]) && this.s[key].length > 1)
                this.s[key].splice(index, 1);
        },
        duplicateItem(key, index) {
            if (!Array.isArray(this.s[key]) || !this.s[key][index]) return;
            const clone = JSON.parse(JSON.stringify(this.s[key][index]));
            clone.id = `${key}-${Date.now()}`;
            this.s[key].splice(index + 1, 0, clone);
        },
        moveItem(key, payload) {
            const list = this.s[key];
            const index = Number(payload?.index);
            const direction = Number(payload?.direction);
            const target = index + direction;
            if (!Array.isArray(list) || !list[index] || !Number.isInteger(target) || target < 0 || target >= list.length) return;
            [list[index], list[target]] = [list[target], list[index]];
        },
        addRotating() {
            (this.s.rotatingTexts ||= []).push("Animated Text");
        },
        removeRotating(index) {
            if (this.s.rotatingTexts?.length > 1)
                this.s.rotatingTexts.splice(index, 1);
        },
        formDatasetEndpoints() {
            return window.PAGE_BUILDER_ELEMENTOR_V24_CONTEXT?.formDatasetEndpoints || {};
        },
        async loadFormDatasets(force = false) {
            if ((!force && this.formDatasetsLoaded) || this.formDatasetLoading) return false;
            const url = this.formDatasetEndpoints().index;
            if (!url) return false;
            this.formDatasetLoading = true;
            this.formDatasetError = "";
            try {
                const response = await fetch(url, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
                if (!response.ok) throw new Error("Could not load shared datasets.");
                const payload = await response.json();
                if (payload?.success !== true || !Array.isArray(payload.data)) throw new Error(payload?.message || "Could not load shared datasets.");
                this.formDatasets = Array.isArray(payload.data) ? payload.data : [];
                window.PAGE_BUILDER_ELEMENTOR_V24_FORM_DATASETS = this.formDatasets;
                this.formDatasetsLoaded = true;
                return true;
            } catch (error) {
                this.formDatasetError = error.message || "Could not load shared datasets.";
                console.error(error);
                return false;
            } finally {
                this.formDatasetLoading = false;
            }
        },
        async openFormDatasetModal(field) {
            await this.loadFormDatasets();
            this.formDatasetField = field;
            this.formDatasetNotice = "";
            this.formDatasetError = "";
            this.formDatasetModalOpen = true;
        },
        closeFormDatasetModal() {
            this.formDatasetModalOpen = false;
            this.formDatasetField = null;
            this.formDatasetNotice = "";
            this.formDatasetError = "";
        },
        async saveFormDataset(payload) {
            const field = this.formDatasetField;
            const dataset = payload?.dataset || {};
            if (!field || !payload?.field || !dataset) return;
            if (payload.field.datasetMode !== "dataset") {
                Object.assign(field, payload.field, { datasetMode: "static", datasetId: "", datasetParentFieldId: "" });
                this.formDatasetError = "";
                this.formDatasetNotice = "Dataset options applied successfully.";
                return;
            }
            this.formDatasetLoading = true;
            this.formDatasetError = "";
            try {
                const endpoints = this.formDatasetEndpoints();
                const existingId = dataset.id ? String(dataset.id) : "";
                const url = existingId
                    ? String(endpoints.update || "").replace("__DATASET_ID__", encodeURIComponent(existingId))
                    : endpoints.store;
                const csrfToken = window.PAGE_BUILDER_ELEMENTOR_V24_CONTEXT?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
                const response = await fetch(url, {
                    method: existingId ? "PUT" : "POST",
                    cache: "no-store",
                    credentials: "same-origin",
                    redirect: "manual",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        name: dataset.name || `${payload.field.label || "Form"} Dataset`,
                        schemaVersion: dataset.schemaVersion || 1,
                        nodes: Array.isArray(dataset.nodes) ? dataset.nodes : [],
                    }),
                });
                const responsePayload = await response.json().catch(() => null);
                if (!response.ok || responsePayload?.success !== true || !responsePayload.data) {
                    throw new Error(responsePayload?.message || (response.redirected || response.type === "opaqueredirect" ? "Your session expired. Refresh the editor and try again." : "Could not save the shared dataset."));
                }
                const saved = responsePayload.data;
                Object.assign(field, payload.field, {
                    datasetMode: "dataset",
                    datasetId: String(saved.id || dataset.id || ""),
                });
                const index = this.formDatasets.findIndex((entry) => String(entry.id) === String(saved.id));
                if (index >= 0) this.formDatasets.splice(index, 1, saved);
                else this.formDatasets.push(saved);
                window.PAGE_BUILDER_ELEMENTOR_V24_FORM_DATASETS = this.formDatasets;
                this.formDatasetsLoaded = true;
                this.formDatasetNotice = existingId ? "Dataset updated successfully." : "Dataset created successfully.";
            } catch (error) {
                this.formDatasetError = error.message || "Could not save the shared dataset.";
                console.error(error);
            } finally {
                this.formDatasetLoading = false;
            }
        },
        clearFormDatasetNotice() {
            this.formDatasetNotice = "";
            this.formDatasetError = "";
        },
        async handleDeletedDataset({ datasetId } = {}) {
            const id = String(datasetId || "");
            if (!id) return;
            const refreshed = await this.loadFormDatasets(true);
            if (!refreshed) return;
            if (this.formDatasets.some((entry) => String(entry.id) === id)) {
                this.formDatasetError = "The server still reports this dataset. Delete was not confirmed.";
                return;
            }
            if (this.formDatasetField && String(this.formDatasetField.datasetId) === id) {
                Object.assign(this.formDatasetField, {
                    datasetMode: "static",
                    datasetId: "",
                    datasetParentFieldId: "",
                });
            }
            this.formDatasetNotice = "Dataset deleted successfully.";
        },
        formActionEnabled(action) {
            return (
                Array.isArray(this.s.submitActions) &&
                this.s.submitActions.includes(action)
            );
        },
        toggleFormAction(action, enabled) {
            const actions = Array.isArray(this.s.submitActions)
                ? [...this.s.submitActions]
                : [];
            const index = actions.indexOf(action);
            if (enabled && index < 0) actions.push(action);
            if (!enabled && index >= 0) actions.splice(index, 1);
            this.s.submitActions = actions;
        },
    },
};
</script>

<style>
.pb-form-row-grid-editor {
    display: grid;
    gap: 10px;
    margin-top: 12px;
}
.pb-form-row-grid-editor__toolbar,
.pb-form-row-grid-editor__row-heading,
.pb-form-row-grid-editor__item-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.pb-form-row-grid-editor__toolbar {
    color: #243047;
    font-size: 12px;
}
.pb-form-row-grid-editor__devices,
.pb-form-row-grid-editor__row-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
}
.pb-form-row-grid-editor__devices button,
.pb-form-row-grid-editor__row-controls button,
.pb-form-row-grid-editor__row-controls select,
.pb-form-row-grid-editor__step-actions input {
    min-height: 28px;
    padding: 4px 7px;
    border: 1px solid #d4dceb;
    border-radius: 5px;
    background: #fff;
    color: #526987;
    font-size: 10px;
}
.pb-form-row-grid-editor__devices button.active {
    border-color: #6979f8;
    background: #eef1ff;
    color: #5367ff;
}
.pb-form-row-grid-editor__step,
.pb-form-row-grid-editor__row {
    display: grid;
    gap: 8px;
    padding: 9px;
    border: 1px solid #dfe5f0;
    border-radius: 7px;
    background: #fbfcfe;
}
.pb-form-row-grid-editor__step-heading {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(104px, .65fr);
    gap: 8px;
}
.pb-form-row-grid-editor__step-heading > div:first-child {
    display: grid;
    gap: 5px;
}
.pb-form-row-grid-editor__eyebrow {
    color: #6979f8;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}
.pb-form-row-grid-editor__step-actions {
    display: grid;
    align-content: start;
    gap: 5px;
}
.pb-form-row-grid-editor__columns {
    display: grid;
    gap: 7px;
    align-items: stretch;
}
.pb-form-row-grid-editor__column {
    min-width: 0;
    min-height: 54px;
    padding: 5px;
    border: 1px dashed #b9c6e3;
    border-radius: 6px;
    background: rgba(255, 255, 255, .82);
    transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
}
.pb-form-row-grid-editor__column.is-full {
    grid-column: 1 / -1;
}
.pb-form-row-grid-editor__column.is-dragging {
    border-color: #6979f8;
    background: #f3f5ff;
}
.pb-form-row-grid-editor__dropzone {
    min-height: 42px;
}
.pb-form-row-grid-editor__empty {
    display: grid;
    min-height: 40px;
    place-items: center;
    color: #8492aa;
    font-size: 10px;
    text-align: center;
}
.pb-form-row-grid-editor__item {
    padding: 6px;
    border: 1px solid #d3dcef;
    border-radius: 6px;
    background: #fff;
}
.pb-form-row-grid-editor__item-heading {
    min-height: 25px;
    color: #344054;
    font-size: 11px;
}
.pb-form-row-grid-editor__remove {
    display: grid;
    width: 25px;
    height: 25px;
    place-items: center;
    padding: 0;
    border: 1px solid #d4dceb;
    border-radius: 5px;
    background: #fff;
    color: #6979f8;
    color: #667085;
    cursor: pointer;
}
.pb-form-row-grid-editor__add-row {
    min-height: 30px;
    padding: 5px 8px;
    border: 1px dashed #aebdf7;
    border-radius: 5px;
    background: #f4f6ff;
    color: #5367ff;
    font-size: 10px;
    font-weight: 700;
}
/* Compact internal Form Row Grid editor. */
.pb-form-row-grid-editor {
    gap: 8px;
}
.pb-form-row-grid-editor__toolbar {
    font-size: 13px;
}
.pb-form-row-grid-editor__devices {
    flex-wrap: nowrap;
    gap: 0;
    overflow: hidden;
    border: 1px solid #d7deeb;
    border-radius: 7px;
    background: #fff;
}
.pb-form-row-grid-editor__devices button {
    min-height: 31px;
    padding: 5px 10px;
    border: 0;
    border-right: 1px solid #e4e8f0;
    border-radius: 0;
    color: #667085;
    font-size: 11px;
    cursor: pointer;
}
.pb-form-row-grid-editor__devices button:last-child {
    border-right: 0;
}
.pb-form-row-grid-editor__devices button.active {
    border-color: transparent;
    background: #f0efff;
    color: #5b4df6;
    font-weight: 700;
}
.pb-form-row-grid-editor__step {
    position: relative;
    gap: 8px;
    padding: 0;
    border: 0;
    background: transparent;
}
.pb-form-row-grid-editor__step-delete {
    position: absolute;
    z-index: 2;
    top: 5px;
    right: 7px;
    display: grid;
    width: 28px;
    height: 28px;
    place-items: center;
    padding: 0;
    border: 1px solid #d7deeb;
    border-radius: 6px;
    background: #fff;
    color: #667085;
    cursor: pointer;
}
.pb-form-row-grid-editor__step-delete:hover {
    border-color: #f0b4b4;
    background: #fff5f5;
    color: #b42318;
}
.pb-form-row-grid-editor__step-settings,
.pb-form-row-grid-editor__row {
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fff;
}
.pb-form-row-grid-editor__step-settings summary {
    display: flex;
    min-height: 38px;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 7px 10px;
    color: #344054;
    font-size: 11px;
    cursor: pointer;
    list-style: none;
}
.pb-form-row-grid-editor__step-settings.has-delete summary {
    padding-right: 43px;
}
.pb-form-row-grid-editor__step-settings summary::-webkit-details-marker {
    display: none;
}
.pb-form-row-grid-editor__step-settings summary::marker {
    content: "";
}
.pb-form-row-grid-editor__step-settings > summary::before {
    display: none !important;
    margin: 0 !important;
    content: none !important;
}
.pb-form-row-grid-editor__step-settings summary > span {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 7px;
}
.pb-form-row-grid-editor__step-settings summary small {
    overflow: hidden;
    color: #98a2b3;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-form-row-grid-editor__step-settings summary > i {
    color: #98a2b3;
    font-size: 9px;
    transition: transform .15s ease;
}
.pb-form-row-grid-editor__step-settings[open] summary > i {
    transform: rotate(90deg);
}
.pb-form-row-grid-editor__step-content {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    padding: 0 10px 10px;
}
.pb-form-row-grid-editor__step-content textarea {
    grid-column: 1 / -1;
}
.pb-form-row-grid-editor__row {
    gap: 7px;
    padding: 8px;
    background: #f8f9fc;
}
.pb-form-row-grid-editor__row-title {
    display: inline-flex;
    min-width: 0;
    align-items: center;
    gap: 6px;
    color: #344054;
    font-size: 11px;
}
.pb-form-row-grid-editor__row-title i {
    color: #7165ee;
    font-size: 10px;
}
.pb-form-row-grid-editor__row-count {
    min-width: 88px;
    min-height: 29px;
    margin-left: auto;
    padding: 4px 25px 4px 8px;
    border: 1px solid #d7deeb;
    border-radius: 6px;
    background: #fff;
    color: #475467;
    font-size: 10px;
}
.pb-form-row-grid-editor__delete {
    display: grid;
    width: 29px;
    height: 29px;
    flex: 0 0 29px;
    place-items: center;
    padding: 0;
    border: 1px solid #d7deeb;
    border-radius: 6px;
    background: #fff;
    color: #667085;
    cursor: pointer;
}
.pb-form-row-grid-editor__delete:disabled {
    cursor: not-allowed;
    opacity: .4;
}
.pb-form-row-grid-editor__columns {
    gap: 6px;
}
.pb-form-row-grid-editor__column {
    min-height: 45px;
    padding: 0;
    border-color: #c8d1e3;
    border-radius: 7px;
    background: #fff;
}
.pb-form-row-grid-editor__column.is-dragging {
    border-color: #7467f5;
    background: #f4f3ff;
}
.pb-form-row-grid-editor__dropzone {
    min-height: 43px;
    width: 100%;
}
.pb-form-row-grid-editor__empty {
    min-height: 41px;
}
.pb-form-row-grid-editor__item {
    overflow: hidden;
    padding: 0;
    border: 0;
    border-radius: 7px;
}
.pb-form-row-grid-editor__item-heading {
    min-height: 41px;
    padding: 5px 6px;
}
.pb-form-row-grid-editor__remove,
.pb-form-row-grid-editor__disclosure {
    display: grid;
    height: 29px;
    place-items: center;
    padding: 0;
    border: 0;
    background: transparent;
}
.pb-form-row-grid-editor__remove {
    width: 27px;
    flex: 0 0 27px;
    border-radius: 5px;
}
.pb-form-row-grid-editor__remove:hover {
    background: #f1f2f6;
}
.pb-form-row-grid-editor__disclosure {
    display: flex;
    min-width: 0;
    flex: 1 1 auto;
    justify-content: flex-start;
    gap: 7px;
    overflow: hidden;
    color: #344054;
    cursor: pointer;
    text-align: left;
}
.pb-form-row-grid-editor__disclosure i {
    width: 9px;
    color: #98a2b3;
    font-size: 8px;
}
.pb-form-row-grid-editor__disclosure strong {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-form-row-grid-editor__disclosure.is-static {
    padding-left: 5px;
    cursor: default;
}
.pb-form-row-grid-editor__type {
    flex: 0 1 auto;
    overflow: hidden;
    color: #98a2b3;
    font-size: 9px;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-form-row-grid-editor__item-body {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 8px;
    padding: 10px;
    border-top: 1px solid #eaecf0;
}
.pb-form-row-grid-editor__item-body > .pb-form-group,
.pb-form-row-grid-editor__item-body > .pb-form-dataset-trigger {
    min-width: 0;
    margin-bottom: 0;
}
.pb-form-row-grid-editor__item-body > .pb-form-dataset-trigger,
.pb-form-row-grid-editor__item-body > .pb-control--toggle {
    grid-column: 1 / -1;
}
.pb-form-row-grid-editor__actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 7px;
}
.pb-form-row-grid-editor__actions.is-single {
    grid-template-columns: 1fr;
}
.pb-form-row-grid-editor__actions button {
    min-height: 34px;
    padding: 6px 8px;
    border: 1px dashed #aebdf7;
    border-radius: 7px;
    background: #f7f7ff;
    color: #5b4df6;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
}
.pb-form-row-grid-editor__actions button:hover {
    border-color: #7467f5;
    background: #f0efff;
}
.pb-form-row-grid-editor__row-toggle {
    display: flex;
    min-width: 0;
    flex: 1 1 auto;
    align-items: center;
    gap: 7px;
    padding: 0;
    border: 0;
    background: transparent;
    color: #344054;
    font-size: 11px;
    text-align: left;
    cursor: pointer;
}
.pb-form-row-grid-editor__row-toggle > i:first-child {
    color: #7165ee;
    font-size: 10px;
}
.pb-form-row-grid-editor__row-chevron {
    margin-left: auto;
    color: #98a2b3;
    font-size: 8px;
}
.pb-form-row-grid-editor__row-body {
    display: grid;
    gap: 9px;
}
.pb-form-row-grid-editor__subheading {
    color: #475467;
    font-size: 10px;
    font-weight: 700;
}
.pb-form-field-more {
    grid-column: 1 / -1;
    border-top: 1px solid #eaecf0;
}
.pb-form-field-more > summary {
    display: flex;
    min-height: 34px;
    align-items: center;
    justify-content: space-between;
    padding: 4px 0;
    color: #667085;
    font-size: 9px;
    font-weight: 700;
    cursor: pointer;
    list-style: none;
}
.pb-form-field-more > summary::-webkit-details-marker {
    display: none;
}
.pb-form-field-more > summary::marker,
.pb-form-field-more > summary::before {
    display: none !important;
    content: none !important;
}
.pb-form-field-more > summary::after {
    color: #98a2b3;
    content: "+";
    font-size: 13px;
    font-weight: 500;
}
.pb-form-field-more[open] > summary::after {
    content: "−";
}
.pb-form-field-more__body {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 8px;
    padding-top: 6px;
}
.pb-form-field-more__body > .pb-form-group,
.pb-form-field-more__body > .pb-form-dataset-trigger {
    min-width: 0;
    margin-bottom: 0;
}
.pb-form-field-more__body > .pb-form-dataset-trigger,
.pb-form-field-more__body > .pb-toggle-label-row {
    grid-column: 1 / -1;
}
.pb-form-row-span-control {
    display: grid;
    grid-column: 1 / -1;
    gap: 5px;
}
.pb-form-row-span-control > label {
    color: #475467;
    font-size: 9px;
    font-weight: 600;
}
.pb-form-row-span-control > div {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 42px;
    gap: 6px;
}
.pb-form-row-span-control select,
.pb-form-row-span-control__device {
    min-height: 31px;
    border: 1px solid #d7deeb;
    border-radius: 6px;
    background: #fff;
    color: #344054;
    font-size: 10px;
}
.pb-form-row-span-control select {
    min-width: 0;
    padding: 5px 8px;
}
.pb-form-row-span-control__device {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 0;
    color: #6558e8;
    cursor: pointer;
}
.pb-form-row-span-control__device .fa-chevron-down {
    color: #98a2b3;
    font-size: 7px;
}
.pb-form-row-grid-editor__field-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 6px;
}
.pb-form-row-grid-editor__field-list-item {
    overflow: hidden;
    border: 1px solid #dfe5f0;
    border-radius: 7px;
    background: #fff;
}
.pb-form-row-grid-editor__field-list-item.is-open {
    border-color: #c9c4ff;
    box-shadow: 0 0 0 1px rgba(101, 88, 232, .05);
}
.pb-form-row-grid-editor__field-header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 29px;
    align-items: center;
    background: #fff;
}
.pb-form-row-grid-editor__field-select {
    display: grid;
    min-width: 0;
    min-height: 38px;
    grid-template-columns: 18px minmax(0, 1fr) auto auto;
    align-items: center;
    gap: 6px;
    padding: 5px 4px 5px 8px;
    border: 0;
    background: transparent;
    color: #344054;
    font-size: 10px;
    text-align: left;
    cursor: pointer;
}
.pb-form-row-grid-editor__field-select:disabled {
    cursor: default;
    opacity: 1;
}
.pb-form-row-grid-editor__field-select > i {
    color: #8a7df0;
    font-size: 9px;
}
.pb-form-row-grid-editor__field-chevron {
    width: 10px;
    color: #98a2b3 !important;
}
.pb-form-row-grid-editor__field-select strong,
.pb-form-row-grid-editor__field-select .pb-form-row-grid-editor__type {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-form-row-grid-editor__column-badge {
    padding: 3px 5px;
    border-radius: 4px;
    background: #f0efff;
    color: #6558e8;
    font-size: 8px;
    font-weight: 700;
    white-space: nowrap;
}
.pb-form-row-grid-editor__field-header > .pb-form-row-grid-editor__remove {
    margin-right: 2px;
}
.pb-form-row-grid-editor__field-body {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px 8px;
    padding: 10px;
    border-top: 1px solid #eaecf0;
    background: #fff;
}
.pb-form-row-grid-editor__field-body .pb-form-field-more__body {
    grid-template-columns: 1fr;
}
.pb-form-row-grid-editor__field-body > .pb-form-group,
.pb-form-row-grid-editor__field-body > .pb-form-dataset-trigger {
    min-width: 0;
    margin-bottom: 0;
}
.pb-form-row-grid-editor__field-body > .pb-form-dataset-trigger,
.pb-form-row-grid-editor__field-body > .pb-toggle-label-row,
.pb-form-row-grid-editor__field-body > .pb-control--toggle {
    grid-column: 1 / -1;
}
.pb-form-row-grid-editor__field-list-empty {
    padding: 9px;
    color: #98a2b3;
    font-size: 9px;
    text-align: center;
}
.pb-form-row-grid-editor__row-add {
    min-height: 34px;
    padding: 6px 8px;
    border: 1px dashed #aebdf7;
    border-radius: 7px;
    background: #fff;
    color: #5b4df6;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
}
.pb-form-row-grid-editor__row-add:hover {
    border-color: #7467f5;
    background: #f0efff;
}
.pb-form-row-grid-editor__form-actions {
    display: grid;
    min-height: 42px;
    grid-template-columns: 22px minmax(0, 1fr) auto;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fff;
    color: #344054;
}
.pb-form-row-grid-editor__form-actions > i {
    color: #6558e8;
    font-size: 11px;
}
.pb-form-row-grid-editor__form-actions > span:nth-child(2) {
    display: grid;
    min-width: 0;
    gap: 1px;
}
.pb-form-row-grid-editor__form-actions strong {
    font-size: 10px;
}
.pb-form-row-grid-editor__form-actions small {
    color: #98a2b3;
    font-size: 8px;
}
.pb-form-row-grid-editor__footer-width {
    min-width: 68px;
    min-height: 29px;
    padding: 4px 24px 4px 8px;
    border: 1px solid #d7deeb;
    border-radius: 6px;
    background: #fff;
    color: #5547df;
    font-size: 9px;
    font-weight: 700;
}
.pb-form-row-grid-editor__add-step {
    margin-top: 1px;
}
.pb-form-step-help {
    display: flex;
    align-items: flex-start;
    gap: 7px;
    margin: 2px 0 0;
    padding: 9px 10px;
    border: 1px solid #e1defe;
    border-radius: 7px;
    background: #f8f7ff;
    color: #667085;
    font-size: 10px;
    line-height: 1.45;
}
.pb-form-step-help i {
    margin-top: 2px;
    color: #6558e8;
}
.pb-form-row-grid-editor + .pb-form-group {
    margin-top: 18px;
}
.pb-widget-settings--pro .pb-form-group {
    margin-bottom: 14px;
}
.pb-widget-settings--pro .pb-input,
.pb-widget-settings--pro .pb-select,
.pb-widget-settings--pro .pb-textarea {
    width: 100%;
    min-height: 31px;
}
.pb-widget-settings--pro .pb-collapsible-body {
    padding-top: 13px;
}
.pb-form-dataset-trigger {
    display: grid;
    grid-template-columns: 30px minmax(0, 1fr) auto;
    align-items: center;
    gap: 8px;
    width: 100%;
    min-height: 36px;
    margin: 0 0 16px;
    padding: 7px 10px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fff;
    color: #243047;
    text-align: left;
    cursor: pointer;
}
.pb-form-dataset-trigger:hover {
    border-color: #9aa8c7;
    box-shadow: 0 0 0 2px rgba(91, 108, 255, 0.08);
}
.pb-form-dataset-trigger__icon {
    display: grid;
    width: 28px;
    height: 28px;
    place-items: center;
    border-radius: 8px;
    background: #eef1ff;
    color: #5b6cff;
    font-size: 12px;
}
.pb-form-dataset-trigger strong,
.pb-form-dataset-trigger small {
    display: block;
}
.pb-form-dataset-trigger strong {
    font-size: 12px;
    font-weight: 600;
}
.pb-form-dataset-trigger small {
    margin-top: 2px;
    color: #6f7b91;
    font-size: 10px;
    font-weight: 500;
}
.pb-form-dataset-trigger > .fas {
    color: #5b6cff;
    font-size: 11px;
}
.pb-form-dataset-modal-backdrop {
    z-index: 10000;
}
.pb-panel.left:has(.pb-form-dataset-modal-backdrop) {
    z-index: 10001;
}
.pb-form-dataset-modal {
    display: flex;
    flex-direction: column;
    position: relative;
    width: min(1080px, calc(100vw - 48px));
    max-width: 96vw;
    max-height: 90vh;
    overflow: hidden;
    border-radius: 1rem;
}
.pb-form-dataset-modal__header,
.pb-form-dataset-modal__footer {
    flex: 0 0 auto;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    padding: 18px 28px;
    border-bottom: 1px solid #dfe5f0;
    background: #fff;
}
.pb-form-dataset-modal__footer {
    align-items: center;
    border-top: 1px solid #dfe5f0;
    border-bottom: 0;
}
.pb-form-dataset-modal__title {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.pb-form-dataset-modal__title > .fas {
    display: grid;
    width: 34px;
    height: 34px;
    place-items: center;
    border-radius: 9px;
    background: #eef1ff;
    color: #5b6cff;
    font-size: 15px;
}
.pb-form-dataset-modal__eyebrow,
.pb-form-dataset__eyebrow {
    display: block;
    color: #8995ab;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
}
.pb-form-dataset-modal__title h2 {
    margin: 4px 0 3px;
    color: #1a2340;
    font-size: 16px;
    font-weight: 700;
    line-height: 20px;
}
.pb-form-dataset-modal__title p {
    margin: 0;
    color: #6f7b91;
    font-size: 11px;
    line-height: 16px;
}
.pb-form-dataset-modal__title p strong {
    color: #5b6cff;
}
.pb-form-dataset-modal__context {
    flex: 0 0 auto;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr)) auto;
    align-items: end;
    gap: 16px;
    padding: 12px 28px 14px;
    border-bottom: 1px solid #dfe5f0;
    background: #fff;
}
.pb-form-dataset-modal__context .pb-form-group {
    flex: 1 1 0;
    min-width: 0;
    margin-bottom: 0 !important;
}
.side-panel.pb-panel.left .v24-properties-section .pb-form-dataset-modal-backdrop .pb-form-dataset-modal__context .pb-form-group {
    margin-bottom: 0 !important;
}
.pb-form-dataset-modal__context .pb-btn {
    flex: 0 0 auto;
    align-self: end;
    min-height: 34px;
    margin-bottom: 0;
    white-space: nowrap;
}
.pb-form-dataset-picker {
    position: relative;
}
.pb-form-dataset-picker__trigger {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: #243047;
    text-align: left;
    cursor: pointer;
}
.pb-form-dataset-picker__trigger > span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-form-dataset-picker__trigger > .fas {
    flex: 0 0 auto;
    color: #6f7b91;
    font-size: 10px;
}
.pb-form-dataset-picker__menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 10003;
    display: grid;
    gap: 2px;
    padding: 4px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 12px 28px rgba(32, 46, 84, .16);
}
.pb-form-dataset-picker__option,
.pb-form-dataset-picker__delete {
    display: flex;
    width: 100%;
    align-items: center;
    gap: 8px;
    min-height: 32px;
    padding: 7px 9px;
    border: 0;
    border-radius: 5px;
    background: transparent;
    color: #243047;
    font: inherit;
    font-size: 11px;
    text-align: left;
    cursor: pointer;
}
.pb-form-dataset-picker__option:hover,
.pb-form-dataset-picker__option[aria-selected="true"] {
    background: #eef1ff;
}
.pb-form-dataset-picker__separator {
    height: 1px;
    margin: 3px 0;
    background: #e7ebf3;
}
.pb-form-dataset-picker__delete {
    color: #c73545;
    font-weight: 700;
}
.pb-form-dataset-picker__delete:hover,
.pb-form-dataset-picker__delete:focus-visible {
    background: #fff0f1;
}
.pb-form-dataset-picker__delete > .fas {
    width: 14px;
    text-align: center;
}
.pb-form-dataset-delete-backdrop {
    z-index: 10004;
    background: rgba(15, 20, 40, .55);
}
.pb-form-dataset-delete-modal {
    width: min(440px, calc(100vw - 40px));
    max-height: none;
    overflow: visible;
    padding: 30px 32px 26px;
    border-radius: 1rem;
    text-align: center;
}
.pb-form-dataset-delete-modal__icon {
    display: grid;
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    place-items: center;
    border-radius: 50%;
    background: #fff0f1;
    color: #c73545;
    font-size: 18px;
}
.pb-form-dataset-delete-modal h2 {
    margin: 0;
    color: #1a2340;
    font-size: 18px;
    line-height: 24px;
}
.pb-form-dataset-delete-modal p {
    margin: 10px auto 0;
    max-width: 330px;
    color: #6f7b91;
    font-size: 12px;
    line-height: 18px;
}
.pb-form-dataset-delete-modal__error {
    color: #b42318 !important;
    font-weight: 600;
}
.pb-form-dataset-delete-modal__actions {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}
.pb-form-dataset-delete-modal__confirm {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-color: #c73545;
    background: #c73545;
    color: #fff;
}
.pb-form-dataset-delete-modal__confirm:hover,
.pb-form-dataset-delete-modal__confirm:focus-visible {
    border-color: #ae2938;
    background: #ae2938;
}
.pb-form-dataset-toast-container {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 10005;
    pointer-events: none;
}
.pb-form-dataset-modal__footer .pb-btn.primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.pb-form-dataset-toast-container .toast {
    pointer-events: auto;
    min-width: 300px;
    max-width: 440px;
    border: 1px solid #dfe5f0;
    box-shadow: 0 12px 30px rgba(15, 20, 40, .16);
}
.pb-form-dataset-toast-container .ph-notice-toast {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    background-clip: padding-box;
    background-color: #f8f9fa;
}
.pb-form-dataset-toast-container .ph-callout-no-border {
    border: 0;
}
.pb-form-dataset-toast-container .ph-callout-success {
    color: #0f5132;
    background-color: #e9f7ef;
}
.pb-form-dataset-toast-container .ph-callout-danger {
    color: #842029;
    background-color: #fff5f5;
}
.pb-form-dataset-toast-container .ph-callout-success::before,
.pb-form-dataset-toast-container .ph-callout-danger::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 4px;
    inset-block-start: 0;
    inset-inline-start: 0;
}
.pb-form-dataset-toast-container .ph-callout-success::before {
    background-color: #198754;
}
.pb-form-dataset-toast-container .ph-callout-danger::before {
    background-color: #dc3545;
}
.pb-form-dataset-toast-container .toast-header-title {
    position: relative;
    padding-left: 2rem;
}
.pb-form-dataset-toast-container .toast-header-title::before,
.pb-form-dataset-toast-container .toast-header-title::after {
    font-weight: 900;
    font-family: "Font Awesome 5 Duotone";
    left: 0;
    top: 50%;
    width: 1.25em;
    position: absolute;
    text-align: center;
    line-height: .75em;
    display: inline-block;
    transform: translateY(-50%);
}
.pb-form-dataset-toast-container .toast-header-title::before {
    color: inherit;
    opacity: 1;
}
.pb-form-dataset-toast-container .toast-header-title::after {
    color: inherit;
    opacity: .4;
}
.pb-form-dataset-toast-container .ph-callout-success .toast-header-icon::before {
    content: "\f058";
}
.pb-form-dataset-toast-container .ph-callout-success .toast-header-icon::after {
    content: "\10f058";
}
.pb-form-dataset-toast-container .ph-callout-danger .toast-header-icon::before {
    content: "\f057";
}
.pb-form-dataset-toast-container .ph-callout-danger .toast-header-icon::after {
    content: "\10f057";
}
.pb-form-dataset-toast-container .toast-body {
    color: #344054;
}
.pb-form-dataset-tabs {
    flex: 0 0 auto;
    margin: 0 28px 0;
}
.pb-form-dataset-tabs {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0;
}
.pb-form-dataset-modal .pb-form-dataset-tabs {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    flex-wrap: nowrap;
}
.pb-form-dataset-tabs > button {
    flex: none !important;
    min-width: 0;
    width: auto !important;
    white-space: nowrap;
}
.pb-form-dataset-body {
    flex: 1 1 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: min(440px, max(120px, calc(90vh - 296px)));
    overflow: auto;
    padding: 18px 0 22px;
}
.pb-form-dataset-tree,
.pb-form-dataset-editor {
    min-width: 0;
    padding: 0 28px;
}
.pb-form-dataset-tree {
    border-right: 1px solid #dfe5f0;
}
.pb-form-dataset-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    min-height: 34px;
}
.pb-form-dataset-heading strong {
    display: block;
    margin-top: 5px;
    color: #243047;
    font-size: 13px;
    font-weight: 700;
    line-height: 17px;
}
.pb-form-dataset-search {
    display: flex;
    align-items: center;
    gap: 7px;
    height: 36px;
    margin: 16px 0 12px;
    padding: 0 9px;
    color: #8995ab;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
}
.pb-form-dataset-search .pb-input {
    min-height: 30px;
    padding: 5px 0;
    border: 0;
    box-shadow: none;
}
.pb-form-dataset-node-list {
    display: grid;
    gap: 5px;
    max-height: 300px;
    overflow: auto;
}
.pb-form-dataset-node {
    position: relative;
    display: grid;
    grid-template-columns: 14px minmax(0, 1fr) auto 26px;
    align-items: center;
    gap: 5px;
    min-height: 36px;
    padding: 6px 8px;
    border: 1px solid transparent;
    border-radius: 5px;
    background: transparent;
    color: #243047;
    text-align: left;
    cursor: pointer;
}
.pb-form-dataset-node:not(.is-root) {
    margin-left: 4px;
}
.pb-form-dataset-node:not(.is-root)::before {
    position: absolute;
    top: -5px;
    bottom: -5px;
    left: calc(var(--node-indent, 18px) + 5px);
    border-left: 1px solid #dfe5f0;
    content: "";
}
.pb-form-dataset-node:not(.is-root)::after {
    position: absolute;
    top: 50%;
    left: calc(var(--node-indent, 18px) - 7px);
    width: 12px;
    border-top: 1px solid #dfe5f0;
    content: "";
}
.pb-form-dataset-node.active {
    border-color: rgba(91, 108, 255, .25);
    background: #eef1ff;
    color: #5367ff;
}
.pb-form-dataset-node > .fas {
    color: #a0aabd;
    font-size: 9px;
}
.pb-form-dataset-node strong {
    overflow: hidden;
    font-size: 12px;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-form-dataset-node small {
    color: #8995ab;
    font-family: monospace;
    font-size: 9px;
}
.pb-form-dataset-node__add {
    display: grid;
    width: 26px;
    height: 26px;
    place-items: center;
    border: 1px solid #d7dfed;
    border-radius: 5px;
    background: #fff;
    color: #5367ff;
    font-size: 10px;
    cursor: pointer;
    transition: border-color 120ms ease, background 120ms ease;
}
.pb-form-dataset-node__add:hover,
.pb-form-dataset-node__add:focus-visible {
    border-color: #5367ff;
    background: #f1f4ff;
}
.pb-form-dataset-context {
    padding: 4px 7px;
    color: #5d6bd9;
    border: 1px solid #dce1ff;
    border-radius: 5px;
    background: #f0f2ff;
    font-size: 9px;
    font-weight: 800;
}
.pb-form-dataset-editor .pb-form-row {
    row-gap: 20px;
    column-gap: 14px;
    margin-top: 22px;
}
.pb-form-dataset-editor .pb-form-row--two {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: start;
    row-gap: 22px;
    column-gap: 16px;
}
.pb-form-dataset-editor .pb-form-row--two > .pb-form-group {
    display: block;
    min-width: 0;
    margin-bottom: 0;
}
.pb-form-dataset-editor .pb-form-row--two label {
    display: block;
    margin-bottom: 8px !important;
    line-height: 1.35;
}
.pb-form-dataset-editor .pb-form-row--two input.pb-input,
.pb-form-dataset-editor .pb-form-row--two select.pb-select {
    min-height: 36px;
    margin-top: 0;
}
.pb-form-dataset-editor .pb-form-row > .pb-btn {
    grid-column: 1 / -1;
    justify-self: start;
}
.pb-form-dataset-node-actions {
    display: flex;
    grid-column: 1 / -1;
    align-items: center;
    gap: 8px;
    margin-top: 2px;
    padding-top: 14px;
    border-top: 1px solid #edf0f6;
}
.pb-form-dataset-node-actions .pb-btn {
    min-height: 34px;
}
.pb-form-dataset-node-actions__add {
    border-color: #cfd8ff;
    color: #5367ff;
    background: #fff;
}
.pb-form-dataset-node-actions__add:hover,
.pb-form-dataset-node-actions__add:focus-visible {
    border-color: #5367ff;
    background: #f1f4ff;
}
.pb-form-dataset-node-actions__delete {
    border-color: #f2c7c7;
    color: #c44747;
    background: #fff;
}
.pb-form-dataset-node-actions__delete:hover,
.pb-form-dataset-node-actions__delete:focus-visible {
    border-color: #d65555;
    color: #a92f2f;
    background: #fff6f6;
}
.pb-form-dataset-static,
.pb-form-dataset-json {
    flex: 1 1 auto;
    min-height: min(440px, max(120px, calc(90vh - 296px)));
    overflow: auto;
    padding: 18px 28px 22px;
}
.pb-form-dataset-json__input {
    min-height: 260px;
    font-family: monospace;
    font-size: 12px;
    line-height: 1.55;
}
.pb-form-dataset-error {
    margin: 10px 0 0;
    padding: 9px 10px;
    color: #b42318;
    border: 1px solid #f1c5ca;
    border-radius: 6px;
    background: #fff1f2;
    font-size: 11px;
}
.pb-form-dataset-conditions {
    flex: 0 0 auto;
    padding: 14px 28px 18px;
    border-top: 1px solid #dfe5f0;
    background: #fbfcfe;
}
.pb-form-dataset-conditions--tab {
    flex: 1 1 auto;
    min-height: min(520px, max(120px, calc(90vh - 296px)));
    overflow: auto;
    padding: 28px;
    border-top: 0;
}
.pb-form-dataset-conditions--tab > .pb-form-dataset-heading,
.pb-form-dataset-conditions--tab > .pb-form-dataset-condition-list {
    width: min(860px, 100%);
    margin-right: auto;
    margin-left: auto;
}
.pb-form-dataset-conditions--tab > .pb-form-dataset-condition-list {
    margin-top: 14px;
    padding: 12px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fff;
}
.pb-form-dataset-conditions--tab .pb-form-dataset-condition-list > .pb-btn:not(.icon) {
    width: auto;
    justify-self: start;
    padding-right: 0;
    padding-left: 0;
    border-color: transparent;
    background: transparent;
    color: #5367ff;
}
.pb-form-dataset-condition-toggle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #42506b;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}
.pb-form-dataset-condition-toggle input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0 0 0 0);
    clip-path: inset(50%);
}
.pb-form-dataset-condition-toggle__track {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 18px;
    border-radius: 999px;
    background: #cfd6e3;
    transition: background 120ms ease;
}
.pb-form-dataset-condition-toggle__track::after {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 2px rgba(24, 37, 64, .24);
    content: "";
    transition: transform 120ms ease;
}
.pb-form-dataset-condition-toggle input:checked + .pb-form-dataset-condition-toggle__track {
    background: #5b6cff;
}
.pb-form-dataset-condition-toggle input:checked + .pb-form-dataset-condition-toggle__track::after {
    transform: translateX(14px);
}
.pb-form-dataset-condition-list {
    display: grid;
    gap: 10px;
    margin-top: 12px;
}
.pb-form-dataset-relation {
    max-width: 180px;
}
.pb-form-dataset-condition-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr)) 36px;
    gap: 8px;
    align-items: end;
}
.pb-form-dataset-condition-control {
    display: grid;
    gap: 4px;
    min-width: 0;
}
.pb-form-dataset-condition-control > span {
    color: #8a96aa;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .08em;
    line-height: 1.2;
    text-transform: uppercase;
}
.pb-form-dataset-condition-control .pb-select,
.pb-form-dataset-condition-control .pb-input {
    width: 100%;
}
.pb-form-dataset-condition-row > .pb-btn.icon {
    grid-column: 4;
    grid-row: 1;
    width: 36px;
    height: 32px;
    padding: 0;
}
.pb-form-dataset-condition-manual-value {
    grid-column: 1 / span 3;
}
.pb-form-dataset-condition-parent {
    grid-column: 1 / span 2;
}
.pb-form-dataset-condition-preview {
    grid-column: 3 / -1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 34px;
    padding: 7px 10px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fbfcfe;
}
.pb-form-dataset-condition-preview span {
    color: #8a96aa;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .08em;
    line-height: 1.2;
    text-transform: uppercase;
}
.pb-form-dataset-condition-preview strong {
    color: #243047;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.4;
}
.pb-form-dataset-condition-preview small {
    margin-top: 2px;
    color: #8a96aa;
    font-size: 10px;
    line-height: 1.3;
}
.pb-form-dataset-parent-values {
    grid-column: 1 / -1;
    display: grid;
    gap: 8px;
    padding: 10px 12px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fbfcfe;
}
.pb-form-dataset-parent-values__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
}
.pb-form-dataset-parent-values__head span {
    color: #8a96aa;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.pb-form-dataset-parent-values__head strong {
    color: #243047;
    font-size: 11px;
    font-weight: 600;
}
.pb-form-dataset-parent-values__list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.pb-form-dataset-parent-value {
    padding: 5px 8px;
    border: 1px solid #d9e0ef;
    border-radius: 6px;
    color: #344054;
    background: #fff;
    font: inherit;
    font-size: 11px;
    line-height: 1.25;
    cursor: pointer;
}
.pb-form-dataset-parent-value:hover,
.pb-form-dataset-parent-value:focus-visible {
    border-color: #5364dd;
    color: #3346cf;
    background: #f1f4ff;
}
.pb-form-dataset-parent-value.active {
    border-color: #5364dd;
    color: #fff;
    background: #5364dd;
}
.pb-form-dataset-parent-values__list span {
    padding: 5px 8px;
    border: 1px solid #d9e0ef;
    border-radius: 6px;
    color: #344054;
    background: #fff;
    font-size: 11px;
    line-height: 1.25;
}
.pb-form-dataset-parent-values__empty,
.pb-form-dataset-parent-values > small {
    color: #8a96aa;
    font-size: 10px;
    line-height: 1.45;
}
.pb-form-dataset-condition-card {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    padding: 0 14px 14px;
    border: 1px solid #dfe5f0;
    border-radius: 8px;
    background: #fff;
}
.pb-form-dataset-condition-card--selected-parent {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.pb-form-dataset-condition-card__header {
    display: flex;
    grid-column: 1 / -1;
    align-items: center;
    justify-content: space-between;
    min-height: 42px;
    margin: 0 -14px 2px;
    padding: 6px 12px;
    border-bottom: 1px solid #edf0f6;
}
.pb-form-dataset-condition-card__header strong {
    color: #243047;
    font-size: 12px;
    font-weight: 700;
}
.pb-form-dataset-condition-remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 0;
    border-radius: 6px;
    color: #fff;
    background: #dc3545;
    font-size: 12px;
}
.pb-form-dataset-condition-remove:hover,
.pb-form-dataset-condition-remove:focus-visible {
    border-color: transparent;
    color: #fff;
    background: #bb2d3b;
}
.pb-form-dataset-condition-card .pb-form-dataset-condition-manual-value {
    grid-column: 1 / -1;
}
.pb-form-dataset-selected-parent-grid {
    display: grid;
    grid-column: 1 / -1;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px 14px;
    margin-top: 2px;
}
.pb-form-dataset-selected-parent-help {
    grid-column: 1 / -1;
    margin-top: -2px;
    color: #8a96aa;
    font-size: 10px;
    line-height: 1.4;
}
.pb-form-dataset-condition-heading {
    position: relative;
}
.pb-form-dataset-condition-title {
    display: inline-flex;
    align-items: center;
    gap: 7px;
}
.pb-form-dataset-condition-title strong {
    margin-top: 0;
}
.pb-form-dataset-help {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    padding: 0;
    border: 1px solid #cfd8ff;
    border-radius: 50%;
    color: #5364dd;
    background: #f1f4ff;
    font-size: 10px;
    cursor: pointer;
}
.pb-form-dataset-help:hover,
.pb-form-dataset-help:focus-visible {
    border-color: #5364dd;
    color: #fff;
    background: #5364dd;
}
.pb-form-dataset-help-tooltip {
    position: absolute;
    z-index: 5;
    top: calc(100% + 8px);
    left: 0;
    width: min(420px, calc(100vw - 72px));
    padding: 10px 12px;
    border: 1px solid #cfd8ff;
    border-radius: 8px;
    color: #536078;
    background: #fff;
    box-shadow: 0 10px 24px rgba(36, 48, 71, .14);
    font-size: 11px;
    line-height: 1.55;
}
.pb-form-dataset-modal__footer > span {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #8a96aa;
    font-size: 11px;
}
.pb-form-dataset-modal__footer > span .fas {
    color: #6170d8;
}
.pb-form-dataset-modal__footer > div {
    display: flex;
    gap: 10px;
}
@media (max-width: 800px) {
    .pb-form-dataset-modal__context {
        grid-template-columns: 1fr;
    }
    .pb-form-dataset-modal__context > .pb-btn {
        justify-self: start;
    }
    .pb-form-dataset-conditions--tab {
        padding: 18px;
    }
    .pb-form-dataset-conditions--tab > .pb-form-dataset-heading,
    .pb-form-dataset-conditions--tab > .pb-form-dataset-condition-list {
        width: 100%;
    }
    .pb-form-dataset-modal__context,
    .pb-form-dataset-body,
    .pb-form-dataset-condition-row {
        grid-template-columns: 1fr;
        flex-direction: column;
        align-items: stretch;
    }
    .pb-form-dataset-body {
        display: block;
    }
    .pb-form-dataset-tree {
        border-right: 0;
        border-bottom: 1px solid #dfe5f0;
        padding-bottom: 18px;
    }
    .pb-form-dataset-editor {
        padding-top: 18px;
    }
    .pb-form-dataset-condition-row {
        display: grid;
    }
    .pb-form-dataset-condition-row > .pb-form-dataset-condition-manual-value,
    .pb-form-dataset-condition-row > .pb-form-dataset-condition-parent,
    .pb-form-dataset-condition-row > .pb-form-dataset-condition-preview {
        grid-column: 1;
    }
    .pb-form-dataset-condition-row > .pb-btn.icon {
        grid-column: 1;
        grid-row: auto;
    }
    .pb-form-dataset-condition-card,
    .pb-form-dataset-condition-card--selected-parent,
    .pb-form-dataset-selected-parent-grid {
        grid-template-columns: 1fr;
    }
    .pb-form-dataset-condition-card__header,
    .pb-form-dataset-selected-parent-help {
        grid-column: 1;
    }
}
.pb-pro-repeater {
    display: grid;
    gap: 8px;
    margin-bottom: 14px;
}
.pb-pro-repeater__item {
    border: 1px solid #d7dfed;
    border-radius: 5px;
    background: #fff;
    overflow: hidden;
}
.pb-pro-repeater__header {
    display: flex;
    align-items: center;
    min-height: 36px;
    gap: 4px;
    padding: 4px 6px;
    cursor: pointer;
    color: #344054;
    font-size: 12px;
    font-weight: 600;
}
.pb-pro-repeater__header:focus-visible {
    outline: 2px solid #8ea7ff;
    outline-offset: -2px;
}
.pb-pro-repeater__disclosure {
    display: grid;
    flex: 0 0 16px;
    width: 16px;
    height: 26px;
    place-items: center;
    padding: 0;
    border: 0;
    background: transparent;
    color: #8795ad;
    font-size: 9px;
}
.pb-pro-repeater__label {
    display: flex;
    flex: 1 1 auto;
    min-width: 0;
    align-items: center;
    gap: 6px;
    text-align: left;
}
.pb-pro-repeater__label > i {
    flex: 0 0 12px;
    color: #6979f8;
    font-size: 12px;
}
.pb-pro-repeater__label strong {
    min-width: 0;
    overflow: hidden;
    font-size: 11px;
    line-height: 1.2;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pb-pro-repeater__summary-actions {
    display: flex;
    flex: 0 0 auto;
    gap: 2px;
}
.pb-pro-repeater__summary-actions button {
    display: grid;
    width: 26px;
    height: 26px;
    place-items: center;
    padding: 0;
    border: 1px solid #d7dfed;
    border-radius: 5px;
    background: #fff;
    color: #526987;
    cursor: pointer;
}
.pb-pro-repeater__summary-actions button:hover:not(:disabled) {
    border-color: #aebdf7;
    color: #5367ff;
    background: #eef1ff;
}
.pb-pro-repeater__summary-actions button:disabled {
    cursor: not-allowed;
    opacity: .35;
}
.pb-pro-repeater__body {
    padding: 10px;
    border-top: 1px solid #e4e7ec;
    background: #f9fafb;
}
.pb-pro-repeater__add {
    width: 100%;
    min-height: 34px;
    padding: 7px 12px;
    border: 1px dashed #aebdf7;
    border-radius: 5px;
    background: #f4f6ff;
    color: #5367ff;
    font-size: 11px;
    font-weight: 600;
}
.pb-pro-responsive-unit .pb-control-device-btn {
    width: 28px;
    height: 28px;
}
.pb-form-row--three {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 7px;
}
.pb-state-tabs--pro {
    display: grid;
    grid-template-columns: 1fr 1fr;
    margin: 0 0 14px;
}
.pb-state-tabs--pro button {
    min-height: 31px;
    border: 1px solid #d3dae6;
    background: #fff;
    color: #667085;
    font-size: 12px;
}
.pb-state-tabs--pro button:first-child {
    border-radius: 5px 0 0 5px;
}
.pb-state-tabs--pro button:last-child {
    margin-left: -1px;
    border-radius: 0 5px 5px 0;
}
.pb-state-tabs--pro button.active {
    position: relative;
    z-index: 1;
    border-color: #6979f8;
    background: #eef1ff;
    color: #5367ff;
}
.pb-pro-action-option {
    display: flex;
    min-height: 31px;
    align-items: center;
    gap: 9px;
    padding: 6px 8px;
    border-bottom: 1px solid #eef1f5;
    color: #344054;
    font-size: 12px;
}
.pb-pro-action-option:last-child {
    border-bottom: 0;
}
.pb-pro-action-option input {
    margin: 0;
    accent-color: #6979f8;
}
.pb-widget-settings--pro .pb-pro-icon-picker {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 7px;
}
.pb-widget-settings--pro .pb-pro-icon-picker__actions {
    display: grid;
    gap: 5px;
}
.pb-widget-settings--pro .pb-pro-icon-picker__actions button {
    width: 28px;
    height: 28px;
    padding: 0;
    border: 1px solid #cdd8ea;
    border-radius: 6px;
    background: #fff;
    color: #667085;
}
.pb-widget-settings--pro .pb-pro-icon-picker__actions button:hover {
    border-color: #aebdf7;
    background: #eef1ff;
    color: #5367ff;
}
.pb-form-message-display > label {
    display: block;
    margin-bottom: 8px;
    color: #344054;
    font-size: 12px;
    font-weight: 600;
}
.pb-form-message-display__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 14px;
}
.pb-form-message-display__grid button {
    display: grid;
    min-height: 64px;
    place-items: center;
    align-content: center;
    gap: 7px;
    padding: 8px;
    border: 1px solid #d5ddea;
    border-radius: 7px;
    background: #fff;
    color: #667085;
    font-size: 11px;
}
.pb-form-message-display__grid button i {
    color: #98a2b3;
    font-size: 15px;
}
.pb-form-message-display__grid button:hover,
.pb-form-message-display__grid button.active {
    border-color: #6979f8;
    background: #f5f3ff;
    color: #5d4cf0;
    box-shadow: inset 0 0 0 1px #6979f8;
}
.pb-form-message-display__grid button.active i {
    color: #5d4cf0;
}
.pb-form-message-state-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    margin: 0 0 14px;
    padding: 3px;
    border-radius: 7px;
    background: #f2f4f7;
}
.pb-form-message-state-tabs button {
    min-height: 31px;
    border: 0;
    border-radius: 5px;
    background: transparent;
    color: #667085;
    font-size: 11px;
}
.pb-form-message-state-tabs button.active {
    background: #fff;
    color: #5367ff;
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(16, 24, 40, .1);
}
.pb-form-message-preview {
    display: inline-flex;
    width: 100%;
    min-height: 36px;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin-top: 4px;
    border: 1px solid #aebdf7;
    border-radius: 6px;
    background: #fff;
    color: #5367ff;
    font-size: 11px;
    font-weight: 600;
}
.pb-form-message-preview:hover,
.pb-form-message-preview:focus-visible {
    border-color: #6979f8;
    background: #f4f6ff;
}
</style>
