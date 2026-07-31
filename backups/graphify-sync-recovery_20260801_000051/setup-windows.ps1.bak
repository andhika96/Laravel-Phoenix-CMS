[CmdletBinding()]
param(
    [switch] $SkipGraphifyInstall,
    [switch] $SkipInitialGraph
)

$ErrorActionPreference = 'Stop'
$requiredVersion = '0.9.23'

$repoRoot = (& git rev-parse --show-toplevel 2>$null).Trim()
if (-not $repoRoot) {
    throw 'Jalankan script ini dari dalam repository Git.'
}

Set-Location -LiteralPath $repoRoot

if (-not $SkipGraphifyInstall) {
    $uv = Get-Command uv.exe -ErrorAction SilentlyContinue
    if (-not $uv) {
        $winget = Get-Command winget.exe -ErrorAction SilentlyContinue
        if (-not $winget) {
            throw 'uv dan winget tidak ditemukan. Install uv dari https://docs.astral.sh/uv/ terlebih dahulu.'
        }

        Write-Host '[graphify setup] Menginstal uv...'
        & $winget.Source install --id=astral-sh.uv -e --accept-source-agreements --accept-package-agreements
        if ($LASTEXITCODE -ne 0) {
            throw "Instalasi uv gagal dengan exit code $LASTEXITCODE"
        }

        $localBin = Join-Path $env:USERPROFILE '.local\bin'
        $env:Path = "$localBin;$env:Path"
        $uv = Get-Command uv.exe -ErrorAction Stop
    }

    Write-Host "[graphify setup] Memastikan graphifyy $requiredVersion terinstal..."
    & $uv.Source tool install "graphifyy==$requiredVersion"
    if ($LASTEXITCODE -ne 0) {
        throw "Instalasi Graphify gagal dengan exit code $LASTEXITCODE"
    }

    & $uv.Source tool update-shell | Out-Null
    $toolBin = (& $uv.Source tool dir --bin).Trim()
    if ($toolBin) {
        $env:Path = "$toolBin;$env:Path"
    }
}

$graphifyCommand = Get-Command graphify.exe -ErrorAction SilentlyContinue
if (-not $graphifyCommand) {
    $graphifyCommand = Get-Command graphify -ErrorAction SilentlyContinue
}
if (-not $graphifyCommand) {
    throw 'Graphify tidak ditemukan di PATH setelah setup.'
}

git config --local core.hooksPath .githooks
if ($LASTEXITCODE -ne 0) {
    throw 'Gagal mengatur core.hooksPath.'
}

& $graphifyCommand.Source hook install
if ($LASTEXITCODE -ne 0) {
    throw 'Gagal memasang Graphify Git hooks.'
}

$graphifyOut = Join-Path $repoRoot 'graphify-out'
New-Item -ItemType Directory -Path $graphifyOut -Force | Out-Null

$uvCommand = Get-Command uv.exe -ErrorAction SilentlyContinue
if ($uvCommand) {
    $graphifyPython = (& $uvCommand.Source tool run --from graphifyy python -c "import sys; print(sys.executable)").Trim()
}
else {
    $graphifyPython = $null
}

if ($graphifyPython) {
    [IO.File]::WriteAllText((Join-Path $graphifyOut '.graphify_python'), $graphifyPython)
}
[IO.File]::WriteAllText((Join-Path $graphifyOut '.graphify_root'), $repoRoot)

$graphPath = Join-Path $graphifyOut 'graph.json'
if (-not $SkipInitialGraph -and -not (Test-Path -LiteralPath $graphPath)) {
    Write-Host '[graphify setup] Graph belum tersedia; membuat graph code-only pertama...'
    & $graphifyCommand.Source extract $repoRoot --code-only --out $repoRoot
    if ($LASTEXITCODE -ne 0) {
        throw "Build Graphify pertama gagal dengan exit code $LASTEXITCODE"
    }
}

Write-Host ''
Write-Host '[graphify setup] Selesai.'
Write-Host "Repository : $repoRoot"
Write-Host "Hooks path : $(git config --local --get core.hooksPath)"
& $graphifyCommand.Source hook status
Write-Host 'Log update : ~/.cache/graphify-git-sync.log'