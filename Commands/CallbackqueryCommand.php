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
use Phalcon\Debug\Dump;
use Phalcon\Mvc\Model\Resultset\Simple;

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

      if ($inline_message_id = $callback_query->getInlineMessageId()) return $this->_inlineMessage($inline_message_id);

      $fromId = $callback_query->getFrom()->getId();
      $fromUsername = $callback_query->getFrom()->getUsername();

      $messageId = $callback_query->getMessage()->getMessageId();

      $rowTrack = \Track::findFirst("telegram_message_id = $messageId");

      if (!$rowTrack) return false;

      $rowRating = \Rating::findFirst("track_id = {$rowTrack->id} AND user_id = {$fromId}");

      if (!$rowRating) {
        (new \Rating([
          'user_id' => $fromId,
          'track_id'=> $rowTrack->id,
          'lik' => $callback_data === 'like',
          'dislik' => $callback_data === 'dislike',
          'username' => $fromUsername
        ]))->save();
      } else {
        $rowRating->lik = $callback_data === 'like';
        $rowRating->dislik = $callback_data === 'dislike';

        $rowRating->save();
      }

      Request::answerCallbackQuery([
        'callback_query_id' => $callback_query_id,
        'text'              => 'Понял, принял😉'
      ]);

      $sqlQuery = "SELECT track.*, COALESCE(SUM(lik::integer), 0) as likes, COALESCE(SUM(dislik::integer), 0) as dislikes FROM track 
                    LEFT JOIN rating ON track.id = rating.track_id 
                    WHERE telegram_message_id = $messageId 
                    GROUP BY track.id";

      $rowTrack = (new Simple(
        null,
        null,
        (new \Track())->getReadConnection()->query($sqlQuery)
      ))->toArray()[0];

	    Request::editMessageReplyMarkup([
		    'chat_id' => $callback_query->getMessage()->getChat()->getId(),
		    'message_id' => $callback_query->getMessage()->getMessageId(),
		    'reply_markup' => new InlineKeyboard([
          ['text' => "👍🏻 {$rowTrack['likes']}", 'callback_data' => 'like'],
          ['text' => "👎🏻 {$rowTrack['dislikes']}", 'callback_data' => 'dislike'],
        ])
	    ]);

	    return true;
    }

    private function _inlineMessage($id)
    {
	    $callback_query    = $this->getCallbackQuery();
	    $callback_query_id = $callback_query->getId();
	    $callback_data     = $callback_query->getData();

	    $fromId = $callback_query->getFrom()->getId();
	    $fromUsername = $callback_query->getFrom()->getUsername();

	    $rowRating = \Rating::findFirst("track_id = {$rowTrack->id} AND user_id = {$fromId}");

	    if (!$rowRating) {
		    (new \Rating([
			    'user_id' => $fromId,
			    'track_id'=> $rowTrack->id,
			    'lik' => $callback_data === 'like',
			    'dislik' => $callback_data === 'dislike',
			    'username' => $fromUsername
		    ]))->save();
	    } else {
		    $rowRating->lik = $callback_data === 'like';
		    $rowRating->dislik = $callback_data === 'dislike';

		    $rowRating->save();
	    }

      Request::answerCallbackQuery([
        'callback_query_id' => $callback_query_id,
        'text'              => 'Понял, принял😉'
      ]);

      $sqlQuery = "SELECT track.*, COALESCE(SUM(lik::integer), 0) as likes, COALESCE(SUM(dislik::integer), 0) as dislikes FROM track 
                    LEFT JOIN rating ON track.id = rating.track_id 
                    WHERE inline_message_id = $id 
                    GROUP BY track.id";

      $rowTrack = (new Simple(
        null,
        null,
        (new \Track())->getReadConnection()->query($sqlQuery)
      ))->toArray()[0];

	    $inline_keyboard = new InlineKeyboard([
		    ['text' => "👍🏻 {$rowTrack['likes']}", 'callback_data' => 'like'],
		    ['text' => "👎🏻 {$rowTrack['dislikes']}", 'callback_data' => 'dislike'],
	    ]);

	    Request::editMessageReplyMarkup([
		    'chat_id' => '@jonkofee_music',
		    'message_id' => $rowTrack->telegram_message_id,
		    'reply_markup' => $inline_keyboard
	    ]);

	    Request::editMessageReplyMarkup([
		    'inline_message_id' => $id,
		    'reply_markup' => $inline_keyboard
	    ]);

	    return true;
    }
}
