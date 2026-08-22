<template>
    <div v-if="targetLabel" class="pb-grid-column-style-controls">
        <div class="pb-grid-column-style-target">
            <div>
                <strong>{{ targetLabel }}</strong>
                <small>{{ isCellTarget ? 'Sparse override; empty values inherit the column style.' : 'Default style applied to every cell in this column.' }}</small>
            </div>
            <button v-if="isCellTarget" type="button" class="pb-link-btn pb-grid-column-style-reset" @click="editor.resetSelectedGridColumnStyle(node)">Reset to Column Style</button>
        </div>

        <div class="pb-prop-section pb-grid-column-style-group">
            <div class="pb-label-row pb-grid-column-style-section-head">
                <div class="pb-prop-section-title mb-0">Border</div>
                <div class="pb-control-device-wrap">
                    <button class="pb-control-device-btn" type="button" @click.stop="editor.openControlResponsiveMenu('grid-column-style')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
                    <div v-if="editor.isControlResponsiveMenuOpen('grid-column-style')" class="pb-control-device-menu">
                        <button v-for="device in editor.responsiveDevices" :key="'grid-column-style-' + device.value" type="button" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('grid-column-style', device.value)">
                            <i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pb-form-group">
                <label class="pb-form-label">Border Type</label>
                <select class="pb-select" :value="value('borderType')" @change="setValue('borderType', $event.target.value)">
                    <option v-for="type in borderTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                </select>
            </div>

            <template v-if="value('borderType') !== 'none'">
                <div class="pb-form-group">
                    <div class="pb-label-row"><label class="pb-form-label mb-0">Border Width</label><select class="pb-mini-unit" :value="dimensionUnit(widthSides[0].key, widthUnits[0])" @change="setDimensionUnit(widthSides, $event.target.value, widthUnits)"><option v-for="unit in widthUnits" :key="'grid-column-border-width-' + unit" :value="unit">{{ unit }}</option></select></div>
                    <div class="pb-four-sides pb-four-sides-with-link mt-1">
                        <label v-for="side in widthSides" :key="side.key" class="pb-side-input">
                            <input class="pb-input" type="number" min="0" :value="dimensionValue(side.key, widthUnits[0])" @input="setDimension(side.key, $event)">
                            <span>{{ side.label }}</span>
                        </label>
                        <div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active: linked('borderWidthLinked')}" @click="toggleLinked('borderWidthLinked')" :title="linked('borderWidthLinked') ? 'Unlink values' : 'Link values'"><i class="fas" :class="linked('borderWidthLinked') ? 'fa-link' : 'fa-unlink'"></i></button></div>
                    </div>
                </div>
                <div class="pb-form-group"><label class="pb-form-label">Border Color</label><input class="pb-input coloris pb-coloris-input" :value="value('borderColor')" @input="setValue('borderColor', $event.target.value)"></div>
            </template>

            <div class="pb-form-group">
                <div class="pb-label-row"><label class="pb-form-label mb-0">Border Radius</label><select class="pb-mini-unit" :value="dimensionUnit(radiusSides[0].key, radiusUnits[0])" @change="setDimensionUnit(radiusSides, $event.target.value, radiusUnits)"><option v-for="unit in radiusUnits" :key="'grid-column-radius-' + unit" :value="unit">{{ unit }}</option></select></div>
                <div class="pb-four-sides pb-four-sides-with-link mt-1">
                    <label v-for="corner in radiusSides" :key="corner.key" class="pb-side-input">
                        <input class="pb-input" type="number" min="0" :value="dimensionValue(corner.key, radiusUnits[0])" @input="setDimension(corner.key, $event)">
                        <span>{{ corner.label }}</span>
                    </label>
                    <div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active: linked('borderRadiusLinked')}" @click="toggleLinked('borderRadiusLinked')" :title="linked('borderRadiusLinked') ? 'Unlink values' : 'Link values'"><i class="fas" :class="linked('borderRadiusLinked') ? 'fa-link' : 'fa-unlink'"></i></button></div>
                </div>
            </div>
        </div>

        <div class="pb-prop-section pb-grid-column-style-group">
            <div class="pb-prop-section-title">Background</div>
            <div class="pb-form-group"><label class="pb-form-label">Type</label><select class="pb-select" :value="value('bgType')" @change="setValue('bgType', $event.target.value)"><option v-for="type in backgroundTypes" :key="type.value" :value="type.value">{{ type.label }}</option></select></div>

            <template v-if="value('bgType') === 'color'">
                <div class="pb-form-group"><label class="pb-form-label">Background Color</label><input class="pb-input coloris pb-coloris-input" :value="value('bgColor')" @input="setValue('bgColor', $event.target.value)"></div>
                <div class="pb-form-group"><label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round(Number(value('bgOpacity') == null ? 1 : value('bgOpacity')) * 100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" :value="value('bgOpacity')" @input="setValue('bgOpacity', Number($event.target.value))"></div>
            </template>

            <template v-if="value('bgType') === 'gradient'">
                <div class="pb-form-group"><label class="pb-form-label">Gradient Type</label><div class="pb-btn-group"><button type="button" class="pb-seg-btn" :class="{active:value('bgGradientType')==='linear'}" @click="setValue('bgGradientType', 'linear')">Linear</button><button type="button" class="pb-seg-btn" :class="{active:value('bgGradientType')==='radial'}" @click="setValue('bgGradientType', 'radial')">Radial</button></div></div>
                <div class="pb-form-group" v-if="value('bgGradientType') === 'linear'"><label class="pb-form-label">Angle <span class="pb-form-hint">{{ value('bgGradientAngle') }}°</span></label><input type="range" class="pb-range" min="0" max="360" step="1" :value="value('bgGradientAngle')" @input="setValue('bgGradientAngle', Number($event.target.value))"></div>
                <div class="pb-form-group"><label class="pb-form-label">Start Color</label><input class="pb-input coloris pb-coloris-input" :value="value('bgGradientStart')" @input="setValue('bgGradientStart', $event.target.value)"></div>
                <div class="pb-form-group"><label class="pb-form-label">End Color</label><input class="pb-input coloris pb-coloris-input" :value="value('bgGradientEnd')" @input="setValue('bgGradientEnd', $event.target.value)"></div>
                <div class="pb-form-group"><label class="pb-form-label">Position <span class="pb-form-hint">{{ value('bgGradientPosition') }}%</span></label><input type="range" class="pb-range" min="0" max="100" step="1" :value="value('bgGradientPosition')" @input="setValue('bgGradientPosition', Number($event.target.value))"></div>
            </template>

            <template v-if="value('bgType') === 'image'">
                <div class="pb-form-group">
                    <label class="pb-form-label">Image</label>
                    <div class="pb-bg-media-field" :class="{'has-image': !!value('bgImage')}">
                        <div class="pb-bg-media-preview" :style="imagePreviewStyle"><button type="button" class="pb-bg-media-center-btn" :title="value('bgImage') ? 'Change Image' : 'Choose Image'" @click="editor.chooseGridColumnBgImage(node)"><i :class="value('bgImage') ? 'fas fa-pen' : 'fas fa-plus'"></i></button></div>
                        <div class="pb-bg-media-actions"><button type="button" class="pb-bg-media-choose" @click="editor.chooseGridColumnBgImage(node)">Choose Image</button><button type="button" class="pb-bg-media-remove" :disabled="!value('bgImage')" title="Remove Image" @click="editor.clearGridColumnBgImage(node)"><i class="fas fa-trash-alt"></i></button></div>
                    </div>
                </div>
                <div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" :value="value('bgSize')" @change="setValue('bgSize', $event.target.value)"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
                <div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" :value="value('bgPosition')" @change="setValue('bgPosition', $event.target.value)"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
                <div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" :value="value('bgRepeat')" @change="setValue('bgRepeat', $event.target.value)"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
                <div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" :value="value('bgAttachment')" @change="setValue('bgAttachment', $event.target.value)"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GridColumnStyleControls',
    props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
    computed: {
        targetLabel() { return this.editor.gridColumnStyleTargetLabel(this.node); },
        isCellTarget() { return this.editor.gridColumnStyleTargetIsCell(this.node); },
        widthSides() { return [{ key: 'borderWidthTop', label: 'Top' }, { key: 'borderWidthRight', label: 'Right' }, { key: 'borderWidthBottom', label: 'Bottom' }, { key: 'borderWidthLeft', label: 'Left' }]; },
        radiusSides() { return [{ key: 'borderRadiusTL', label: 'Top Left' }, { key: 'borderRadiusTR', label: 'Top Right' }, { key: 'borderRadiusBR', label: 'Bottom Right' }, { key: 'borderRadiusBL', label: 'Bottom Left' }]; },
        widthUnits() { return ['px', 'pt', 'em', 'rem']; },
        radiusUnits() { return ['px', '%', 'em', 'rem', 'vw']; },
        borderTypes() { return [{ value: 'none', label: 'None' }, { value: 'solid', label: 'Solid' }, { value: 'double', label: 'Double' }, { value: 'dotted', label: 'Dotted' }, { value: 'dashed', label: 'Dashed' }, { value: 'groove', label: 'Groove' }]; },
        backgroundTypes() { return [{ value: 'none', label: 'None' }, { value: 'color', label: 'Color' }, { value: 'gradient', label: 'Gradient' }, { value: 'image', label: 'Image' }]; },
        imagePreviewStyle() { return this.value('bgImage') ? { backgroundImage: 'url("' + this.value('bgImage') + '")' } : {}; },
    },
    methods: {
        value(key) { return this.editor.gridColumnStyleValue(this.node, key); },
        setValue(key, value) { this.editor.setGridColumnStyleValue(this.node, key, value); },
        linked(key) { return this.value(key) !== false; },
        toggleLinked(key) { this.setValue(key, !this.linked(key)); },
        parsed(key, fallbackUnit) {
            const raw = String(this.value(key) == null ? '' : this.value(key)).trim();
            const match = raw.match(/^(-?\d+(?:\.\d+)?)([a-z%]+)?$/i);
            return { value: match ? Number(match[1]) : 0, unit: match && match[2] ? match[2] : fallbackUnit };
        },
        dimensionValue(key, fallbackUnit) { return this.parsed(key, fallbackUnit).value; },
        dimensionUnit(key, fallbackUnit) { return this.parsed(key, fallbackUnit).unit; },
        setDimension(key, event) {
            const parsed = this.parsed(key, this.dimensionUnit(key, 'px'));
            const value = Math.max(0, Number(event.target.value) || 0) + parsed.unit;
            const linkKey = key.startsWith('borderWidth') ? 'borderWidthLinked' : 'borderRadiusLinked';
            if (this.linked(linkKey)) {
                const sides = key.startsWith('borderWidth') ? this.widthSides : this.radiusSides;
                sides.forEach((side) => this.setValue(side.key, value));
                return;
            }
            this.setValue(key, value);
        },
        setDimensionUnit(sides, unit, allowedUnits) {
            const safeUnit = allowedUnits.includes(unit) ? unit : allowedUnits[0];
            sides.forEach((side) => this.setValue(side.key, this.dimensionValue(side.key, safeUnit) + safeUnit));
        },
    },
};
</script>
