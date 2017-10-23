<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

require 'config.php';

$bot_api_key  = '461745599:AAHzddZi6dUJ2o2eOOhvsP1ecgnB8WQF5iM';
$bot_username = 'jonkofee_music_bot';
$hook_url = $domain . 'hook.php';

try {
	// Create Telegram API object
	$telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

	// Set webhook
	$result = $telegram->setWebhook($hook_url);
	if ($result->isOk()) {
		echo $result->getDescription();
	}
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
	// log telegram errors
	 echo $e->getMessage();
}