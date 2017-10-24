<?php
/**
 * README
 * This file is intended to unset the webhook.
 * Uncommented parameters must be filled
 */
// Load composer
require_once __DIR__ . '/vendor/autoload.php';
use Longman\TelegramBot\Request;
require 'config.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    echo "<pre>";
	var_dump(Request::getWebhookInfo());die;
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}
