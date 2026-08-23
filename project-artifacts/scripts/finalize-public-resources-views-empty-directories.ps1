$ErrorActionPreference = 'Stop'

$repoRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
$repoPrefix = $repoRoot + [IO.Path]::DirectorySeparatorChar
$directoryTargets = @(
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
    'public/assets/plugins/filemanager_v2.bak_20260726_120143_folder_open_and_checklist_toggle',
    'public/prototypes',
    'resources/views/auth/templates/html'
)

$allDirectories = @()
foreach ($relative in $directoryTargets) {
    $fullPath = [IO.Path]::GetFullPath((Join-Path $repoRoot $relative))
    if (-not $fullPath.StartsWith($repoPrefix, [StringComparison]::OrdinalIgnoreCase)) {
        throw "Directory target escapes repository: $fullPath"
    }

    if (-not (Test-Path -LiteralPath $fullPath)) {
        continue
    }

    $rootItem = Get-Item -LiteralPath $fullPath -Force
    if (($rootItem.Attributes -band [IO.FileAttributes]::ReparsePoint) -ne 0) {
        throw "Refusing reparse point: $relative"
    }

    if (@(Get-ChildItem -LiteralPath $fullPath -File -Recurse -Force).Count -ne 0) {
        throw "Directory still contains files: $relative"
    }

    $allDirectories += @(Get-ChildItem -LiteralPath $fullPath -Directory -Recurse -Force)
    $allDirectories += $rootItem
}

$uniqueDirectories = @(
    $allDirectories |
        Sort-Object FullName -Unique |
        Sort-Object { $_.FullName.Length } -Descending
)

foreach ($directory in $uniqueDirectories) {
    if (-not (Test-Path -LiteralPath $directory.FullName)) {
        continue
    }
    if (@(Get-ChildItem -LiteralPath $directory.FullName -Force).Count -ne 0) {
        throw "Directory is not empty: $($directory.FullName)"
    }
    Remove-Item -LiteralPath $directory.FullName -Force
}

$remaining = @($directoryTargets | Where-Object {
        Test-Path -LiteralPath (Join-Path $repoRoot $_)
    })

if ($remaining.Count -ne 0) {
    throw "Finalization left $($remaining.Count) target directories behind."
}

[pscustomobject]@{
    DirectoriesRemoved = $uniqueDirectories.Count
    RemainingTargetDirectories = 0
    Verified = $true
} | Format-List
