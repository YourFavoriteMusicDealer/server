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
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultCachedAudio;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use Phalcon\Mvc\Model\Resultset\Simple;

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
	public function executeTest()
	{
		$inline_query = $this->getInlineQuery();
		$query        = $inline_query->getQuery();

		$sqlQuery = "SELECT track.*, COALESCE(SUM(lik::integer), 0) as likes, COALESCE(SUM(dislik::integer), 0) as dislikes FROM track
					LEFT JOIN rating ON track.id = rating.track_id
					WHERE LOWER(track.artist) LIKE LOWER('%$query%') OR LOWER(track.title) LIKE LOWER('%$query%')
					GROUP BY track.id
					ORDER BY likes desc";

		$arrTracks =  (new Simple(
			null,
			null,
			(new \Track())->getReadConnection()->query($sqlQuery)
		))->toArray();

//		if (!$arrTracks) return false;

		$data    = ['inline_query_id' => $inline_query->getId()];
		$results = [];

		foreach ($arrTracks as $track) {
//			$inline_keyboard = new \Longman\TelegramBot\Entities\InlineKeyboard([
//				['text' => "👍🏻 {$track['likes']}", 'callback_data' => 'like'],
//				['text' => "👎🏻 {$track['dislikes']}", 'callback_data' => 'dislike'],
//			]);

			$results[] = new \Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultAudio([
				'audio_url'             => $track['telegram_file_id'],
				'id'                    => $track['id'],
				'title'                 => 'asd',
//				'reply_markup'          => $inline_keyboard
			]);
		}

		$data['results'] = json_encode($results);

		$request = Request::answerInlineQuery($data);

		TelegramLog::debug(var_dump($inline_query, $data, $request));

		return $request;
	}

	public function execute()
	{
		$inline_query = $this->getInlineQuery();
		$query        = $inline_query->getQuery();
		$data    = ['inline_query_id' => $inline_query->getId()];
		$results = [];
		if ($query !== '') {
			$articles = [
				[
					'id'                    => '020',
					'title'                 => 'Lions',
					'audio_url'         => 'CQADAgADbQADkwbYSxkjttQgDwiZAg',
				],
				[
					'id'                    => '050',
					'title'                 => 'Ниа (feat. Райда, 104, Скриптонит)',
					'audio_url'         => 'CQADAgADpQADbIvgS1tWrEymbnwvAg',
				],
				[
					'id'                    => '073',
					'title'                 => 'Wild For The Night (Feat. Skrillex)',
					'audio_url'         => 'CQADAgADkgADbIvwS1yWazKXhAHcAg',
				],
			];
			foreach ($articles as $article) {
				$results[] = new InlineQueryResultAudio($article);
			}
		}
		$data['results'] = json_encode($results);

		$request = Request::answerInlineQuery($data);

		TelegramLog::debug(var_dump($data, $request));

		return $request;
	}
} 