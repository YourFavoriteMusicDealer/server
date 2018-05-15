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

		$arrTrack = $track->toArray([
			'artist',
			'title',
			'img'
		]);

		$arrTrack['audio'] = $track->getAudioUrl();

		return $arrTrack;
	}

}