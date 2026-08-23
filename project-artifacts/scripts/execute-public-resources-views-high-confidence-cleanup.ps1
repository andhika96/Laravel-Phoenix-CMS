param(
    [switch] $Execute
)

$ErrorActionPreference = 'Stop'

$repoRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
$repoPrefix = $repoRoot + [IO.Path]::DirectorySeparatorChar
$manifestPath = Join-Path $repoRoot 'project-artifacts\backups\manifests\public-resources-views-high-confidence-20260823_202836.csv'

$deleteTargets = @(
    'public/phpinfo.php',
    'public/change.php',
    'public/assets/plugins/bootstrap/5.3.3',
    'public/assets/plugins/bootstrap/5.3.6_source',
    'public/assets/plugins/bootstrap/5.3.8',
    'public/assets/plugins/bootstrap/5.3.8_custom',
    'public/assets/plugins/bootstrap/5.3.8_source',
    'public/assets/plugins/echarts/5.6.0',
    'public/assets/plugins/fontawesome/5.15.4',
    'public/assets/plugins/datatables',
    'public/assets/plugins/uikit-compatible-w-bootstrap',
    'public/assets/pagebuilder_elementor/renditions',
    'resources/views/filemanager/filemanager.blade.zip',
    'public/assets/plugins/filemanager_v2.bak_20260726_031716_root_listing_and_breadcrumb',
    'public/assets/plugins/filemanager_v2.bak_20260726_032641_persistent_star_indicator',
    'public/assets/plugins/filemanager_v2.bak_20260726_033217_high_contrast_star_badge',
    'public/assets/plugins/filemanager_v2.bak_20260726_051722_unified_action_modal',
    'public/assets/plugins/filemanager_v2.bak_20260726_052045_unified_action_modal_final',
    'public/assets/plugins/filemanager_v2.bak_20260726_101431_folder_tree_and_modal_footer',
    'public/assets/plugins/filemanager_v2.bak_20260726_102300_reduced_motion_scope',
    'public/assets/plugins/filemanager_v2.bak_20260726_111329_folder_details_tree_rename',
    'public/assets/plugins/filemanager_v2.bak_20260726_113343_selection_tree_regressions',
    'public/assets/plugins/filemanager_v2.bak_20260726_115034_checklist_only_selection',
    'public/assets/plugins/filemanager_v2.bak_20260726_120143_folder_open_and_checklist_toggle'
)

if (-not (Test-Path -LiteralPath $manifestPath -PathType Leaf)) {
    throw "Backup manifest is missing: $manifestPath"
}

$manifest = @(Import-Csv -LiteralPath $manifestPath)
if ($manifest.Count -ne 4898 -or @($manifest | Where-Object { $_.HashMatch -ne 'True' }).Count -ne 0) {
    throw 'Backup manifest is incomplete or contains a hash mismatch.'
}

$manifestByPath = @{}
foreach ($row in $manifest) {
    $manifestByPath[$row.OriginalPath] = $row
}

$resolvedTargets = @()
$targetDirectories = @()
foreach ($target in $deleteTargets) {
    $fullPath = [IO.Path]::GetFullPath((Join-Path $repoRoot $target))
    if (-not $fullPath.StartsWith($repoPrefix, [StringComparison]::OrdinalIgnoreCase)) {
        throw "Target escapes repository: $fullPath"
    }

    $relative = $fullPath.Substring($repoRoot.Length + 1).Replace('\', '/')
    if (-not ($relative.StartsWith('public/', [StringComparison]::OrdinalIgnoreCase) -or
            $relative.StartsWith('resources/views/', [StringComparison]::OrdinalIgnoreCase))) {
        throw "Target is outside approved roots: $relative"
    }

    if (-not (Test-Path -LiteralPath $fullPath)) {
        throw "Target is missing: $relative"
    }

    $item = Get-Item -LiteralPath $fullPath -Force
    if (($item.Attributes -band [IO.FileAttributes]::ReparsePoint) -ne 0) {
        throw "Refusing to process reparse point: $relative"
    }

    if ($item.PSIsContainer) {
        $targetDirectories += $fullPath
    }
    $resolvedTargets += $fullPath
}

$trackedFiles = @(git -c core.quotePath=false ls-files -- $deleteTargets | Sort-Object -Unique)
if ($trackedFiles.Count -ne 4883) {
    throw "Unexpected tracked file count: $($trackedFiles.Count); expected 4883."
}

$diskFiles = @()
foreach ($fullPath in $resolvedTargets) {
    $item = Get-Item -LiteralPath $fullPath -Force
    $diskFiles += if ($item.PSIsContainer) {
        @(Get-ChildItem -LiteralPath $fullPath -File -Recurse -Force)
    } else {
        @($item)
    }
}

$diskRelative = @($diskFiles | ForEach-Object {
        $_.FullName.Substring($repoRoot.Length + 1).Replace('\', '/')
    } | Sort-Object -Unique)

if ($diskRelative.Count -ne $trackedFiles.Count -or @(Compare-Object $trackedFiles $diskRelative).Count -ne 0) {
    throw 'Disk contents differ from the exact tracked cleanup set.'
}

$verifiedBytes = 0L
foreach ($relative in $trackedFiles) {
    $normalized = $relative.Replace('\', '/')
    $row = $manifestByPath[$normalized]
    if ($null -eq $row) {
        throw "No backup manifest row for: $normalized"
    }

    $fullPath = Join-Path $repoRoot $relative
    $currentHash = (Get-FileHash -LiteralPath $fullPath -Algorithm SHA256).Hash.ToLowerInvariant()
    if ($currentHash -ne $row.SHA256) {
        throw "Current file differs from verified backup: $normalized"
    }
    $verifiedBytes += [int64] $row.Bytes
}

if (-not $Execute) {
    [pscustomobject]@{
        Mode = 'DryRun'
        Targets = $deleteTargets.Count
        Files = $trackedFiles.Count
        MiB = [math]::Round($verifiedBytes / 1MB, 2)
        BackupVerified = $true
    } | Format-List
    exit 0
}

foreach ($relative in $trackedFiles) {
    Remove-Item -LiteralPath (Join-Path $repoRoot $relative) -Force
}

$allDirectories = @()
foreach ($fullPath in $targetDirectories) {
    $allDirectories += @(Get-ChildItem -LiteralPath $fullPath -Directory -Recurse -Force)
    $allDirectories += Get-Item -LiteralPath $fullPath -Force
}

$allDirectories |
    Sort-Object FullName -Unique |
    Sort-Object { $_.FullName.Length } -Descending |
    ForEach-Object {
        if (Test-Path -LiteralPath $_.FullName) {
            if (@(Get-ChildItem -LiteralPath $_.FullName -Force).Count -ne 0) {
                throw "Directory is not empty after exact file deletion: $($_.FullName)"
            }
            Remove-Item -LiteralPath $_.FullName -Force
        }
    }

foreach ($relative in @('public/prototypes', 'resources/views/auth/templates/html')) {
    $fullPath = Join-Path $repoRoot $relative
    if (Test-Path -LiteralPath $fullPath) {
        if (@(Get-ChildItem -LiteralPath $fullPath -Force).Count -ne 0) {
            throw "Relocation source directory is not empty: $relative"
        }
        Remove-Item -LiteralPath $fullPath -Force
    }
}

$remainingTargets = @($deleteTargets | Where-Object { Test-Path -LiteralPath (Join-Path $repoRoot $_) })
if ($remainingTargets.Count -ne 0) {
    throw "Cleanup left $($remainingTargets.Count) target(s) behind."
}

[pscustomobject]@{
    Mode = 'Execute'
    DeletedTargets = $deleteTargets.Count
    DeletedFiles = $trackedFiles.Count
    DeletedMiB = [math]::Round($verifiedBytes / 1MB, 2)
    RemainingTargets = 0
    Verified = $true
} | Format-List
