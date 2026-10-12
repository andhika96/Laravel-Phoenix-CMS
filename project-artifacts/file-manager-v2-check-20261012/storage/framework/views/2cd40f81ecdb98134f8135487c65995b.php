<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7fb">
    <title>Arunika Files</title>
    <?php
        $assetRoot = public_path('assets/plugins/filemanager_v2');
        $assetVersion = is_file($assetRoot . '/filemanager-v2.js') ? filemtime($assetRoot . '/filemanager-v2.js') : 'dev';
    ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/filemanager_v2/filemanager-v2.css')); ?>?v=<?php echo e($assetVersion); ?>">
</head>
<body>
    <div id="filemanager-v2"></div>
    <script>
        window.FILEMANAGER_V2_CONFIG = {
            apiBase: <?php echo json_encode(url('/api/v2/file-manager'), 15, 512) ?>,
            csrfToken: <?php echo json_encode(csrf_token(), 15, 512) ?>,
        };
    </script>
    <script type="module" src="<?php echo e(asset('assets/plugins/filemanager_v2/filemanager-v2.js')); ?>?v=<?php echo e($assetVersion); ?>"></script>
</body>
</html>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/filemanager_v2/index.blade.php ENDPATH**/ ?>