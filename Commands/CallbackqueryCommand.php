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

use Core\Enum\Exception;
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

        $fromId = $callback_query->getFrom()->getId();

        $messageId = $callback_query->getMessage()->getMessageId();

        $rowTrack = \Track::findFirst("telegram_message_id = $messageId");

        if (!$rowTrack) return false;

		$rowRating = \Rating::findFirst("track_id = {$rowTrack->id} AND user_id = {$fromId}");

		if (!$rowRating) {
			(new \Rating([
				'user_id' => $fromId,
				'track_id'=> $rowTrack->id,
				'like' => $callback_data === 'like',
				'dislike' => $callback_data === 'dislike'
			]))->save();
		} else {
			$rowRating->like = $callback_data === 'like';
			$rowRating->dislike = $callback_data === 'dislike';

			$rowRating->save();
		}
	    error_log("TEST");
	    $inline_keyboard = new InlineKeyboard([
		    ['text' => "👍🏻 {$rowTrack->like}", 'callback_data' => 'like'],
		    ['text' => "👎🏻 {$rowTrack->like}", 'callback_data' => 'dislike'],
	    ]);

	    Request::editMessageReplyMarkup([
		    'chat_id' => $callback_query->getMessage()->getChat()->getId(),
		    'message_id' => $callback_query->getMessage()->getMessageId(),
		    'reply_markup' => $inline_keyboard
	    ]);

	    $data = [
		    'callback_query_id' => $callback_query_id,
		    'text'              => 'Хорошо, я запомнил😉',
		    'show_alert'        => true,
	    ];

	    return Request::answerCallbackQuery($data);
    }
}
