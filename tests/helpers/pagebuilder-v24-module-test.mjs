export function configureModules(context, rawManifests) {
  const assetExtensions = { definition: '.js', canvas: '.vue', settings: '.vue', runtime: '.js', styles: '.css' };
  const catalog = {};
  for (const rawManifest of rawManifests) {
    const manifest = typeof rawManifest === 'string' ? JSON.parse(rawManifest) : rawManifest;
    const assets = Object.fromEntries(
      Object.keys(manifest.assets)
        .filter((key) => key !== 'view')
        .map((key) => [key, `/pagebuilder-elementor/v2.4/module-assets/${manifest.type}/${key}${assetExtensions[key]}`]),
    );
    catalog[manifest.type] = {
      ...manifest,
      assets,
      capabilities: manifest.capabilities || [],
    };
  }
  context.window.PageBuilderElementorV24Widgets.configure(catalog);
  return catalog;
}

export function configureSingleModule(context, rawManifest) {
  const catalog = configureModules(context, [rawManifest]);
  return Object.values(catalog)[0];
}
