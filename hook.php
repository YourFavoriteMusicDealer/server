<?php
// Load composer
require_once __DIR__ . '/vendor/autoload.php';

require 'config.php';

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
	__DIR__ . '/Commands/',
];

try {
//    // Create Telegram API object
	$telegram = new \Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
//
//    // Add commands paths containing your custom commands
	$telegram->addCommandsPaths($commands_paths);

	// Enable admin users
//    $telegram->enableAdmins($admin_users);

	// Enable MySQL
	//$telegram->enableMySql($mysql_credentials);

	// Logging (Error, Debug and Raw Updates)
	//Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . "/{$bot_username}_error.log");
	//Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . "/{$bot_username}_debug.log");
	//Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . "/{$bot_username}_update.log");

	// If you are using a custom Monolog instance for logging, use this instead of the above
	//Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

	// Set custom Upload and Download paths
	//$telegram->setDownloadPath(__DIR__ . '/Download');
	//$telegram->setUploadPath(__DIR__ . '/Upload');

	// Here you can set some command specific parameters
	// e.g. Google geocode/timezone api key for /date command
	//$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

	// Botan.io integration
	$telegram->enableBotan('15c09989-c082-4a09-aa58-7529fc7e6159');

	// Requests Limiter (tries to prevent reaching Telegram API limits)
	//$telegram->enableLimiter();

	// Handle telegram webhook request
	$telegram->handle();


} catch (\Longman\TelegramBot\Exception\TelegramException $e) {
	// Silence is golden!
	//echo $e;
	// Log telegram errors
	\Longman\TelegramBot\TelegramLog::error($e);
}
