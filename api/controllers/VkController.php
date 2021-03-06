<?php

/**
 * @RoutePrefix('/vk')
 */
class VkController extends Controller
{

	private $_id3;

	private $_mark = [
		'(#NR)',
		'[#NR]',
		'(NR)',
		'[NR]',
		'(Новый Рэп)',
		'[Новый Рэп]',
		'/'
	];

	/**
	 * @Post('/callback')
	 */
	public function callbackAction()
	{
    $input = $this->_getInput();

    $postId = $input->object->id;
    $fromId = $input->object->from_id;

		$postInDB = Post::findFirst("owner_id = $fromId AND post_id = $postId");

		if ($postInDB) {
      //Если уже обрабатывали или в обработке такой пост - далее
      switch ($postInDB->status) {
        case Post::STATUS_COMPLETE:
          return 'ok';
        case Post::STATUS_IN_PROCESS:
          return 'in process';
      }
    } else {
      $postInDB = new Post([
        'owner_id' => $fromId,
        'post_id' => $postId,
        'status'  => Post::STATUS_IN_PROCESS
      ]);

      $postInDB->save();
    }

    $this->_initId3();

    try {
      $post = $this->_getPost($fromId, $postId);

      //Пропускаем рекламные посты
      if ($post->marked_as_ads) return;

      $tracks = $this->_getTracksByPost($post);

      foreach ($tracks as $track) {
        try {
          $this->_normalizeMetadata($track);
          $this->_fileWithMetatag($track);

          $response = $this->_sendTrack($track);
        } catch (Exception $e) {
          continue;
        }

        if ($response->isOk()) {
          $result = $response->getResult();

          $track_id = $result->getAudio()->getFileId();
          $message_id = $result->getMessageId();

          $data = [
            'artist' => $track->artist,
            'title' => $track->title,
            'telegram_file_id' => $track_id,
            'telegram_message_id' => $message_id,
            'hash' => $track->hash
          ];

          if (isset($track->album->thumb->photo_600)) {
            $data['img'] = $track->album->thumb->photo_600;
          }

          $newTrackInDB = new Track($data);

          $newTrackInDB->save();
        }
      }

      //Запоминаем, что этот пост обработали
      $postInDB->status = Post::STATUS_COMPLETE;

      $postInDB->save();

      return 'ok';
    } catch (Exception $e) {
		  $postInDB->status = Post::STATUS_ERROR;

		  $postInDB->save();

		  return 'error';
    }
	}

	private function _getPost($fromId, $postId)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.vk.com/method/wall.getById?posts={$fromId}_{$postId}&access_token=630494155c417d7382ffe1fc428faa0e344b3763a97f921694f65b91c1e4468ff261900562c8bf4dfbb32&v=5.69",
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
      $response = json_decode($response)->response;

      if (!$response) throw new Exception('No find post in vk');
			return $response[0];
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
		  $tempDir = sys_get_temp_dir();

		  if (substr($tempDir, -1) != '/') {
		    $tempDir .= '/';
      }

			$localtempfilename = $tempDir . "{$track->artist} - {$track->title}.mp3";
		  
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

		if (Track::findFirstByHash($track->hash)) {
			unlink($track->localPath);
			throw new Exception('Такая песня уже есть');
		}

		return $localtempfilename;
	}

	private function _setMetatag(&$track)
	{
		$oldTagData = $this->_id3->analyze($track->url);
		$track->duration = (int) $oldTagData['playtime_seconds'];

		if (isset($oldTagData['tags'])) {
      $tagData = isset($oldTagData['tags']['id3v2']) ? $oldTagData['tags']['id3v2'] : $oldTagData['tags']['id3v1'];
    } else {
		  $tagData = [];
    }

    if (isset($oldTagData['comments']) && isset($oldTagData['comments']['picture'])) {
		  $tagData['attached_picture'] = $oldTagData['comments']['picture'];
    }

		$tagwriter = new getid3_writetags;
		$tagwriter->filename       = $track->url;
		$tagwriter->tagformats     = ['id3v1', 'id3v2.3'];
		$tagwriter->remove_other_tags = true;
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding   = 'UTF-8';

		$tagData['artist'] = [$track->artist];
		$tagData['copyright_message'] = ['https://t.me/jonkofee_music'];
		$tagData['comment'] = ['https://t.me/jonkofee_music'];
		$tagData['content_group_description'] = ['https://t.me/jonkofee_music'];
		$tagData['title'] = [$track->title];

		if (isset($track->album->title)) $tagData['album'] = [$track->album->title];
		if (isset($track->track_genre_id)) $tagData['genre'] = [$track->track_genre_id];

		if (isset($track->album->thumb)) {
			//Add artcover
			$img = file_get_contents($track->album->thumb->photo_600);

			$tagData['attached_picture'][0] = [
				'data' => $img,
				'picturetypeid' => 'jpg',
				'description' => "{$track->artist} - {$track->title}"
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
			'chat_id' => -1001149842026,
			'audio'  => $telegramTrackStream,
			'reply_markup' => $inline_keyboard,
			'performer' => $track->artist,
			'title' => $track->title,
			'duration' => $track->duration
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

	private function _initId3()
	{
		$this->_id3 = new getID3;
		$this->_id3->setOption(array('encoding'=>'UTF-8'));
	}

	private function _getInput()
	{
		return json_decode(file_get_contents('php://input'));
	}

}