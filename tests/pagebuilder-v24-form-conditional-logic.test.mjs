import test from "node:test";
import assert from "node:assert/strict";
import {
    fieldIsVisible,
    normalizeConditionalLogic,
    resolveDatasetOptions,
} from "../resources/pagebuilder_elementor_v24/modules/widgets/pro/form/logic.js";

test("conditional logic shows a field when an equals rule matches", () => {
    const field = {
        id: "province",
        conditionalLogic: {
            enabled: true,
            relation: "all",
            rules: [{ fieldId: "country", operator: "equals", value: "ID" }],
        },
    };

    assert.equal(fieldIsVisible(field, { country: "ID" }), true);
    assert.equal(fieldIsVisible(field, { country: "MY" }), false);
});

test("conditional logic supports any relation and multiple values", () => {
    const field = {
        id: "details",
        conditionalLogic: {
            enabled: true,
            relation: "any",
            rules: [
                { fieldId: "country", operator: "equals", value: "ID" },
                { fieldId: "tags", operator: "contains", value: "priority" },
            ],
        },
    };

    assert.equal(fieldIsVisible(field, { country: "MY", tags: ["priority"] }), true);
    assert.equal(fieldIsVisible(field, { country: "MY", tags: ["standard"] }), false);
});

test("conditional logic can source the expected value from a selected parent", () => {
    const field = {
        id: "details",
        conditionalLogic: {
            enabled: true,
            relation: "all",
            rules: [{
                fieldId: "country",
                operator: "equals",
                valueSource: "selectedParent",
                parentFieldId: "country",
                parentValue: "ID",
            }],
        },
    };

    assert.equal(fieldIsVisible(field, { country: "ID" }), true);
    assert.equal(fieldIsVisible(field, { country: "MY" }), false);
    assert.equal(fieldIsVisible(field, { country: "" }), false);

    assert.deepEqual(normalizeConditionalLogic(field.conditionalLogic, field.id).rules[0], {
        fieldId: "country",
        operator: "equals",
        valueSource: "selectedParent",
        parentFieldId: "country",
        parentValue: "ID",
        value: "",
    });
});

test("selected parent does not equal hides only for the selected parent value", () => {
    const field = {
        id: "province",
        conditionalLogic: {
            enabled: true,
            relation: "all",
            rules: [{
                fieldId: "country",
                operator: "not_equals",
                valueSource: "selectedParent",
                parentFieldId: "country",
                parentValue: "ID",
            }],
        },
    };

    assert.equal(fieldIsVisible(field, { country: "ID" }), false);
    assert.equal(fieldIsVisible(field, { country: "MY" }), true);
});

test("selected parent without a chosen value stays hidden for comparison operators", () => {
    const field = {
        id: "province",
        conditionalLogic: {
            enabled: true,
            relation: "all",
            rules: [{
                fieldId: "country",
                operator: "equals",
                valueSource: "selectedParent",
                parentFieldId: "country",
                parentValue: "",
            }],
        },
    };

    assert.equal(fieldIsVisible(field, { country: "ID" }), false);
    assert.equal(fieldIsVisible(field, { country: "MY" }), false);
});

test("invalid or disabled conditional logic defaults to visible", () => {
    assert.equal(fieldIsVisible({ id: "message" }, {}), true);
    assert.deepEqual(normalizeConditionalLogic({ enabled: true }, "message"), {
        enabled: false,
        relation: "all",
        rules: [],
    });
});

test("dataset options resolve root and child nodes from parent values", () => {
    const nodes = [
        { id: "id", parentId: null, label: "Indonesia", value: "ID", active: true, sortOrder: 1 },
        { id: "my", parentId: null, label: "Malaysia", value: "MY", active: true, sortOrder: 2 },
        { id: "id-jb", parentId: "id", label: "Jawa Barat", value: "ID-JB", active: true, sortOrder: 1 },
        { id: "id-jk", parentId: "id", label: "DKI Jakarta", value: "ID-JK", active: true, sortOrder: 2 },
        { id: "inactive", parentId: "id", label: "Hidden", value: "HIDDEN", active: false, sortOrder: 3 },
    ];

    assert.deepEqual(resolveDatasetOptions(nodes, null).map((node) => node.value), ["ID", "MY"]);
    assert.deepEqual(resolveDatasetOptions(nodes, "ID").map((node) => node.value), ["ID-JB", "ID-JK"]);
});
