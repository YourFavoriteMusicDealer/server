<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
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

    switch ($message->getText()) {
      case 'плейлист':
      case 'Плейлист':
      case 'playlist':
      case 'Playlist':
      case 'песни':
      case 'tracks':
      case '⏯ Плейлист':
        return $this->_myplalist($message);
        break;
      case 'top':
      case 'top 10':
      case 'top10':
      case '🔝10 месяца':
        return $this->_top($message);
        break;
      default:
        return;
    }
  }

  private function _myplalist($message)
  {
    $userId = $message->getFrom()->getId();

    $sqlQuery = "SELECT track.telegram_message_id FROM rating
					LEFT JOIN track ON track.id = rating.track_id
					WHERE user_id = $userId AND lik = TRUE";


    $arr =  (new Simple(
      null,
      null,
      (new \Track())->getReadConnection()->query($sqlQuery)
    ))->toArray();

    if (!$arr) {
      Request::sendMessage([
        'chat_id' => $message->getChat()->getId(),
        'text' => 'Нет избранных песен. Для того, чтобы они появились нужно поставить 👍🏻 на понравившейся песне в нашем канале',
        'reply_markup' => new \Longman\TelegramBot\Entities\InlineKeyboard([
          ['text' => "Перейти в канал", 'url' => 'https://t.me/jonkofee_music']
        ])
      ]);
    }

    foreach ($arr as $item) {
      $data = [
        'chat_id' => $message->getChat()->getId(),
        'from_chat_id' => '@jonkofee_music',
        'message_id' => $item['telegram_message_id'],
        'disable_notification' => true
      ];

      \Longman\TelegramBot\Request::forwardMessage($data);
    }

    return true;
  }

  private function _top($message)
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
      'text'    => "Вот топ 10 треков по мнению моих подписчиков"
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
}