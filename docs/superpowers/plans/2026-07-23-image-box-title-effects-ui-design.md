# Image Box Title and Text Effects UI Design

## Approved behavior

- `Title HTML Tag` changes both semantic markup and the default visual scale.
- Automatic scale: H1 40px, H2 34px, H3 29px, H4 24px, H5 20px, H6 16px.
- Editing Typography Size switches the title to custom-size mode; Typography Reset restores automatic tag scale.
- Existing saved Image Box widgets keep custom sizing when their stored title size differs from the legacy 29px default.

## Approved UI

- Text Stroke and Text Shadow reuse the compact Typography trigger/popover pattern.
- Triggers are pencil icon buttons; reset is an undo icon in the popover header.
- Only one text-effect popover is open at a time.
- Inputs use the builder's compact range, number, unit, label, border, radius, and spacing language.
- Popovers overlay the following controls instead of stretching the sidebar vertically.

## Scope

Keep editor state, canvas preview, saved settings normalization, and Blade frontend rendering aligned. Do not change unrelated widgets or the frontend box model.
