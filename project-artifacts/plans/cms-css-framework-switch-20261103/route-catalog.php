<?php
$root=dirname(__DIR__,3);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$routes=[];
foreach(Illuminate\Support\Facades\Route::getRoutes() as $route){
 $action=$route->getActionName();$uri=$route->uri();
 if(!str_contains($action,'App\\Http\\Controllers\\Web\\'))continue;
 if(preg_match('/\\\\(Auth|Homepage|Article|PageBuilder[^\\\\]*|FileManagerV2|Experimental|Setup)\\\\/i',$action))continue;
 if(!in_array('GET',$route->methods(),true))continue;
 [$class,$method]=array_pad(explode('@',$action,2),2,'__invoke');
 $controller=$root.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
 $views=[];
 if(is_file($controller)){
  preg_match_all('/\bview\(\s*[\'"]([^\'"]+)[\'"]/m',file_get_contents($controller),$matches);
  $views=array_values(array_unique($matches[1]));
 }
 $routes[]=['uri'=>$uri,'name'=>$route->getName(),'action'=>$action,'middleware'=>$route->gatherMiddleware(),'controllerExists'=>is_file($controller),'candidateControllerViews'=>$views];
}
file_put_contents(__DIR__.'/route-catalog.json',json_encode($routes,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo count($routes).' candidate GET routes recorded; controller view literals require per-method verification.'.PHP_EOL;
