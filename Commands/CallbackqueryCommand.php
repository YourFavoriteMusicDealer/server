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
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

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
        $callback_query    = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data     = $callback_query->getData();

        $message = $this->getMessage();

        $count = 6;

        if ($callback_data == 'like') {
        	$count++;
        } elseif ($callback_data == 'dislike') {
        	$count--;
        }

//        Request::editMessageReplyMarkup([
//        	'chat_id' => 325275444,
//	        'message_id' => 391,
//	        'reply_markup' => new InlineKeyboard([
//		        ['text' => "👍🏻 $count", 'callback_data' => 'like'],
//		        ['text' => "👎🏻 $count", 'callback_data' => 'dislike'],
//	        ])
//        ]);

        $data = [
            'callback_query_id' => $callback_query_id,
            'text'              => 123,
            'show_alert'        => true,
        ];

        return Request::answerCallbackQuery($data);
    }
}
