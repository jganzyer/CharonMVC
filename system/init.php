<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath('.').DS);

define('APP_DIR', ROOT.'app'.DS);

define('SYSTEM_DIR', ROOT.'system'.DS);
define('CORE_DIR', SYSTEM_DIR.'core'.DS);
define('HELPERS_DIR', SYSTEM_DIR.'helpers'.DS);
define('PUBLIC_DIR', ROOT.'public'.DS);
define('CACHE_DIR', ROOT.'cache'.DS);
define('COMMAND_DIR', APP_DIR.'command'.DS);
define('MODEL_DIR', APP_DIR.'model'.DS);
define('VIEW_DIR', APP_DIR.'view'.DS);
define('CONTROLLER_DIR', APP_DIR.'controller'.DS);
define('CONFIG_DIR', APP_DIR.'config'.DS);

require_once('system\libs\puck\puck.php');
require_once('system\core\config.php');
Config::load('main.json');
$_mode = Config::get("puck")['mode'];
Puck::init($_mode);

$app = new Charon();
Route::init($app);
require_once(HELPERS_DIR.'system.php');
oops::init();
oops::add_handler(oops\niceone::class);
if ($_mode === 0) # PRODUCTION
{
  oops::ignore(E_ALL);
  Charon\Dump::$active = false;
}
require_once(APP_DIR.'init.php');
$app->run();
oops::response();
