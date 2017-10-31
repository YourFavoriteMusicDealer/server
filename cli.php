<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

// Использование стандартного CLI контейнера для сервисов
$di = new CliDI();

/**
 * Регистрируем автозагрузчик и сообщаем ему директорию
 * для регистрации каталога задач
 */
$loader = new Loader();

$loader->registerDirs(
	[
		__DIR__ . '/tasks',
	]
);

$loader->register();

// Загрузка файла конфигурации (если есть)
$configFile = __DIR__ . '/config/config.php';

if (is_readable($configFile)) {
	$config = include $configFile;

	$di->set('config', $config);
}

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
	fwrite(STDERR, $e->getMessage() . PHP_EOL);
	exit(1);
} catch (\Throwable $throwable) {
	fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
	exit(1);
} catch (\Exception $exception) {
	fwrite(STDERR, $exception->getMessage() . PHP_EOL);
	exit(1);
}