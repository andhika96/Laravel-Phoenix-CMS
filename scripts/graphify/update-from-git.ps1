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

    function Invoke-GraphifyCodeUpdate {
        param([switch] $Force)

        $arguments = @('update', $repoRoot)
        if ($Force) {
            $arguments += '--force'
        }

        $previousErrorActionPreference = $ErrorActionPreference
        $ErrorActionPreference = 'Continue'
        try {
            if ($python) {
                $commandOutput = & $python -m graphify @arguments 2>&1
            }
            else {
                $graphifyCommand = Get-Command graphify.exe -ErrorAction SilentlyContinue
                if (-not $graphifyCommand) {
                    $graphifyCommand = Get-Command graphify -ErrorAction SilentlyContinue
                }
                if (-not $graphifyCommand) {
                    throw 'Graphify tidak ditemukan. Jalankan scripts\graphify\setup-windows.ps1.'
                }
                $commandOutput = & $graphifyCommand.Source @arguments 2>&1
            }

            return [PSCustomObject]@{
                ExitCode = $LASTEXITCODE
                Output   = @($commandOutput)
            }
        }
        finally {
            $ErrorActionPreference = $previousErrorActionPreference
        }
    }

    $result = Invoke-GraphifyCodeUpdate
    if ($result.Output) {
        $result.Output | ForEach-Object { Add-Content -LiteralPath $logPath -Value ([string] $_) }
    }

    if ($result.ExitCode -ne 0) {
        $outputText = ($result.Output | ForEach-Object { [string] $_ }) -join [Environment]::NewLine
        $staleCorpusGuard = $outputText -match 'Refusing to overwrite' -and $outputText -match 'left the scan corpus'

        if (-not $staleCorpusGuard) {
            throw "graphify update keluar dengan exit code $($result.ExitCode)"
        }

        # A tracked ignore-rule change can intentionally remove old plan, backup,
        # or generated files from the local graph. Graphify fails closed first;
        # only this exact dual diagnostic is safe to recover with --force.
        $recoveryMessage = "[$timestamp][$Event] stale corpus guard detected; running verified forced code-only rebuild"
        Add-Content -LiteralPath $logPath -Value $recoveryMessage
        Write-Host "[graphify sync:$Event] graph lama memuat file yang sekarang dikecualikan; melakukan rebuild code-only aman..."

        $recovery = Invoke-GraphifyCodeUpdate -Force
        if ($recovery.Output) {
            $recovery.Output | ForEach-Object { Add-Content -LiteralPath $logPath -Value ([string] $_) }
        }
        if ($recovery.ExitCode -ne 0) {
            throw "rebuild Graphify dengan --force keluar dengan exit code $($recovery.ExitCode)"
        }
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
