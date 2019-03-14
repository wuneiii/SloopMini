<?php


#require './SloopMini/loader.php';


$app = new SloopMini\App();
$app->regAppDir(realpath('./App'));

$app->addDefault(function () {
    die('hell world');
});

$app->add('/callback_func', function () {
    die('a');
});
$app->add('/callback_object', new \App\BizController());

$app->add('/callback_object2', [
    new \App\BizController(),
    'run'
]);
$app->add('/callback_class', '\App\BizController');

$app->add('/callback_class', \App\BizController::class);

$app->run();