param(
    [switch] $TestsOnly,
    [switch] $SourceOnly
)

$ErrorActionPreference = 'Stop'
if ($TestsOnly -and $SourceOnly) {
    throw 'Use either -TestsOnly or -SourceOnly, not both.'
}

$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..\..')).Path
$allowedPrefix = $projectRoot.TrimEnd('\') + '\'
$copyTests = -not $SourceOnly
$copySource = -not $TestsOnly

$jobs = [System.Collections.Generic.List[object]]::new()

function Add-CloneJob {
    param(
        [Parameter(Mandatory = $true)][string] $Source,
        [Parameter(Mandatory = $true)][string] $Target,
        [bool] $ReplaceVersionNumber = $true
    )

    $sourceAbsolute = [System.IO.Path]::GetFullPath((Join-Path $projectRoot $Source))
    $targetAbsolute = [System.IO.Path]::GetFullPath((Join-Path $projectRoot $Target))
    if (-not $sourceAbsolute.StartsWith($allowedPrefix, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Source escaped project root: $sourceAbsolute"
    }
    if (-not $targetAbsolute.StartsWith($allowedPrefix, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Target escaped project root: $targetAbsolute"
    }
    if (-not (Test-Path -LiteralPath $sourceAbsolute -PathType Leaf)) {
        throw "Missing clone source: $Source"
    }

    $jobs.Add([pscustomobject]@{
        Source = $sourceAbsolute
        Target = $targetAbsolute
        ReplaceVersionNumber = $ReplaceVersionNumber
    })
}

function Add-DirectoryCloneJobs {
    param(
        [Parameter(Mandatory = $true)][string] $SourceDirectory,
        [Parameter(Mandatory = $true)][string] $TargetDirectory
    )

    $sourceRoot = [System.IO.Path]::GetFullPath((Join-Path $projectRoot $SourceDirectory))
    if (-not (Test-Path -LiteralPath $sourceRoot -PathType Container)) {
        throw "Missing clone directory: $SourceDirectory"
    }

    Get-ChildItem -LiteralPath $sourceRoot -Recurse -File |
        Where-Object { $_.Name -notlike '*.bak*' } |
        ForEach-Object {
            $relative = $_.FullName.Substring($sourceRoot.Length + 1)
            $targetRelative = Convert-VersionedPath -Path $relative
            Add-CloneJob -Source (Join-Path $SourceDirectory $relative) -Target (Join-Path $TargetDirectory $targetRelative)
        }
}

function Convert-VersionedPath {
    param([Parameter(Mandatory = $true)][string] $Path)

    $converted = $Path
    $converted = $converted.Replace('PageBuilderElementorV23', 'PageBuilderElementorV24')
    $converted = $converted.Replace('pagebuilder-editor-redesign-v23', 'pagebuilder-editor-redesign-v24')
    $converted = $converted.Replace('pagebuilder-editor-v23', 'pagebuilder-editor-v24')
    $converted = $converted.Replace('pagebuilder-v23', 'pagebuilder-v24')
    return $converted
}

function Convert-VersionedContent {
    param(
        [Parameter(Mandatory = $true)][string] $Content,
        [bool] $ReplaceVersionNumber
    )

    $converted = $Content
    $converted = $converted.Replace('Page_Builder_Elementor_V23', 'Page_Builder_Elementor_V24')
    $converted = $converted.Replace('PageBuilderElementorV23', 'PageBuilderElementorV24')
    $converted = $converted.Replace('pagebuilder_elementor_v23', 'pagebuilder_elementor_v24')
    $converted = $converted.Replace('pagebuilder-elementor-v23', 'pagebuilder-elementor-v24')
    $converted = $converted.Replace('pagebuilder-editor-redesign-v23', 'pagebuilder-editor-redesign-v24')
    $converted = $converted.Replace('pagebuilder-editor-v23', 'pagebuilder-editor-v24')
    $converted = $converted.Replace('pagebuilder-v23', 'pagebuilder-v24')
    $converted = $converted.Replace('V23', 'V24')
    $converted = $converted.Replace('v23', 'v24')

    if ($ReplaceVersionNumber) {
        $converted = $converted.Replace('2\.3', '2\.4')
        $converted = $converted.Replace('2.3', '2.4')
    }

    return $converted
}

if ($copySource) {
    @(
        @('app/Http/Controllers/Web/PageBuilderElementorV23', 'app/Http/Controllers/Web/PageBuilderElementorV24'),
        @('app/Http/Requests/Page_Builder_Elementor_V23', 'app/Http/Requests/Page_Builder_Elementor_V24'),
        @('app/Models/PageBuilderElementorV23', 'app/Models/PageBuilderElementorV24'),
        @('app/Support/PageBuilderElementorV23', 'app/Support/PageBuilderElementorV24'),
        @('public/js/pagebuilder_elementor_v23', 'public/js/pagebuilder_elementor_v24'),
        @('resources/views/pagebuilder_elementor_v23', 'resources/views/pagebuilder_elementor_v24')
    ) | ForEach-Object { Add-DirectoryCloneJobs -SourceDirectory $_[0] -TargetDirectory $_[1] }

    @(
        @('app/Mail/PageBuilderElementorV23FormMail.php', 'app/Mail/PageBuilderElementorV24FormMail.php', $true),
        @('config/pagebuilder_elementor_v23_widgets.php', 'config/pagebuilder_elementor_v24_widgets.php', $true),
        @('database/migrations/2026_08_19_120000_create_pagebuilder_elementor_v23_form_datasets_table.php', 'database/migrations/2026_08_22_210800_create_pagebuilder_elementor_v24_form_datasets_table.php', $true),
        @('public/assets/css/frontend_elementor_v23.css', 'public/assets/css/frontend_elementor_v24.css', $true),
        @('public/assets/css/pagebuilder_elementor_v23.css', 'public/assets/css/pagebuilder_elementor_v24.css', $true),
        @('public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html', 'public/mockups/pagebuilder-editor-redesign-prototype-v2.4.html', $true),
        @('resources/data/pagebuilder_elementor_v23_shapes.json', 'resources/data/pagebuilder_elementor_v24_shapes.json', $false),
        @('resources/views/emails/pagebuilder-elementor-v23-form-text.blade.php', 'resources/views/emails/pagebuilder-elementor-v24-form-text.blade.php', $true)
    ) | ForEach-Object { Add-CloneJob -Source $_[0] -Target $_[1] -ReplaceVersionNumber $_[2] }
}

if ($copyTests) {
    $testsRoot = Join-Path $projectRoot 'tests'
    Get-ChildItem -LiteralPath $testsRoot -Recurse -File |
        Where-Object {
            $_.Name -notlike '*.bak*' -and
            ($_.Name -match 'PageBuilderElementorV23|pagebuilder-v23|pagebuilder-editor-v23|pagebuilder-editor-redesign-v23')
        } |
        ForEach-Object {
            $relative = $_.FullName.Substring($projectRoot.Length + 1)
            $target = Convert-VersionedPath -Path $relative
            Add-CloneJob -Source $relative -Target $target
        }
}

$duplicateTargets = $jobs | Group-Object Target | Where-Object Count -gt 1
if ($duplicateTargets) {
    throw "Duplicate clone targets: $($duplicateTargets.Name -join ', ')"
}

$existingTargets = $jobs | Where-Object { Test-Path -LiteralPath $_.Target }
if ($existingTargets) {
    throw "Clone target already exists: $($existingTargets[0].Target)"
}

$utf8 = [System.Text.UTF8Encoding]::new($false)
foreach ($job in $jobs) {
    $source = [System.IO.File]::ReadAllText($job.Source)
    $converted = Convert-VersionedContent -Content $source -ReplaceVersionNumber $job.ReplaceVersionNumber
    [System.IO.Directory]::CreateDirectory([System.IO.Path]::GetDirectoryName($job.Target)) | Out-Null
    [System.IO.File]::WriteAllText($job.Target, $converted, $utf8)
}

$mode = if ($TestsOnly) { 'tests' } elseif ($SourceOnly) { 'source' } else { 'source+tests' }
Write-Output "Cloned $($jobs.Count) $mode files from v2.3 to v2.4."
