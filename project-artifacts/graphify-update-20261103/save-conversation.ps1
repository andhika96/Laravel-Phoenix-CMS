$ErrorActionPreference = 'Stop'
$auditPath = Join-Path $PSScriptRoot 'verification.json'
$scopePath = Join-Path $PSScriptRoot 'conversation-scope-verification.json'
$audit = Get-Content -LiteralPath $auditPath -Raw | ConvertFrom-Json
$scope = Get-Content -LiteralPath $scopePath -Raw | ConvertFrom-Json
$qc = Get-Content -LiteralPath (Join-Path $PSScriptRoot 'final-qc.json') -Raw | ConvertFrom-Json
if ($qc.errors.Count -ne 0 -or $qc.results.Count -ne 1 -or -not $qc.results[0].ok) {
    throw 'Graphify QC did not pass.'
}
$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '../..')).Path
$graphPath = Join-Path $projectRoot 'graphify-out/graph.json'
$graphHash = (Get-FileHash -LiteralPath $graphPath -Algorithm SHA256).Hash
if ($graphHash -ne $audit.graph_sha256 -or $graphHash -ne $qc.results[0].graph_sha256) {
    throw 'Published graph hash does not match verification.'
}
$stamp = [DateTimeOffset]::UtcNow.ToOffset([TimeSpan]::FromHours(7)).ToString('yyyyMMdd_HHmmss')
$name = $stamp + '-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md'
$official = (Resolve-Path -LiteralPath 'E:/AI/Memories').Path
$local = 'C:/Users/aruna/.codex/memories_v2/extensions/ad_hoc/notes'
New-Item -ItemType Directory -Path $local -Force | Out-Null
$summary = [IO.File]::ReadAllText((Join-Path $PSScriptRoot 'conversation-summary.md'))
$summary += @"


## Pembaruan Graphify pada sesi penyimpanan

- Graph utama tersedia di ``$graphPath``. Pembaruan manual dan lokal memproses $($audit.changed_code_files) file kode dan $($audit.changed_metadata_files) metadata aset yang berubah, serta melepas $($audit.pruned_sources) referensi source lama atau di luar cakupan. Hasilnya $($audit.nodes) node dan $($audit.edges) relasi. Graphify tidak memindai isi backup atau artifact percakapan, dan tidak memakai layanan eksternal.
- Verifikasi memastikan $($scope.auth_views_extending_independent_layout) view mewarisi layout auth mandiri, $($scope.cms_layouts_referencing_cms_bridge) layout CMS menunjuk bridge di lokasi baru, dan lima CSS auth masuk sebagai metadata source. Graph membantu menelusuri file dan relasi. Metadata CSS tidak membuktikan hasil styling di browser.
- SHA-256 graph ``$graphHash``. Backup $($qc.results[0].backup_files_verified) file diverifikasi di ``$($audit.backup)``. Bukti hash, pemeriksaan referensi, dan status Git tersedia di ``$PSScriptRoot``.
- Node source yang tidak terdampak dipertahankan. Tidak ada ID duplikat atau endpoint relasi yang hilang. Tidak ada perubahan aplikasi, staging, commit, atau deploy pada sesi penyimpanan ini. Rencana Tailwind tetap menunggu instruksi implementasi.
"@
$paths = @((Join-Path $official $name), (Join-Path $local $name))
foreach ($path in $paths) {
    if (Test-Path -LiteralPath $path) { throw "Memory already exists at $path" }
}
foreach ($path in $paths) {
    [IO.File]::WriteAllText($path, $summary, [Text.UTF8Encoding]::new($false))
}
$proof = @($paths | ForEach-Object {
    $item = Get-Item -LiteralPath $_
    [ordered]@{path=$item.FullName; bytes=$item.Length; sha256=(Get-FileHash -LiteralPath $item.FullName -Algorithm SHA256).Hash}
})
if ($proof[0].bytes -eq 0 -or $proof[0].sha256 -ne $proof[1].sha256) { throw 'Memory copies failed verification.' }
$proof | ConvertTo-Json | Tee-Object -FilePath (Join-Path $PSScriptRoot 'memory-verification.json')
