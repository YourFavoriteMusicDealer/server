<?php

use Phalcon\Cli\Task;

class VkTask extends Task
{
	private $_id3;

	private $_mark = [
		'(#NR)',
		'[#NR]',
		'(NR)',
		'[NR]',
		'(Новый Рэп)',
		'[Новый Рэп]'
	];

	private $_domains = [
		'jonkofee_music'
	];

	public function syncAction()
	{
		$this->_id3 = new getID3;
		$this->_id3->setOption(array('encoding'=>'UTF-8'));

		foreach ($this->_domains as $domain) {
			$wall = $this->_getWall($domain);

			foreach ($wall as $post) {
				//Пропускаем рекламные посты
				if ($post->marked_as_ads) continue;

				//Если уже обрабатывали такой пост - далее
				if (Post::findFirst("owner_id = {$post->owner_id} AND post_id = {$post->id}")) continue;

				$tracks = $this->_getTracksByPost($post);

				foreach ($tracks as $track) {
					try {
						$this->_normalizeMetadata($track);

						$oldUrl = $track->url;

						$this->_fileWithMetatag($track);

						$response = $this->_sendTrack($track);
					} catch (Exception $e) {
						continue;
					}

					if ($response->isOk()) {
						$result = $response->getResult();

						$track_id = $result->getAudio()->getFileId();
						$message_id = $result->getMessageId();

						$newTrackInDB = new Track([
							'artist' => $track->artist,
							'title' => $track->title,
							'img' => $track->album->thumb->photo_600,
							'url' => $oldUrl,
							'telegram_file_id' => $track_id,
							'telegram_message_id' => $message_id,
							'hash' => $track->hash
						]);

						$newTrackInDB->save();
					}
				}

				//Запоминаем, что этот пост обработали
				$newPostInDB = new Post([
					'owner_id' => $post->owner_id,
					'post_id' => $post->id
				]);

				$newPostInDB->save();
			}
		}
	}

	private function _getWall($domain)
	{
		$count = 50;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.vk.com/method/wall.get?domain=$domain&count=$count&filter=owner&access_token=630494155c417d7382ffe1fc428faa0e344b3763a97f921694f65b91c1e4468ff261900562c8bf4dfbb32&v=5.69",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return json_decode($response)->response->items;
		}
	}

	private function _getTracksByPost($post)
	{
		$result = [];

		$attachments = $post->attachments;

		foreach ($attachments as $attachment) {
			if ($attachment->type == 'audio') $result[] = $attachment->audio;
		}

		return $result;
	}

	private function _fileWithMetatag(&$track)
	{
		$track->url = $this->_copyToLocalPath($track);

		$this->_setMetatag($track);
	}

	private function _copyToLocalPath(&$track)
	{
		$url = $track->url;

		$localtempfilename = '';

		//Скачиваем в temp
		if ($fp_remote = fopen($url, 'rb')) {
			$localtempfilename = sys_get_temp_dir() . "/{$track->artist} - {$track->title}.mp3";

			$track->localPath = $localtempfilename;

			if ($fp_local = fopen($localtempfilename, 'wb')) {
				while ($buffer = fread($fp_remote, 8192)) {
					fwrite($fp_local, $buffer);
				}

				fclose($fp_local);
			}
			fclose($fp_remote);
		}

		$track->hash = hash_file('md5', $track->localPath);

//		if (Track::findFirst("hash = '{$track->hash}'")) {
//			unlink($track->localPath);
//			throw new Exception('Такая песня уже есть');
//		}

		return $localtempfilename;
	}

	private function _setMetatag($track)
	{
		$tagwriter = new getid3_writetags;
		$tagwriter->filename       = $track->url;
		$tagwriter->tagformats     = ['id3v1', 'id3v2.3'];
		$tagwriter->remove_other_tags = true;
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding   = 'UTF-8';

		$tagData = [
			'album' => [$track->album->title],
			'artist' => [$track->artist],
			'copyright' => ['jonkofee'],
			'genre' => [$track->track_genre_id],
			'title' => [$track->title]
		];

		if (isset($track->album->thumb)) {
			//Add artcover
			$img = file_get_contents($track->album->thumb->photo_600);
			$exif_imagetype = exif_imagetype($track->album->thumb->photo_600);

			$tagData['attached_picture'][] = [
				'data' => $img,
				'picturetypeid' => 'jpg',
				'description' => "{$track->artist} - {$track->title}",
				'mime' => image_type_to_mime_type($exif_imagetype)
			];
		}

		$tagwriter->tag_data = $tagData;

		$tagwriter->WriteTags();
	}

	private function _sendTrack($track)
	{
		$telegramTrackStream = \Longman\TelegramBot\Request::encodeFile($track->url);

		unlink($track->localPath);

		$inline_keyboard = new \Longman\TelegramBot\Entities\InlineKeyboard([
			['text' => '👍🏻', 'callback_data' => 'like'],
			['text' => '👎🏻', 'callback_data' => 'dislike'],
		]);

		$data = [
			'chat_id' => '@jonkofee_music',
			'audio'  => $telegramTrackStream,
			'reply_markup' => $inline_keyboard
		];

		return \Longman\TelegramBot\Request::sendAudio($data);

	}

	private function _normalizeMetadata(&$track)
	{
//		if (!isset($track->album->thumb) || !$track->title || !$track->artist) throw new Exception();
		$track->title = trim(str_replace($this->_mark, '', $track->title));
		$track->artist = trim(str_replace($this->_mark, '', $track->artist));

		if (isset($track->album) && isset($track->album->thumb)) $track->album->title = trim(str_replace($this->_mark, '', $track->album->title));
	}
}