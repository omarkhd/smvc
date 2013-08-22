<?php
/*
 * you can change the directory of the smvc system folder, just make sure to require
 * the bootstrap.php file with the new correct path, in the line below this comment
 */
require_once __DIR__ . '/system/bootstrap.php';
\smvc\controller\FrontController::run();