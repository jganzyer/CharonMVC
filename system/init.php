<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath('.').DS);
define('APP_DIR', ROOT.'app'.DS);
define('SYSTEM_DIR', ROOT.'system'.DS);
define('CORE_DIR', SYSTEM_DIR.'core'.DS);
define('CACHE_DIR', APP_DIR.'cache'.DS);
define('COMMAND_DIR', APP_DIR.'command'.DS);
define('MODEL_DIR', APP_DIR.'model'.DS);
define('VIEW_DIR', APP_DIR.'view'.DS);
define('CONTROLLER_DIR', APP_DIR.'controller'.DS);

$app = new Charon();
