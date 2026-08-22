<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$configPath = $projectRoot.'/config/pagebuilder_elementor_v24_widgets.php';
$modules = require $configPath;
$requestedTypes = array_slice($argv, 1);

if ($requestedTypes === []) {
    fwrite(STDERR, "Pass at least one module type.\n");
    exit(1);
}

$orders = [];
$categoryIndexes = [];
foreach ($modules as $type => $module) {
    $category = (string) ($module['category'] ?? 'basic');
    $categoryIndexes[$category] = ($categoryIndexes[$category] ?? 0) + 1;
    $orders[$type] = $categoryIndexes[$category] * 10;
}

foreach ($requestedTypes as $type) {
    if (! isset($modules[$type])) {
        throw new RuntimeException('Unknown legacy module type: '.$type);
    }

    $module = $modules[$type];
    $category = (string) $module['category'];
    $definitionSource = $projectRoot.'/public/'.ltrim((string) $module['definition'], '/');
    $canvasSource = $projectRoot.'/public/'.ltrim((string) $module['canvas'], '/');
    $settingsSource = $projectRoot.'/public/'.ltrim((string) $module['settings'], '/');
    $viewSource = $projectRoot.'/resources/views/'.str_replace('.', '/', (string) $module['view']).'.blade.php';
    $slug = basename(dirname(str_replace('\\', '/', (string) $module['definition'])));
    $categoryPath = $category === 'layout' ? 'layout' : 'widgets/'.$category;
    $destination = $projectRoot.'/resources/pagebuilder_elementor_v24/modules/'.$categoryPath.'/'.$slug;

    foreach ([$definitionSource, $canvasSource, $settingsSource, $viewSource] as $source) {
        if (! is_file($source)) {
            throw new RuntimeException('Missing active source for '.$type.': '.$source);
        }
    }

    if (file_exists($destination.'/module.json')) {
        throw new RuntimeException('Canonical module already exists: '.$type);
    }

    if (! is_dir($destination) && ! mkdir($destination, 0777, true) && ! is_dir($destination)) {
        throw new RuntimeException('Cannot create module directory: '.$destination);
    }

    $copies = [
        $definitionSource => $destination.'/definition.js',
        $canvasSource => $destination.'/Canvas.vue',
        $settingsSource => $destination.'/Settings.vue',
        $viewSource => $destination.'/frontend.blade.php',
    ];

    foreach ($copies as $source => $target) {
        if (! copy($source, $target)) {
            throw new RuntimeException('Cannot copy '.$source.' to '.$target);
        }
    }

    $manifest = [
        'schemaVersion' => 1,
        'type' => (string) $module['type'],
        'label' => (string) $module['label'],
        'category' => $category,
        'icon' => (string) $module['icon'],
        'order' => $orders[$type],
        'toolbox' => ($module['toolbox'] ?? true) !== false,
        'assets' => [
            'definition' => 'definition.js',
            'canvas' => 'Canvas.vue',
            'settings' => 'Settings.vue',
            'view' => 'frontend.blade.php',
        ],
        'advanced' => [
            'profile' => $category === 'layout' ? 'layout' : 'widget',
            'capabilities' => [],
        ],
    ];

    $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
    if (file_put_contents($destination.'/module.json', $json) === false) {
        throw new RuntimeException('Cannot write manifest: '.$type);
    }

    fwrite(STDOUT, $type.' -> '.str_replace('\\', '/', substr($destination, strlen($projectRoot) + 1))."\n");
}
