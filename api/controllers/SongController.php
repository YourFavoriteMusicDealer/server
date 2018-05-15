<?php

/**
 * @RoutePrefix('/song')
 */
class SongController extends Controller
{

	/**
	 * @Get('/{id}')
	 */
	public function getInfoAction($id)
	{
		$track = Track::findFirst($id);

		if (!$track) throw new Exception('Такая песня отсутствует', 404);

		$arrTrack = $track->toArray([
			'artist',
			'title',
			'img'
		]);

		$arrTrack['audio'] = "http://{$this->request->getServerName()}:{$this->request->getPort()}/song/{$id}/file";

		return $arrTrack;
	}

	/**
	 * @Get('/{id}/file')
	 */
	public function getFileAction($id)
	{
		$track = Track::findFirst($id);

		if (!$track) throw new Exception('Такая песня отсутствует', 404);

		$telegramUrl = $track->getAudioUrl();

		if ($fp_remote = fopen($telegramUrl, 'rb')) {
			header("Content-Type: audio/mpeg");

			fpassthru($fp_remote);
			fclose($fp_remote);
			exit;
		} else {
			throw new Exception('Не получается загрузить файл');
		}
	}

}