<?php
$root=dirname(__DIR__,2);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$state=json_decode(file_get_contents(__DIR__.'/preflight.json'),true);
$needles=array_map(fn($m)=>substr($m['old'],strlen('public/')),array_filter($state['moves'],fn($m)=>$m['kind']==='stylesheet'));
$db=Illuminate\Support\Facades\DB::connection();
$db->statement('START TRANSACTION READ ONLY');
try {
    $columns=$db->select("SELECT TABLE_NAME AS t,COLUMN_NAME AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','json')",[$db->getDatabaseName()]);
    $hits=[];
    foreach($columns as $column){
        $table=str_replace('`','``',$column->t);$field=str_replace('`','``',$column->c);
        $query=$db->getPdo()->prepare("SELECT `{$field}` FROM `{$table}` WHERE `{$field}` LIKE ?");
        $query->execute(['%assets/css/%']);
        while(($value=$query->fetchColumn())!==false){
            foreach($needles as $needle){if(str_contains($value,$needle))$hits[]=['table'=>$column->t,'column'=>$column->c,'path'=>$needle];}
        }
    }
    $result=['mode'=>'READ ONLY transaction','columns'=>count($columns),'hits'=>$hits];
    file_put_contents(__DIR__.'/database-path-references.json',json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
} finally {$db->rollBack();}
