<?php

use Illuminate\Container\Container;
use \Illuminate\Database\Capsule\Manager;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Support\Fluent;
use Illuminate\View\ViewServiceProvider;

require __DIR__ . '/../vendor/autoload.php';
$app = new Illuminate\Container\Container();
//Container::setInstance($app);
with(new Illuminate\Events\EventServiceProvider($app))->register();
with(new Illuminate\Routing\RoutingServiceProvider($app))->register();

//// 启动 Eloquent ORM 模块并进行相关配置
//$manager = new Manager();
//$manager->addConnection(require '../config/database.php');
//$manager->bootEloquent();


//$app->instance('config',new Fluent());
//$app['config']['view.compiled'] = "D:\\workspace\\studylara\\storage\\framework\\views\\";
//$app['config']['view.paths'] = ["D:\\workspace\\studylara\\resources\\views\\"];
//with(new ViewServiceProvider($app))->register();
//with(new FilesystemServiceProvider($app))->register();

require __DIR__ . '/../app/Http/routes.php';exit();
$request = Illuminate\Http\Request::createFromGlobals();
var_dump($request);exit;
$response = $app['router']->dispatch($request);
$response->send();