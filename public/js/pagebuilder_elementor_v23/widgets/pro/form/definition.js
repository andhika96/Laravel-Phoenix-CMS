(function (registry) {
    "use strict";

    if (!registry) {
        throw new Error("Page Builder Elementor v2.3 widget registry is not loaded.");
    }

    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() ||
        {};
    const deviceNames = ["desktop", "tablet", "mobile"];
    const uid = (prefix) =>
        `${prefix}-${Math.random().toString(36).slice(2, 9)}`;
    const clamp = (value, min, max) =>
        Math.min(max, Math.max(min, Number(value) || min));
    const asString = (value, fallback = "") =>
        value == null ? fallback : String(value);
    const enumValue = (value, allowed, fallback) =>
        allowed.includes(String(value || "")) ? String(value) : fallback;
    const responsiveRowSpan = (source = {}) => {
        const values = source && typeof source === "object" ? source : {};
        return deviceNames.reduce((result, device) => {
            result[device] = clamp(values[device], 1, 4);
            return result;
        }, {});
    };

    const fieldDefaults = (field = {}) => ({
        id: "field",
        label: "Field",
        type: "text",
        placeholder: "",
        defaultValue: "",
        required: false,
        width: 100,
        autocomplete: "",
        optionsText: "Option 1|option-1\nOption 2|option-2",
        multiple: false,
        inlineList: false,
        rows: 4,
        min: "",
        max: "",
        step: "",
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
        conditionalLogic: {
            enabled: false,
            relation: "all",
            rules: [],
        },
        rowSpan: { desktop: 1, tablet: 1, mobile: 1 },
        ...field,
    });

    function normalizeField(field = {}) {
        const normalized = fieldDefaults(field);
        normalized.rowSpan = responsiveRowSpan(normalized.rowSpan);
        normalized.datasetMode = normalized.datasetMode === "dataset" ? "dataset" : "static";
        normalized.datasetId = asString(normalized.datasetId);
        normalized.datasetParentFieldId = asString(normalized.datasetParentFieldId);
        const logic = normalized.conditionalLogic && typeof normalized.conditionalLogic === "object"
            ? normalized.conditionalLogic
            : {};
        const rules = Array.isArray(logic.rules)
            ? logic.rules
                  .filter((rule) => rule && typeof rule === "object")
                  .map((rule) => {
                      const fieldId = asString(rule.fieldId).trim();
                      const parentFieldId = asString(rule.parentFieldId).trim();
                      const valueSource = ["manual", "selectedParent"].includes(rule.valueSource) && parentFieldId
                          ? rule.valueSource
                          : "manual";
                      const selectedParent = valueSource === "selectedParent";
                      return {
                          fieldId: selectedParent ? parentFieldId : fieldId,
                          operator: ["equals", "not_equals", "contains", "empty", "not_empty"].includes(rule.operator)
                              ? rule.operator
                              : "equals",
                          valueSource,
                          parentFieldId: selectedParent ? parentFieldId : "",
                          parentValue: selectedParent ? asString(rule.parentValue ?? rule.value).trim() : "",
                          value: selectedParent ? "" : asString(rule.value),
                      };
                  })
                  .filter((rule) => rule.fieldId && rule.fieldId !== normalized.id)
            : [];
        normalized.conditionalLogic = {
            enabled: logic.enabled === true && rules.length > 0,
            relation: logic.relation === "any" ? "any" : "all",
            rules,
        };
        return normalized;
    }

    function columnCount(value, fallback = 1) {
        return clamp(value, 1, 4) || fallback;
    }

    function columnCounts(source = {}) {
        return deviceNames.reduce((result, device) => {
            result[device] = columnCount(source[device], 1);
            return result;
        }, {});
    }

    function fieldItem(field) {
        const normalized = normalizeField(field);
        return {
            id: `field:${normalized.id}`,
            kind: "field",
            field: normalized,
        };
    }

    function createColumn(items = [], id = uid("column")) {
        const source = Array.isArray(items) ? items : (items ? [items] : []);
        return {
            id,
            items: source.filter((item) => item?.kind === "field"),
        };
    }

    function visualRowItems(row) {
        const columns = Array.isArray(row?.columns) ? row.columns : [];
        const length = Math.max(0, ...columns.map((column) => (column.items || []).length));
        const items = [];
        for (let itemIndex = 0; itemIndex < length; itemIndex += 1) {
            columns.forEach((column) => {
                const item = column.items?.[itemIndex];
                if (item?.kind === "field") items.push(item);
            });
        }
        return items;
    }

    function ensureColumns(row) {
        row.columnCounts = columnCounts(row.columnCounts);
        row.columns = (Array.isArray(row.columns) ? row.columns : [])
            .filter((column) => column && typeof column === "object" && column.span !== "full")
            .map((column) => createColumn(
                (Array.isArray(column.items) ? column.items : []).filter((item) => item?.kind === "field"),
                asString(column.id).trim() || uid("column"),
            ));
        const desired = Math.max(...deviceNames.map((device) => columnCount(row.columnCounts[device], 1)));
        if (row.columns.length === desired) return row;

        const items = visualRowItems(row);
        const previous = row.columns;
        row.columns = Array.from({ length: desired }, (_, index) =>
            createColumn([], previous[index]?.id || uid("column")));
        items.forEach((item, index) => row.columns[index % desired].items.push(item));
        return row;
    }

    function appendItemToRow(row, item, columnId = "", index = null) {
        if (item?.kind !== "field") return null;
        ensureColumns(row);
        const requested = (row.columns || []).find((column) => String(column.id) === String(columnId));
        const column = requested || row.columns.reduce((shortest, candidate) =>
            !shortest || candidate.items.length < shortest.items.length ? candidate : shortest, null);
        if (!column) return null;
        const hasIndex = index !== null && index !== undefined && index !== "";
        const position = hasIndex && Number.isInteger(Number(index))
            ? Math.min(column.items.length, Math.max(0, Number(index)))
            : column.items.length;
        column.items.splice(position, 0, item);
        return column;
    }

    function createRow(items = [], counts = {}) {
        const row = {
            id: uid("row"),
            columnCounts: columnCounts(counts),
            columns: [],
        };
        ensureColumns(row);
        items.forEach((item) => appendItemToRow(row, item));
        return row;
    }

    function createStep(step = {}, index = 0) {
        return {
            id: asString(step.id).trim() || `step-${index + 1}`,
            title: asString(step.title),
            description: asString(step.description),
            nextButton: asString(step.nextButton, "Next") || "Next",
            previousButton: asString(step.previousButton, "Previous") || "Previous",
            iconSource: enumValue(step.iconSource, ["none", "library", "svg"], "library"),
            iconStyle: asString(step.iconStyle, "solid") || "solid",
            iconName: asString(step.iconName, "check") || "check",
            iconClass: asString(step.iconClass, "fas fa-check") || "fas fa-check",
            iconSvg: asString(step.iconSvg),
            rows: Array.isArray(step.rows) ? step.rows : [],
        };
    }

    function widthToColumns(width) {
        const value = Number(width) || 100;
        if (value >= 80) return 1;
        if (value >= 42) return 2;
        if (value >= 28) return 3;
        return 4;
    }

    function rowFieldCount(row) {
        return (row.columns || []).reduce((count, column) => count + (column.items || []).length, 0);
    }

    function trackPlan(row, device = "desktop", includeTail = false) {
        const columns = Array.isArray(row?.columns) ? row.columns : [];
        const count = columnCount(row?.columnCounts?.[device], 1);
        const placements = [];
        let totalRows = 0;

        for (let groupStart = 0; groupStart < columns.length; groupStart += count) {
            const group = columns.slice(groupStart, groupStart + count);
            const spans = group.map((column) => {
                const contentRows = (column.items || []).reduce((sum, item) =>
                    sum + (item?.kind === "field" ? responsiveRowSpan(item.field?.rowSpan)[device] : 0), 0);
                return contentRows + (includeTail ? 1 : 0);
            });
            const groupRows = Math.max(1, ...spans);
            group.forEach((_column, offset) => {
                placements[groupStart + offset] = {
                    gridColumn: offset + 1,
                    rowStart: totalRows + 1,
                    rowSpan: groupRows,
                };
            });
            totalRows += groupRows;
        }

        return {
            columnCount: count,
            totalRows: Math.max(1, totalRows),
            placements,
        };
    }

    function ensureStepRow(step) {
        if (!Array.isArray(step.rows) || !step.rows.length) {
            step.rows = [createRow()];
        }
        return step.rows[0];
    }

    function ensureLayout(layout) {
        const steps = Array.isArray(layout.steps) && layout.steps.length ? layout.steps : [createStep({}, 0)];
        layout.version = 2;
        layout.steps = steps;
        steps.forEach((step) => {
            ensureStepRow(step);
            step.rows.forEach((row) => ensureColumns(row));
        });
        return layout;
    }

    function normalizeItem(item) {
        if (item?.kind === "field") return fieldItem(item.field || {});
        if (item && item.id && item.type) return fieldItem(item);
        return null;
    }

    function normalizeTrackRow(sourceRow, stepIndex, rowIndex) {
        const row = {
            id: asString(sourceRow?.id).trim() || `row-${stepIndex + 1}-${rowIndex + 1}`,
            columnCounts: columnCounts(sourceRow?.columnCounts),
            columns: (Array.isArray(sourceRow?.columns) ? sourceRow.columns : [])
                .filter((column) => column?.span !== "full")
                .map((column, columnIndex) => createColumn(
                    (Array.isArray(column?.items) ? column.items : []).map(normalizeItem).filter(Boolean),
                    asString(column?.id).trim() || `column-${stepIndex + 1}-${rowIndex + 1}-${columnIndex + 1}`,
                )),
        };
        return ensureColumns(row);
    }

    function migrateCellRow(sourceRow, stepIndex, rowIndex) {
        const row = {
            id: asString(sourceRow?.id).trim() || `row-${stepIndex + 1}-${rowIndex + 1}`,
            columnCounts: columnCounts(sourceRow?.columnCounts),
            columns: [],
        };
        ensureColumns(row);
        let cellIndex = 0;
        (Array.isArray(sourceRow?.columns) ? sourceRow.columns : []).forEach((column) => {
            if (column?.span === "full") return;
            const target = row.columns[cellIndex % row.columns.length];
            (Array.isArray(column?.items) ? column.items : [])
                .map(normalizeItem)
                .filter(Boolean)
                .forEach((item) => target.items.push(item));
            cellIndex += 1;
        });
        return row;
    }

    function normalizeLayout(source, legacyFields = []) {
        const sourceSteps = Array.isArray(source?.steps) && source.steps.length
            ? source.steps
            : null;
        if (!sourceSteps) return fromLegacyFields(legacyFields);

        const sourceVersion = Number(source?.version) || 1;
        const layout = {
            version: 2,
            steps: sourceSteps.map((sourceStep, stepIndex) => {
                const step = createStep(sourceStep, stepIndex);
                step.rows = (Array.isArray(sourceStep.rows) ? sourceStep.rows : []).map((sourceRow, rowIndex) =>
                    sourceVersion >= 2
                        ? normalizeTrackRow(sourceRow, stepIndex, rowIndex)
                        : migrateCellRow(sourceRow, stepIndex, rowIndex));
                ensureStepRow(step);
                return step;
            }),
        };
        return ensureLayout(layout);
    }

    function fromLegacyFields(fields = []) {
        const layout = { version: 2, steps: [createStep({ id: "step-root" }, 0)] };
        let step = layout.steps[0];
        let row = null;
        let bucket = null;

        for (const rawField of Array.isArray(fields) ? fields : []) {
            if (!rawField || typeof rawField !== "object") continue;
            if (rawField.type === "step") {
                const stepData = {
                    id: asString(rawField.id).trim() || uid("step"),
                    title: rawField.stepTitle || "Step",
                    description: rawField.stepDescription || "",
                    nextButton: rawField.nextButton || "Next",
                    previousButton: rawField.previousButton || "Previous",
                    iconSource: rawField.iconSource,
                    iconStyle: rawField.iconStyle,
                    iconName: rawField.iconName,
                    iconClass: rawField.iconClass,
                    iconSvg: rawField.iconSvg,
                };
                const hasFields = (step.rows || []).some((entry) => rowFieldCount(entry) > 0);
                if (!hasFields) {
                    Object.assign(step, createStep(stepData, layout.steps.length - 1));
                } else {
                    step = createStep(stepData, layout.steps.length);
                    layout.steps.push(step);
                }
                row = null;
                bucket = null;
                continue;
            }

            const field = normalizeField(rawField);
            const nextBucket = widthToColumns(field.width);
            const capacity = nextBucket === 1 ? Number.POSITIVE_INFINITY : nextBucket;
            if (!row || bucket !== nextBucket || rowFieldCount(row) >= capacity) {
                row = createRow([], { desktop: nextBucket, tablet: nextBucket, mobile: 1 });
                step.rows.push(row);
                bucket = nextBucket;
            }
            appendItemToRow(row, fieldItem(field));
        }

        layout.steps.forEach(ensureStepRow);
        return ensureLayout(layout);
    }

    function projectFields(layout) {
        const fields = [];
        (layout?.steps || []).forEach((step, stepIndex) => {
            if (stepIndex > 0) {
                fields.push({
                    id: step.id,
                    type: "step",
                    stepTitle: step.title || "Step",
                    stepDescription: step.description || "",
                    nextButton: step.nextButton || "Next",
                    previousButton: step.previousButton || "Previous",
                    iconSource: step.iconSource || "library",
                    iconStyle: step.iconStyle || "solid",
                    iconName: step.iconName || "check",
                    iconClass: step.iconClass || "fas fa-check",
                    iconSvg: step.iconSvg || "",
                });
            }
            (step.rows || []).forEach((row) => visualRowItems(row).forEach((item) => fields.push(item.field)));
        });
        return fields;
    }

    function appendFieldToLayout(layout, stepId, item) {
        const steps = Array.isArray(layout?.steps) ? layout.steps : [];
        const step = steps.find((entry) => String(entry.id) === String(stepId)) || steps[steps.length - 1];
        if (!step) return null;
        ensureStepRow(step);
        const row = step.rows[step.rows.length - 1];
        appendItemToRow(row, item);
        return row;
    }

    function appendFieldToRow(layout, stepId, rowId, item) {
        const step = (layout?.steps || []).find((entry) => String(entry.id) === String(stepId));
        const row = step?.rows?.find((entry) => String(entry.id) === String(rowId));
        if (!row) return null;
        appendItemToRow(row, item);
        return row;
    }

    function findColumn(layout, location) {
        const step = (layout?.steps || []).find((entry) => String(entry.id) === String(location?.stepId));
        const row = step?.rows?.find((entry) => String(entry.id) === String(location?.rowId));
        const column = row?.columns?.find((entry) => String(entry.id) === String(location?.columnId));
        return { step, row, column };
    }

    function canAcceptDrop(sourceMeta = {}, targetMeta = {}) {
        const sourceOwner = asString(sourceMeta.ownerId).trim();
        const targetOwner = asString(targetMeta.ownerId).trim();
        const expectedGroup = sourceOwner ? `pb-form-grid:${sourceOwner}` : "";
        if (!sourceOwner || sourceOwner !== targetOwner) return false;
        if (sourceMeta.group !== expectedGroup || targetMeta.group !== expectedGroup) return false;
        return sourceMeta.kind !== "submit";
    }

    function canAcceptSortableGroup(to, from, ownerId, groupName) {
        const owner = asString(ownerId);
        const group = asString(groupName);
        return Boolean(
            owner
            && group
            && to?.el?.dataset?.formLayoutOwner === owner
            && from?.el?.dataset?.formLayoutOwner === owner
            && to?.options?.group?.name === group
            && from?.options?.group?.name === group
        );
    }

    function moveItem(layout, sourceMeta, targetMeta, options = {}) {
        if (!canAcceptDrop(sourceMeta, targetMeta)) return { ok: false, reason: "outside" };
        if (sourceMeta.stepId !== targetMeta.stepId && options.confirmed !== true) {
            return { ok: false, reason: "cross-step" };
        }
        const source = findColumn(layout, sourceMeta);
        const target = findColumn(layout, targetMeta);
        if (!source.column || !target.column) return { ok: true };
        const requestedId = asString(sourceMeta.itemId);
        const fallbackIndex = Math.max(0, Number(sourceMeta.index) || 0);
        const sourceIndex = requestedId
            ? source.column.items.findIndex((item) => String(item.id) === requestedId)
            : Math.min(fallbackIndex, source.column.items.length - 1);
        if (sourceIndex < 0) return { ok: true };
        const [sourceItem] = source.column.items.splice(sourceIndex, 1);
        const targetIndex = Number.isInteger(Number(targetMeta.index))
            ? Math.min(target.column.items.length, Math.max(0, Number(targetMeta.index)))
            : target.column.items.length;
        target.column.items.splice(targetIndex, 0, sourceItem);
        return { ok: true };
    }

    function removeItem(layout, itemId) {
        let removed = false;
        (layout?.steps || []).forEach((step) => {
            (step.rows || []).forEach((row) => {
                (row.columns || []).forEach((column) => {
                    const before = column.items || [];
                    const after = before.filter((item) => {
                        const matches = item?.kind === "field" && String(item.id) === String(itemId);
                        removed = removed || matches;
                        return !matches;
                    });
                    column.items = after;
                });
            });
        });
        return removed;
    }

    function deleteRow(layout, stepId, rowId) {
        const step = (layout?.steps || []).find((entry) => String(entry.id) === String(stepId));
        if (!step || !Array.isArray(step.rows) || step.rows.length <= 1) return false;
        const index = step.rows.findIndex((row) => String(row.id) === String(rowId));
        if (index < 0) return false;
        const targetIndex = index > 0 ? index - 1 : index + 1;
        const target = step.rows[targetIndex];
        const removed = step.rows.splice(index, 1)[0];
        ensureColumns(target);
        ensureColumns(removed);
        const moved = Array.from({ length: target.columns.length }, () => []);
        removed.columns.forEach((column, columnIndex) => {
            moved[Math.min(columnIndex, target.columns.length - 1)].push(...column.items);
        });
        target.columns.forEach((column, columnIndex) => {
            column.items = index < targetIndex
                ? [...moved[columnIndex], ...column.items]
                : [...column.items, ...moved[columnIndex]];
        });
        return true;
    }

    function appendStep(layout, stepData = {}) {
        ensureLayout(layout);
        const step = createStep({
            ...stepData,
            id: asString(stepData.id).trim() || uid("step"),
        }, layout.steps.length);
        ensureStepRow(step);
        layout.steps.push(step);
        return step;
    }

    function deleteStep(layout, stepId) {
        ensureLayout(layout);
        if (layout.steps.length <= 1) return false;
        const index = layout.steps.findIndex((step) => String(step.id) === String(stepId));
        if (index < 0) return false;
        const target = layout.steps[index > 0 ? index - 1 : index + 1];
        const removed = layout.steps[index];
        const rowsWithFields = (removed.rows || []).filter((row) => rowFieldCount(row) > 0);
        if (rowsWithFields.length) target.rows.push(...rowsWithFields);
        layout.steps.splice(index, 1);
        ensureStepRow(target);
        return true;
    }

    const formRowGrid = {
        version: 2,
        normalizeLayout,
        normalizeSettings(settings = {}) {
            const normalized = { ...settings };
            normalized.stepType = enumValue(
                settings.stepType,
                ["none", "text", "icon", "number", "progress", "number-text", "icon-text"],
                "none",
            );
            normalized.stepShape = enumValue(
                settings.stepShape,
                ["circle", "square", "rounded", "none"],
                "circle",
            );
            normalized.messageDisplay = enumValue(
                settings.messageDisplay,
                ["basic", "above-form", "toast", "modal"],
                "basic",
            );
            normalized.rowGrid = normalizeLayout(settings.rowGrid, settings.fields);
            normalized.fields = projectFields(normalized.rowGrid);
            return normalized;
        },
        fromLegacyFields,
        projectFields,
        normalizedSteps: (layout) => layout?.steps || [],
        createStep,
        createRow,
        createColumn,
        createField: normalizeField,
        fieldItem,
        appendItemToRow,
        visualRowItems,
        trackPlan,
        ensureColumns,
        appendFieldToLayout,
        appendFieldToRow,
        removeItem,
        moveItem,
        deleteRow,
        appendStep,
        deleteStep,
        canAcceptDrop,
        canAcceptSortableGroup,
    };
    window.PageBuilderElementorV23FormRowGrid = formRowGrid;

    const defaults = () => ({
        ...advanced(),
        formName: "New Form",
        fields: [
            fieldDefaults({ id: "name", label: "Name" }),
            fieldDefaults({ id: "email", label: "Email", type: "email", required: true }),
            fieldDefaults({ id: "message", label: "Message", type: "textarea" }),
        ],
        inputSize: "small",
        showLabels: true,
        markRequired: false,
        buttonSize: "small",
        buttonWidth: "100",
        buttonText: "Send",
        buttonIconSource: "none",
        buttonIconStyle: "solid",
        buttonIconName: "",
        buttonIconClass: "",
        buttonIconSvg: "",
        buttonIconPosition: "before",
        buttonIconSpacing: "5px",
        buttonId: "",
        submitActions: ["message"],
        emailTo: "",
        emailSubject: 'New message from "New Form"',
        emailContent: "[all-fields]",
        emailFrom: "",
        emailFromName: "",
        emailReplyTo: "email",
        emailCc: "",
        emailBcc: "",
        emailContentType: "html",
        email2To: "",
        email2Subject: "New form submission",
        email2Content: "[all-fields]",
        email2From: "",
        email2FromName: "",
        email2ReplyTo: "",
        email2Cc: "",
        email2Bcc: "",
        email2ContentType: "html",
        redirectUrl: "",
        webhookUrl: "",
        stepType: "none",
        stepShape: "circle",
        formId: "new_form",
        validation: "browser",
        customMessages: false,
        messageDisplay: "basic",
        successTitle: "Message sent",
        successMessage: "The form was sent successfully.",
        errorTitle: "Submission failed",
        errorMessage: "An error occurred.",
        messageShowIcon: true,
        messageDismissible: true,
        columnGap: "10px",
        rowGap: "10px",
        labelColor: "#344054",
        htmlColor: "#344054",
        fieldTextColor: "#344054",
        fieldBackground: "#ffffff",
        fieldBorderColor: "#d0d5dd",
        fieldBorderWidth: "1px",
        fieldRadius: "4px",
        fieldFocusBorderColor: "#6979f8",
        fieldFocusBackground: "#ffffff",
        buttonAlign: "left",
        buttonBackground: "#6979f8",
        buttonBackgroundHover: "#5868e8",
        buttonTextColor: "#ffffff",
        buttonTextColorHover: "#ffffff",
        buttonRadius: "4px",
        successColor: "#067647",
        errorColor: "#b42318",
        stepActiveColor: "#6979f8",
        stepInactiveColor: "#d0d5dd",
    });

    registry.register({
        type: "form",
        label: "Form",
        category: "pro",
        icon: "fab fa-wpforms",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const settings = {
                ...defaults(),
                ...(node.settings || {}),
            };
            const iconClass = String(settings.buttonIconClass || "").trim();
            if (settings.buttonIconSource === "svg" && settings.buttonIconSvg) {
                settings.buttonIconClass = "";
            } else if (/^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(iconClass)) {
                const [prefix, name] = iconClass.split(/\s+/);
                settings.buttonIconSource = "library";
                settings.buttonIconStyle =
                    { fas: "solid", far: "regular", fab: "brands" }[prefix] ||
                    settings.buttonIconStyle ||
                    "solid";
                settings.buttonIconName = name.replace(/^fa-/, "");
                settings.buttonIconSvg = "";
            } else {
                settings.buttonIconSource = "none";
                settings.buttonIconName = "";
                settings.buttonIconClass = "";
                settings.buttonIconSvg = "";
            }
            const allowedActions = ["message", "email", "email2", "redirect", "webhook", "collect"];
            settings.submitActions = Array.isArray(settings.submitActions)
                ? settings.submitActions.filter((action) => allowedActions.includes(action))
                : ["message"];
            node.settings = formRowGrid.normalizeSettings(settings);
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
