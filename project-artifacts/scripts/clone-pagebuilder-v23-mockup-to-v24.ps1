$ErrorActionPreference = 'Stop'
$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..\..')).Path
$source = [System.IO.Path]::GetFullPath((Join-Path $projectRoot 'project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype'))
$target = [System.IO.Path]::GetFullPath((Join-Path $projectRoot 'project-artifacts/mockups/pagebuilder-v24-responsive-hero-prototype'))
$allowedPrefix = $projectRoot.TrimEnd('\') + '\'

if (-not $source.StartsWith($allowedPrefix, [System.StringComparison]::OrdinalIgnoreCase) -or
    -not $target.StartsWith($allowedPrefix, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw 'Prototype clone path escaped the project root.'
}
if (-not (Test-Path -LiteralPath $source -PathType Container)) {
    throw "Missing prototype source: $source"
}
if (Test-Path -LiteralPath $target) {
    throw "Prototype target already exists: $target"
}

Copy-Item -LiteralPath $source -Destination $target -Recurse

$textExtensions = @('.css', '.html', '.js', '.jsx', '.json', '.md', '.mjs', '.txt')
$textNames = @('.gitignore', '.npmrc')
$utf8 = [System.Text.UTF8Encoding]::new($false)

Get-ChildItem -LiteralPath $target -Recurse -File |
    Where-Object { $textExtensions -contains $_.Extension.ToLowerInvariant() -or $textNames -contains $_.Name } |
    ForEach-Object {
        $content = [System.IO.File]::ReadAllText($_.FullName)
        $content = $content.Replace('V23', 'V24').Replace('v23', 'v24')
        if ($_.Name -ne 'package-lock.json') {
            $content = $content.Replace('2.3', '2.4')
        }
        [System.IO.File]::WriteAllText($_.FullName, $content, $utf8)
    }

Write-Output "Cloned responsive Hero prototype to $target"
