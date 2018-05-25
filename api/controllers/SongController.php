<?php

/**
 * @RoutePrefix('/song')
 */
class SongController extends Controller
{

	/**
	 * @Get('/{telegramMessageId}')
	 */
	public function getInfoAction($telegramMessageId)
	{
		$track = Track::findFirstByTelegramMessageId($telegramMessageId);

		if (!$track) throw new Exception('Такая песня отсутствует', 404);

		$arrTrack = $track->toArray([
			'artist',
			'title',
			'img'
		]);

		$arrTrack['url'] = "http://{$this->request->getServerName()}/song/{$telegramMessageId}/stream";

		return $arrTrack;
	}

	/**
	 * @Get('/{telegramMessageId}/stream')
	 */
	public function getFileAction($telegramMessageId)
	{
		$track = Track::findFirstByTelegramMessageId($telegramMessageId);

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