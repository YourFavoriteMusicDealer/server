<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\File;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use Phalcon\Exception;
use Phalcon\Mvc\Model\Resultset\Simple;
/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
  /**
   * @var string
   */
  protected $name = 'genericmessage';
  /**
   * @var string
   */
  protected $description = 'Handle generic message';
  /**
   * @var string
   */
  protected $version = '1.1.0';

  /**
   * Execute command
   *
   * @return \Longman\TelegramBot\Entities\ServerResponse
   * @throws \Longman\TelegramBot\Exception\TelegramException
   */
  public function execute()
  {
    $message = $this->getMessage();

    switch (true) {
      case strtolower($message->getText()) == 'плейлист':
      case strtolower($message->getText()) == 'playlist':
      case strtolower($message->getText()) == 'песни':
      case strtolower($message->getText()) == 'tracks':
      case strtolower($message->getText()) == '/myplaylist':
      case strtolower($message->getText()) == '⏯ Плейлист':
        return $this->_myplalist();
      case strtolower($message->getText()) == 'top':
      case strtolower($message->getText()) == 'top 10':
      case strtolower($message->getText()) == 'top10':
      case strtolower($message->getText()) == '🔝10 месяца':
        return $this->_top($message);
      case $this->_isVoice():
        return $this->_recognitionAudio($message);
      default:
        return;
    }
  }

  private function _myplalist()
  {
    $this->telegram->executeCommand('myplaylist');
  }

  private function _top(Message $message)
  {
    $sqlQuery = "SELECT track.*, COALESCE(SUM(lik::integer), 0) as likes, COALESCE(SUM(dislik::integer), 0) as dislikes, COALESCE(SUM(lik::integer) - SUM(dislik::integer), 0) as rating FROM track
		LEFT JOIN rating ON track.id = rating.track_id
		WHERE date_trunc('day', LOCALTIMESTAMP) - date_trunc('day', track.datetime_create) <= interval '30 days'
		GROUP BY track.id
		ORDER BY rating desc, likes desc, datetime_create desc LIMIT 10;";


    $arr =  (new Simple(
      null,
      null,
      (new \Track())->getReadConnection()->query($sqlQuery)
    ))->toArray();

    if (!$arr) {
      Request::sendMessage([
        'chat_id' => $message->getChat()->getId(),
        'parse_mode' => 'Markdown',
        'text' => 'Жаль, но за последний месяц совсем не было песен😔' . PHP_EOL .
                  'Давай это исправим😉' . PHP_EOL .
                  'Предложи [сюда](https://vk.com/jonkofee_music) свои треки дабы все послушали🤗',
        'disable_web_page_preview' => true
      ]);
    }

    Request::sendMessage([
      'chat_id' => $message->getChat()->getId(),
      'text'    => "Вот топ 10 треков по мнению моих подписчиков за последний месяц"
    ]);

    foreach ($arr as $track) {
      $inline_keyboard = new \Longman\TelegramBot\Entities\InlineKeyboard([
        ['text' => "👍🏻 {$track['likes']}", 'callback_data' => 'like'],
        ['text' => "👎🏻 {$track['dislikes']}", 'callback_data' => 'dislike'],
      ]);

      $data = [
        'chat_id' => $message->getChat()->getId(),
        'audio'  => $track['telegram_file_id'],
        'reply_markup' => $inline_keyboard,
        'performer' => $track['artist'],
        'title' => $track['title']
      ];

      \Longman\TelegramBot\Request::sendAudio($data);
    }

    return true;
  }

  private function _isVoice()
  {
    return (bool) $this->getMessage()->getVoice();
  }

  private function _recognitionAudio(Message $message)
  {
    $config = \ConfigIni::getInstance();

    $voice = $message->getVoice();

    if ($voice->getDuration() < 5) {
      return Request::sendMessage([
        'chat_id' => $message->getChat()->getId(),
        'text' => "Слишком мало, дайте хотябы секунд 5 послушать😊",
      ]);
    }

    /* @var File $telegramFile */
    $telegramFile = Request::getFile([
      'file_id' => $voice->getFileId()
    ])->getResult();

    $fileUrl = "https://api.telegram.org/file/bot" . $config->bot->token . "/" . $telegramFile->getFilePath();

    if ($fp_remote = fopen($fileUrl, 'rb')) {
      $localtempfilename = sys_get_temp_dir() . "/{$telegramFile->getFileId()}.ogg";

      if ($fp_local = fopen($localtempfilename, 'wb')) {
        while ($buffer = fread($fp_remote, 8192)) {
          fwrite($fp_local, $buffer);
        }

        fclose($fp_local);
      }
      fclose($fp_remote);
    } else {
      throw new Exception('Не получается загрузить файл');
    }

    $post = [
      "sample"            => new \CURLFile($localtempfilename, "mp3", basename($localtempfilename)),
      "sample_bytes"      => filesize($localtempfilename),
      "access_key"        => $config->acr->key,
      "data_type"         => 'audio',
      "signature"         => base64_encode(hash_hmac("sha1", "POST" . "\n" . "/v1/identify" ."\n" . $config->acr->key . "\n" . "audio" . "\n" . "1" . "\n" . time(), $config->acr->secret, true)),
      "signature_version" => "1",
      "timestamp"         => time()
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://identify-eu-west-1.acrcloud.com/v1/identify");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);

    unlink($localtempfilename);

    if (!$response) {
      throw new Exception(curl_error($ch));
    }

    $data = json_decode($response);

    if ($data->status->code == 0) {
      $meta = $data->metadata->music[0];

      $arrArtists = array_column($meta->artists, 'name');

      $text = $arrArtists[0] . ' - ' . $meta->title;

      if (count($arrArtists) > 1) {
        unset($arrArtists[0]);

        $text .= ' ' . '(feat. ' . implode(', ', $arrArtists) . ')';
      }
    } else {
      $text = 'Хмммм... Что-то я не знаю такой трек🤔';
    }

    return Request::sendMessage([
      'chat_id' => $message->getChat()->getId(),
      'text' => $text,
    ]);
  }
}