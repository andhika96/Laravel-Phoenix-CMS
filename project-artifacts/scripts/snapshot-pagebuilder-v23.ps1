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
    'app/Http/Controllers/Web/PageBuilderElementorV23',
    'app/Http/Requests/Page_Builder_Elementor_V23',
    'app/Models/PageBuilderElementorV23',
    'app/Support/PageBuilderElementorV23',
    'public/js/pagebuilder_elementor_v23',
    'resources/views/pagebuilder_elementor_v23'
)

$singleFiles = @(
    'app/Mail/PageBuilderElementorV23FormMail.php',
    'config/pagebuilder_elementor_v23_widgets.php',
    'database/migrations/2026_08_19_120000_create_pagebuilder_elementor_v23_form_datasets_table.php',
    'public/assets/css/frontend_elementor_v23.css',
    'public/assets/css/pagebuilder_elementor_v23.css',
    'public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html',
    'resources/data/pagebuilder_elementor_v23_shapes.json',
    'resources/views/emails/pagebuilder-elementor-v23-form-text.blade.php'
)

$files = [System.Collections.Generic.List[string]]::new()
foreach ($relative in $directoryRoots) {
    $directory = Join-Path $projectRoot $relative
    if (-not (Test-Path -LiteralPath $directory -PathType Container)) {
        throw "Missing v2.3 directory: $relative"
    }
    Get-ChildItem -LiteralPath $directory -Recurse -File |
        Where-Object { $_.Name -notlike '*.bak*' } |
        ForEach-Object { $files.Add($_.FullName) }
}

foreach ($relative in $singleFiles) {
    $file = Join-Path $projectRoot $relative
    if (-not (Test-Path -LiteralPath $file -PathType Leaf)) {
        throw "Missing v2.3 file: $relative"
    }
    $files.Add($file)
}

Get-ChildItem -LiteralPath (Join-Path $projectRoot 'tests') -Recurse -File |
    Where-Object {
        $_.Name -notlike '*.bak*' -and
        ($_.Name -match 'PageBuilderElementorV23|pagebuilder-v23|pagebuilder-editor-v23|pagebuilder-editor-redesign-v23')
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
