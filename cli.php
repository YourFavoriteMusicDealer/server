<?php
define('ROOT_DIR', __DIR__);
ini_set('memory_limit', '256M');

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Factory;

require 'vendor/autoload.php';
require 'Core/ConfigIni.php';

// Использование стандартного CLI контейнера для сервисов
$di = new CliDI();

(new Loader())
	->registerDirs([
		'api/controllers/',
		'api/models/',
		'api/validations',
		'Core/helpers',
		'Core/',
		'task/'
	])
	->registerNamespaces([
		'Core' => 'Core',
	])->register();


$config = new \Phalcon\Config\Adapter\Ini('api/config/config.ini');

$di->set('config', $config);


$di->set(
	'db',
	function () {
		return Factory::load($this->get('config')->database);
	}
);

$telegram = new \Longman\TelegramBot\Telegram($config->token, $config->username);
$telegram->addCommandsPaths([
	__DIR__ . '/Commands/'
]);


$di['bot'] = $telegram;

// Создание консольного приложения
$console = new ConsoleApp();

$console->setDI($di);

/**
 * Обработка аргументов консоли
 */
$arguments = [];

foreach ($argv as $k => $arg) {
	if ($k === 1) {
		$arguments['task'] = $arg;
	} elseif ($k === 2) {
		$arguments['action'] = $arg;
	} elseif ($k >= 3) {
		$arguments['params'][] = $arg;
	}
}

try {
	// Обработка входящих аргументов
	$console->handle($arguments);
} catch (\Phalcon\Exception $e) {
	// Связанные с Phalcon вещи указываем здесь
	// ..
	fwrite(STDERR, var_dump($e->getMessage()) . PHP_EOL);
	exit(1);
} catch (\Throwable $throwable) {
	fwrite(STDERR, var_dump($throwable->getMessage()) . PHP_EOL);
	exit(1);
} catch (\Exception $exception) {
	fwrite(STDERR, var_dump($exception->getMessage()) . PHP_EOL);
	exit(1);
}