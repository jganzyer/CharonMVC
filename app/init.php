<?php
// $db = new Capella(Config::get('database'));
$app->get("/page/[page:(test|hi)]", 'Home.index')->name('test');

$pattern = '/page/[variable:(regex)]';

$pattern = '/page/[testing:(\d+)]';
// matches: /page/(0, 1, 2 ,3...)

Route::get($pattern, $callback);
Route::post($pattern, $callback);
Route::put($pattern, $callback);
Route::delete($pattern, $callback);
Route::any($pattern, $callback);
Route::map($methods, $pattern, $callback);
$methods = 'GET,POST,PUT';
$callback = function() {  };
$callback = 'Home.index';
