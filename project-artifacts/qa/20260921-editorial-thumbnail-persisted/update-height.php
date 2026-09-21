<?php
require __DIR__.'/../../../vendor/autoload.php';
$app = require __DIR__.'/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Article\ArticleTemplateSetting;
use Illuminate\Support\Facades\DB;
$result = DB::transaction(function () {
    $setting = ArticleTemplateSetting::query()->lockForUpdate()->findOrFail(1);
    $before = $setting->getAttributes();
    $options = $setting->archive_template_options;
    $old = data_get($options, 'editorial-journal.thumbnail.height');
    if ($old !== '5.63rem' || $setting->archive_template !== 'editorial-journal') {
        throw new RuntimeException('Settings changed since inspection; no write performed.');
    }
    $backupDir = base_path('project-artifacts/backups/'.date('Ymd_His').'-editorial-thumbnail-persisted');
    if (!mkdir($backupDir, 0777, true)) throw new RuntimeException('Backup directory failed');
    $backupPath = $backupDir.'/article-template-setting-1.json';
    $json = json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    if (file_put_contents($backupPath, $json) !== strlen($json) || file_get_contents($backupPath) !== $json) {
        throw new RuntimeException('Backup verification failed');
    }
    data_set($options, 'editorial-journal.thumbnail.height', '13.5rem');
    $setting->archive_template_options = $options;
    $setting->save();
    $fresh = $setting->fresh();
    if ($fresh->archive_template_options !== $options) throw new RuntimeException('Saved options mismatch');
    $after = $fresh->getAttributes();
    foreach ($before as $key => $value) {
        if (!in_array($key, ['archive_template_options', 'updated_at'], true) && $after[$key] !== $value) {
            throw new RuntimeException('Unexpected change to '.$key);
        }
    }
    $comparison = $fresh->archive_template_options;
    data_set($comparison, 'editorial-journal.thumbnail.height', $old);
    if ($comparison !== json_decode($before['archive_template_options'], true, 512, JSON_THROW_ON_ERROR)) {
        throw new RuntimeException('Other template options changed');
    }
    return ['before'=>$old, 'after'=>data_get($fresh->archive_template_options, 'editorial-journal.thumbnail.height'), 'backup'=>$backupPath, 'other_settings_unchanged'=>true];
});
file_put_contents(__DIR__.'/result.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
