<?php
require(__DIR__ . '/../vendor/autoload.php');
define('SOURCE_PATH', dirname(__DIR__) . '/tests/_data');
define('DESTINATION_PATH', dirname(__DIR__) . '/tests/_output');
define('MATRIX', [
    'profile' => [
        ['width' => 100, 'height' => 100],
        ['width' => 50, 'height' => 50],
        ['width' => 16, 'height' => 16],
        ['width' => 200, 'height' => 200],
    ],
    'degree' => [
        ['width' => 100, 'height' => 100],
        ['width' => 600, 'height' => 600],
    ],
]);
