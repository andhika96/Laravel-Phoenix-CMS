[CmdletBinding()]
param(
    [ValidateSet('post-merge', 'post-rewrite', 'pre-push', 'manual-test')]
    [string] $Event = 'manual-test'
)

$ErrorActionPreference = 'Stop'

if ($env:GRAPHIFY_SKIP_HOOK -eq '1') {
    exit 0
}

try {
    $repoRoot = (& git rev-parse --show-toplevel 2>$null).Trim()
    if (-not $repoRoot) {
        exit 0
    }

    Set-Location -LiteralPath $repoRoot

    $graphPath = Join-Path $repoRoot 'graphify-out\graph.json'
    if (-not (Test-Path -LiteralPath $graphPath)) {
        Write-Warning "[graphify sync:$Event] graph.json belum tersedia; jalankan scripts\graphify\setup-windows.ps1 terlebih dahulu."
        exit 0
    }

    $logDir = Join-Path $env:USERPROFILE '.cache'
    New-Item -ItemType Directory -Path $logDir -Force | Out-Null
    $logPath = Join-Path $logDir 'graphify-git-sync.log'
    $timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    Add-Content -LiteralPath $logPath -Value "[$timestamp][$Event] incremental update started"

    $python = $null
    $pythonFile = Join-Path $repoRoot 'graphify-out\.graphify_python'
    if (Test-Path -LiteralPath $pythonFile) {
        $candidate = [IO.File]::ReadAllText($pythonFile).Trim()
        if ($candidate -and (Test-Path -LiteralPath $candidate)) {
            & $candidate -c "import graphify" 2>$null
            if ($LASTEXITCODE -eq 0) {
                $python = $candidate
            }
        }
    }

    $previousErrorActionPreference = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        if ($python) {
            $output = & $python -m graphify update $repoRoot 2>&1
        }
        else {
            $graphifyCommand = Get-Command graphify.exe -ErrorAction SilentlyContinue
            if (-not $graphifyCommand) {
                $graphifyCommand = Get-Command graphify -ErrorAction SilentlyContinue
            }
            if (-not $graphifyCommand) {
                throw 'Graphify tidak ditemukan. Jalankan scripts\graphify\setup-windows.ps1.'
            }
            $output = & $graphifyCommand.Source update $repoRoot 2>&1
        }
        $exitCode = $LASTEXITCODE
    }
    finally {
        $ErrorActionPreference = $previousErrorActionPreference
    }
    if ($output) {
        $output | ForEach-Object { Add-Content -LiteralPath $logPath -Value ([string] $_) }
    }

    if ($exitCode -ne 0) {
        throw "graphify update keluar dengan exit code $exitCode"
    }

    $timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    Add-Content -LiteralPath $logPath -Value "[$timestamp][$Event] incremental update completed"
    Write-Host "[graphify sync:$Event] graph sudah diperiksa/diperbarui."
}
catch {
    $message = "[graphify sync:$Event] update gagal tetapi operasi Git tetap dilanjutkan: $($_.Exception.Message)"
    Write-Warning $message
    try {
        $fallbackLog = Join-Path $env:USERPROFILE '.cache\graphify-git-sync.log'
        Add-Content -LiteralPath $fallbackLog -Value "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] $message"
    }
    catch {
        # Logging must never block Git.
    }
}

exit 0