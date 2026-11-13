<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Autoload dependencies
require __DIR__ . '/vendor/autoload.php';

// Initialize Capsule
$capsule = new Capsule;

$driver = $_ENV['DB_CONNECTION'] ?? 'sqlite';

if ($driver === 'sqlite') {
    $dbFile = $_ENV['DB_DATABASE'] ?? (__DIR__ . '/database.sqlite');
    if (!str_contains($dbFile, '/') && !str_contains($dbFile, '\\')) {
        $dbFile = __DIR__ . '/' . $dbFile;
    }
    if (!file_exists($dbFile)) {
        touch($dbFile);
    }
    $capsule->addConnection([
        'driver'   => 'sqlite',
        'database' => $dbFile,
        'prefix'   => '',
    ]);
} else {
    $capsule->addConnection([
        'driver'    => $driver,
        'host'      => $_ENV['DB_HOST'] ?? 'localhost',
        'port'      => $_ENV['DB_PORT'] ?? 3306,
        'database'  => $_ENV['DB_DATABASE'] ?? 'parivar_motors',
        'username'  => $_ENV['DB_USERNAME'] ?? 'root',
        'password'  => $_ENV['DB_PASSWORD'] ?? '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule;
