<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Dibi
$container['dibi'] = function ($c){
    $settings = $c->get('settings')['db'];
    $database = \dibi::connect([
        'driver' => 'mysqli',
        'host' => $settings['host'],
        'username' => $settings['user'],
        'password' => $settings['pass'],
        'database' => $settings['dbname']
    ]);
    return $database;
};