param(
    [Parameter(Mandatory = $true)]
    [string] $OutputPath
)

$ErrorActionPreference = 'Stop'
$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..\..')).Path
$outputAbsolute = [System.IO.Path]::GetFullPath((Join-Path $projectRoot $OutputPath))
$allowedPrefix = $projectRoot.TrimEnd('\') + '\'

if (-not $outputAbsolute.StartsWith($allowedPrefix, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw "Output path must stay inside the project root: $outputAbsolute"
}

$directoryRoots = @(
    'app/Http/Controllers/Web/PageBuilderElementorV24',
    'app/Http/Requests/Page_Builder_Elementor_V24',
    'app/Models/PageBuilderElementorV24',
    'app/Support/PageBuilderElementorV24',
    'public/js/pagebuilder_elementor_v24',
    'resources/views/pagebuilder_elementor_v24'
)

$optionalDirectoryRoots = @(
    'resources/pagebuilder_elementor_v24'
)

$singleFiles = @(
    'app/Mail/PageBuilderElementorV24FormMail.php',
    'database/migrations/2026_08_22_210800_create_pagebuilder_elementor_v24_form_datasets_table.php',
    'public/assets/css/frontend_elementor_v24.css',
    'public/assets/css/pagebuilder_elementor_v24.css',
    'public/mockups/pagebuilder-editor-redesign-prototype-v2.4.html',
    'resources/data/pagebuilder_elementor_v24_shapes.json',
    'resources/views/emails/pagebuilder-elementor-v24-form-text.blade.php',
    'routes/pagebuilder_elementor_v24.php'
)

$files = [System.Collections.Generic.List[string]]::new()
foreach ($relative in $directoryRoots) {
    $directory = Join-Path $projectRoot $relative
    if (-not (Test-Path -LiteralPath $directory -PathType Container)) {
        throw "Missing v2.4 directory: $relative"
    }
    Get-ChildItem -LiteralPath $directory -Recurse -File |
        Where-Object { $_.Name -notlike '*.bak*' } |
        ForEach-Object { $files.Add($_.FullName) }
}

foreach ($relative in $optionalDirectoryRoots) {
    $directory = Join-Path $projectRoot $relative
    if (Test-Path -LiteralPath $directory -PathType Container) {
        Get-ChildItem -LiteralPath $directory -Recurse -File |
            Where-Object { $_.Name -notlike '*.bak*' } |
            ForEach-Object { $files.Add($_.FullName) }
    }
}

foreach ($relative in $singleFiles) {
    $file = Join-Path $projectRoot $relative
    if (-not (Test-Path -LiteralPath $file -PathType Leaf)) {
        throw "Missing v2.4 file: $relative"
    }
    $files.Add($file)
}

Get-ChildItem -LiteralPath (Join-Path $projectRoot 'tests') -Recurse -File |
    Where-Object {
        $_.Name -notlike '*.bak*' -and
        ($_.Name -match 'PageBuilderElementorV24|pagebuilder-v24|pagebuilder-editor-v24|pagebuilder-editor-redesign-v24')
    } |
    ForEach-Object { $files.Add($_.FullName) }

$lines = $files |
    Sort-Object -Unique |
    ForEach-Object {
        $relative = $_.Substring($projectRoot.Length + 1).Replace('\', '/')
        $hash = (Get-FileHash -LiteralPath $_ -Algorithm SHA256).Hash.ToLowerInvariant()
        "$hash  $relative"
    }

[System.IO.Directory]::CreateDirectory([System.IO.Path]::GetDirectoryName($outputAbsolute)) | Out-Null
[System.IO.File]::WriteAllLines($outputAbsolute, $lines, [System.Text.UTF8Encoding]::new($false))
Write-Output "Snapshot: $($lines.Count) files -> $outputAbsolute"
