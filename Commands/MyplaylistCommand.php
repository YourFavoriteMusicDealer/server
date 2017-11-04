<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Phalcon\Mvc\Model\Resultset\Simple;

class StartCommand extends SystemCommand
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
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
	    $message = $this->getMessage();

	    $chat_id = $message->getChat()->getId();

	    $sqlQuery = "SELECT track.telegram_message_id FROM rating
					LEFT JOIN track ON track.id = rating.track_id
					WHERE user_id = $chat_id AND lik = TRUE";


	    $arr =  (new Simple(
		    null,
		    null,
		    (new \Track())->getReadConnection()->query($sqlQuery)
	    ))->toArray();

	    foreach ($arr as $item) {
		    $data = [
			    'chat_id' => $chat_id,
			    'from_chat_id' => '@jonkofee_music',
			    'message_id' => $item['telegram_message_id'],
			    'disable_notification' => true
		    ];

		    \Longman\TelegramBot\Request::forwardMessage($data);
	    }
    }
}
