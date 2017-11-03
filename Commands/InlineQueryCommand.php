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
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultAudio;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;

/**
 * Inline query command
 *
 * Command that handles inline queries.
 */
class InlinequeryCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'inlinequery';

	/**
	 * @var string
	 */
	protected $description = 'Reply to inline query';

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
		$inline_query = $this->getInlineQuery();
		$query        = $inline_query->getQuery();

		$data    = ['inline_query_id' => $inline_query->getId()];
		$results = [];

		$inline_keyboard = new InlineKeyboard([
			['text' => "👍🏻 1", 'callback_data' => 'like'],
			['text' => "👎🏻 2", 'callback_data' => 'dislike'],
		]);

		if ($query !== '') {
			$articles = [
				[
					'type'                  => 'audio',
					'audio_url'             => 'CQADAgADdwADkwbYSzvvt6MfaW7QAg',
					'id'                    => '001',
					'title'                 => 'Nas',
					'description'           => 'Made You Look',
					'reply_markup'          => $inline_keyboard,
				],
				[
					'type'                  => 'audio',
					'audio_url'             => 'CQADAgADdwADkwbYSzvvt6MfaW7QAg',
					'id'                    => '002',
					'title'                 => 'Nas',
					'description'           => 'Made You Look',
					'reply_markup'          => $inline_keyboard,
				],
				[
					'type'                  => 'audio',
					'audio_url'             => 'CQADAgADdwADkwbYSzvvt6MfaW7QAg',
					'id'                    => '003',
					'title'                 => 'Nas',
					'description'           => 'Made You Look',
					'reply_markup'          => $inline_keyboard,
				],
			];

			foreach ($articles as $article) {
				$results[] = new InlineQueryResultAudio($article);
			}
		}

		$data['results'] = '[' . implode(',', $results) . ']';

		return Request::answerInlineQuery($data);
	}
} 