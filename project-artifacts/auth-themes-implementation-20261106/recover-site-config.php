<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
set_exception_handler(function (Throwable $error) { fwrite(STDERR, 'Recovery was not completed ('.$error::class.'). Details withheld to protect configuration values.'.PHP_EOL); exit(1); });
$connection = Illuminate\Support\Facades\DB::connection();
$table = $connection->getTablePrefix().'site_config';
$columns = array_column($connection->select('SHOW COLUMNS FROM `'.$table.'`'), 'Field');
$logBase = $connection->selectOne('SELECT @@log_bin_basename AS value')->value;
$logs = array_map(fn ($row) => dirname($logBase).'/'.$row->Log_name, $connection->select('SHOW BINARY LOGS'));
$logs = array_values(array_filter($logs, fn ($file) => filemtime($file) >= strtotime('2026-11-06 11:13:00') && filesize($file) < 20000000));
$command = ['D:/Laragon/bin/mysql/mysql-8.4.5-winx64/bin/mysqlbinlog.exe', '--base64-output=DECODE-ROWS', '-vv', '--start-datetime=2026-11-06 11:13:00', ...$logs];
$process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
fclose($pipes[0]);
$events = []; $event = null; $part = null;
while (($line = fgets($pipes[1])) !== false) {
    if (str_starts_with($line, '### UPDATE ')) {
        if ($event) $events[] = $event;
        $event = str_contains($line, '`'.$table.'`') ? ['before' => [], 'after' => []] : null;
        $part = null;
    } elseif ($event !== null && str_starts_with($line, '### WHERE')) $part = 'before';
    elseif ($event !== null && str_starts_with($line, '### SET')) $part = 'after';
    elseif ($event !== null && $part && preg_match('/^###\s+@(\d+)=(.*?)(?: \/\*.*)?\r?\n$/', $line, $match)) {
        $event[$part][$columns[(int)$match[1]-1]] = $match[2];
    }
}
if ($event) $events[] = $event;
fclose($pipes[1]); $stderr = stream_get_contents($pipes[2]); fclose($pipes[2]);
if (proc_close($process) !== 0) throw new RuntimeException($stderr);
$fields = ['enable_recaptcha_signup', 'enable_recaptcha_signin', 'recaptcha_site_key', 'recaptcha_secret_key', 'enable_ratelimit_signup', 'updated_at'];
$original = null;
foreach ($events as $item) {
    if (($item['after']['recaptcha_site_key'] ?? null) === "'qa-fixture-site-key'" && ($item['before']['recaptcha_site_key'] ?? null) !== "'qa-fixture-site-key'") {
        $original = array_intersect_key($item['before'], array_flip($fields));
        break;
    }
}
if (!$original || count($original) !== count($fields)) throw new RuntimeException('Original configuration row was not found; no data was changed.');
$values = [];
foreach ($original as $key => $literal) {
    $expression = $key === 'updated_at' && ctype_digit($literal) ? 'FROM_UNIXTIME('.$literal.')' : $literal;
    $values[$key] = $connection->selectOne('SELECT '.$expression.' AS value')->value;
}
$report = ['sourceLogs' => array_map('basename', $logs), 'matchingEvents' => count($events), 'originalRecovered' => true,
    'originalSignupFlag' => $values['enable_recaptcha_signup'], 'originalSigninFlag' => $values['enable_recaptcha_signin'],
    'originalSiteKeyEmpty' => empty($values['recaptcha_site_key']), 'originalSecretEmpty' => empty($values['recaptcha_secret_key']), 'restored' => false];
if (in_array('--restore', $argv, true)) {
    $row = $connection->table('site_config')->where('id', 1)->first();
    if ($row->recaptcha_site_key !== 'qa-fixture-site-key' || $row->recaptcha_secret_key !== 'qa-fixture-secret') throw new RuntimeException('Configuration has changed; recovery was not applied.');
    $connection->table('site_config')->where('id', 1)->update($values);
    $after = (array)$connection->table('site_config')->where('id', 1)->first();
    foreach ($values as $key => $value) if ($after[$key] != $value) throw new RuntimeException('Recovery verification failed for '.$key);
    $report['restored'] = true;
    $report['verifiedFields'] = array_keys($values);
    file_put_contents(__DIR__.'/site-config-recovery-report.json', json_encode($report, JSON_PRETTY_PRINT));
}
echo json_encode($report, JSON_PRETTY_PRINT).PHP_EOL;
