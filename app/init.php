<?php
// $db = new Capella(Config::get('database'));
$app->get("/page/[page:(test|hi)]", 'Home.index')->name('test');
echo phpversion();
