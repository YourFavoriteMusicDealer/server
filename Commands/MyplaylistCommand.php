<?php
namespace Longman\TelegramBot\Commands\SystemCommands;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Phalcon\Mvc\Model\Resultset\Simple;
class MyplaylistCommand extends SystemCommand
{
  /**
   * @var string
   */
  protected $name = 'myplaylist';
  /**
   * @var string
   */
  protected $description = 'Get my playlist';
  /**
   * @var string
   */
  protected $usage = '/myplaylist';
  /**
   * @var string
   */
  protected $version = '1.1.0';
  /**
   * @var bool
   */
  protected $private_only = false;
  /**
   * Command execute method
   *
   * @return \Longman\TelegramBot\Entities\ServerResponse
   * @throws \Longman\TelegramBot\Exception\TelegramException
   */
  public function execute()
  {
    $message = $this->getMessage();

    $userId = $message->getFrom()->getId();

    $sqlQuery = "SELECT track.* FROM rating
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
				'audio'  => $item['telegram_file_id'],
				'performer' => $item['artist'],
				'title' => $item['title']
			];

			$response = \Longman\TelegramBot\Request::sendAudio($data);
    }

    return $response;
  }
}