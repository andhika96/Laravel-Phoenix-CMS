<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7fb">
    <title>Arunika Files</title>
    @php
        $assetRoot = public_path('assets/plugins/filemanager_v2');
        $assetVersion = is_file($assetRoot . '/filemanager-v2.js') ? filemtime($assetRoot . '/filemanager-v2.js') : 'dev';
    @endphp
    <link rel="stylesheet" href="{{ asset('assets/plugins/filemanager_v2/filemanager-v2.css') }}?v={{ $assetVersion }}">
</head>
<body>
    <div id="filemanager-v2"></div>
    <script>
        window.FILEMANAGER_V2_CONFIG = {
            apiBase: @json(url('/api/v2/file-manager')),
            csrfToken: @json(csrf_token()),
        };
    </script>
    <script type="module" src="{{ asset('assets/plugins/filemanager_v2/filemanager-v2.js') }}?v={{ $assetVersion }}"></script>
</body>
</html>
