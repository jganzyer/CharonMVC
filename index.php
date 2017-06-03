<?php
require_once('system\libs\puck\puck.php');
require_once('system\core\config.php');
Config::load('main.json');
Puck::init(Config::get("puck")['mode']);
require_once('system\init.php');
