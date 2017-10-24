<?php
namespace Longman\TelegramBot;

use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\InlineKeyboard;

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

$domain = 'https://jk-music-bot.herokuapp.com/';

$bot_api_key  = '461745599:AAHzddZi6dUJ2o2eOOhvsP1ecgnB8WQF5iM';
$bot_username = 'jonkofee_music_bot';

// Define all IDs of admin users in this array (leave as empty array if not used)
$admin_users = [
//    123,
];

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
	__DIR__ . '/Commands/',
];

try {
//    // Create Telegram API object
	$telegram = new Telegram($bot_api_key, $bot_username);
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
	//$telegram->enableBotan('your_botan_token');

	// Requests Limiter (tries to prevent reaching Telegram API limits)
	//$telegram->enableLimiter();

	// Handle telegram webhook request
	//$telegram->handle();
	if (empty($bot_username)) {
		throw new Exception\TelegramException('Bot Username is not defined!');
	}

	$input = Request::getInput();

//	if (empty($input)) {
//		throw new Exception\TelegramException('Input is empty!');
//	}

	$post = json_decode($input, true);
//	if (empty($post)) {
//		throw new Exception\TelegramException('Invalid JSON!');
//	}

	$update = new Update($post, $bot_username);


	$message = $update->getMessage();

	$chat_id = $message->getChat()->getId();

	$switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';

	$inline_keyboard = new InlineKeyboard([
		['text' => '👍🏻', 'callback_data' => 'identifier'],
		['text' => '👎🏻', 'callback_data' => 'identifier'],
	]);

	$data = [
		'chat_id' => $chat_id,
		'audio'    => "CQADAgADcQADC8x5S0Nip46xdLbpAg",
		'reply_markup' => $inline_keyboard
	];

	return Request::sendAudio($data);


} catch (Exception\TelegramException $e) {
	// Silence is golden!
	//echo $e;
	// Log telegram errors
	TelegramLog::error($e);
} catch (Exception\TelegramException $e) {
	// Silence is golden!
	// Uncomment this to catch log initialisation errors
	//echo $e;
}
