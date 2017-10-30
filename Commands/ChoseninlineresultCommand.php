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
use Longman\TelegramBot\Request;

/**
 * Chosen inline result command
 *
 * Gets executed when an item from an inline query is selected.
 */
class ChoseninlineresultCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'choseninlineresult';

    /**
     * @var string
     */
    protected $description = 'Chosen result query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
//	    $inline_keyboard = new InlineKeyboard([
//		    ['text' => '👍🏻 11', 'callback_data' => ['like', 'a']],
//		    ['text' => '👎🏻 33', 'callback_data' => 'dislike'],
//	    ]);

	    $data = [
		    'chat_id' => $this->getMessage()->getChat()->getId(),
		    'text'    => "CQADAgADcQADC8x5S0Nip46xdLbpAg",
//		    'reply_markup' => $inline_keyboard
	    ];

	    return Request::sendMessage($data);
    }
}
