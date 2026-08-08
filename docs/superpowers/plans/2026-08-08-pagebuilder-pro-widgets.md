# Page Builder Elementor Pro Widgets Implementation Plan

1. Add failing feature tests for the Pro registry, toolbox category, module files, control labels, shared Advanced controls, canvas markers, and frontend views.
2. Add failing runtime tests for Slides/Carousel navigation, Countdown ticking/expiry, Hotspot trigger behavior, Flip Box keyboard behavior, and Form validation.
3. Back up every tracked source file before editing.
4. Register the ten modules in `config/pagebuilder_elementor_widgets.php`, add Pro toolbox rendering and node labels/icons in `app.js`, and extend shared Advanced preview handling to Pro widget types.
5. Add a shared Pro schema/runtime plus the ten definition modules, one shared settings SFC, and one shared canvas SFC.
6. Add the shared Blade renderer and browser runtime hooks; route saved Form submissions through a rate-limited Laravel endpoint that reloads the server-owned node settings before Collect, Email, Email 2, Webhook, Message, or Redirect actions execute.
7. Run focused tests after each implementation slice, then all Page Builder feature tests.
8. Perform Chrome QA at desktop/tablet/mobile, compare local and reference screenshots, fix visible geometry or interaction defects, and never click Save.
9. Update Graphify incrementally and inspect the final diff/status without staging or cleaning unrelated changes.
