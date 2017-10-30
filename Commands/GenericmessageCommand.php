<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
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
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
	    $inline_keyboard = new InlineKeyboard([
		    ['text' => '👍🏻 11', 'callback_data' => 'like'],
		    ['text' => '👎🏻 33', 'callback_data' => 'dislike'],
	    ]);

	    $data = [
		    'chat_id' => $this->getMessage()->getChat()->getId(),
		    'audio'    => "CQADAgADcQADC8x5S0Nip46xdLbpAg",
		    'reply_markup' => $inline_keyboard
	    ];

	    return Request::sendAudio($data);
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
	    $inline_keyboard = new InlineKeyboard([
		    ['text' => '👍🏻 11', 'callback_data' => 'like'],
		    ['text' => '👎🏻 33', 'callback_data' => 'dislike'],
	    ]);

	    $data = [
		    'chat_id' => $this->getMessage()->getChat()->getId(),
		    'audio'    => "CQADAgADcQADC8x5S0Nip46xdLbpAg",
		    'reply_markup' => $inline_keyboard
	    ];

	    return Request::sendAudio($data);
    }
}
