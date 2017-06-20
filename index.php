  <?php
require_once('system\init.php');
require_once('system\libs\puck\puck.php');
require_once('system\core\config.php');
Config::load('main.json');
Puck::init(Config::get("puck")['mode']);

$app = new Charon();
Route::init($app);
require_once(HELPERS_DIR.'system.php');
oops::init();
oops::add_handler(oops\niceone::class);
require_once(APP_DIR.'init.php');
$app->run();
oops::response();
