<?php

/**
 * @RoutePrefix('/song')
 */
class SongController extends Controller
{

	/**
	 * @Get('/{telegram_message_id}')
	 */
	public function getInfoAction($telegram_message_id)
	{
		$track = Track::findFirstByTelegramMessageId($telegram_message_id);

		if (!$track) throw new Exception('Такая песня отсутствует', 404);

		$arrTrack = $track->toArray([
			'artist',
			'title',
			'img'
		]);

		$arrTrack['url'] = "http://{$this->request->getServerName()}/song/{$telegram_message_id}/stream";

		return $arrTrack;
	}

	/**
	 * @Get('/{telegram_message_id}/stream')
	 */
	public function getFileAction($telegram_message_id)
	{
		$track = Track::findFirstByTelegramMessageId($telegram_message_id);

		if (!$track) throw new Exception('Такая песня отсутствует', 404);

		$telegramUrl = $track->getAudioUrl();

		if ($fp_remote = fopen($telegramUrl, 'rb')) {
			header("Content-Type: audio/mpeg");
			header("Content-Disposition: inline; filename=\"{$track->artist} - {$track->title}.mp3\"");

			fpassthru($fp_remote);
			fclose($fp_remote);
			exit;
		} else {
			throw new Exception('Не получается загрузить файл');
		}
	}

}