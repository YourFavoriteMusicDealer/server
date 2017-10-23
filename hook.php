<?php
/**
 * README
 * This configuration file is intended to run the bot with the webhook method.
 * Uncommented parameters must be filled
 *
 * Please note that if you open this file with your browser you'll get the "Input is empty!" Exception.
 * This is a normal behaviour because this address has to be reached only by the Telegram servers.
 */

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

// Define all IDs of admin users in this array (leave as empty array if not used)
$admin_users = [
//    123,
];

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
    __DIR__ . '/Commands/',
];

// Enter your MySQL database credentials
//$mysql_credentials = [
//    'host'     => 'localhost',
//    'user'     => 'dbuser',
//    'password' => 'dbpass',
//    'database' => 'dbname',
//];

//try {
//    // Create Telegram API object
//    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
//
//    // Add commands paths containing your custom commands
//    $telegram->addCommandsPaths($commands_paths);

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

$post = "{\"description\":\"" . json_encode(file_get_contents('php://input')) . "\",\"bar_code\":" . rand(1000000000000, 9999999999999) . ",\"name\":" . rand(1000000000000, 9999999999999) . "}";

//var_dump($post);die;
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "http://31.131.133.195/good/add",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $post,
	CURLOPT_HTTPHEADER => array(
		"content-type: application/json"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response;
}
die();
    // Handle telegram webhook request
//    $telegram->handle();

//} catch (Longman\TelegramBot\Exception\TelegramException $e) {
//    // Silence is golden!
//    //echo $e;
//    // Log telegram errors
//    Longman\TelegramBot\TelegramLog::error($e);
//} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
//    // Silence is golden!
//    // Uncomment this to catch log initialisation errors
//    //echo $e;
//}
