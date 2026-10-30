<?php
$root=dirname(__DIR__,2);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db=Illuminate\Support\Facades\DB::connection();
$needles=['mockups/','build/assets/','app-B-qvmLQ3.js','app-l0sNRNKZ.js'];
$db->statement('START TRANSACTION READ ONLY');
try {
 $columns=$db->select("SELECT TABLE_NAME AS t,COLUMN_NAME AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','json')",[$db->getDatabaseName()]);
 $hits=[];
 foreach($columns as $column){
  $table=str_replace('`','``',$column->t);$field=str_replace('`','``',$column->c);
  foreach($needles as $needle){
   $q=$db->getPdo()->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$field}` LIKE ?");$q->execute(['%'.$needle.'%']);$count=(int)$q->fetchColumn();
   if($count)$hits[]=['table'=>$column->t,'column'=>$column->c,'needle'=>$needle,'count'=>$count];
  }
 }
 $result=['mode'=>'READ ONLY transaction','columns'=>count($columns),'hits'=>$hits];
 file_put_contents(__DIR__.'/database-check.json',json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
 echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
} finally {$db->rollBack();}
