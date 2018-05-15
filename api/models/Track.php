<?php

use Phalcon\Validation;
use Longman\TelegramBot\Request;

class Track extends Model
{
	protected $_tableName = 'track';

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $artist;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	public $title;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	public $img;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	public $url;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	public $telegram_file_id;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", nullable=false)
	 */
	public $telegram_message_id;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	public $hash;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=false)
	 */
	public $datetime_create;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		parent::initialize();

		$this->hasMany('id', 'Rating', 'track_id', ['alias' => 'Rating']);
	}

	public function getLikes()
	{
		return $this->getRating('lik = true')->count();
	}

	public function getDislikes()
	{
		return $this->getRating('dislik = true')->count();
	}

	public function getAudioUrl()
	{
		$config = \ConfigIni::getInstance();
		
		$telegramFile = Request::getFile([
			'file_id' => $this->telegram_file_id
		])->getResult();

		return "https://api.telegram.org/file/bot" . $config->bot->token . "/" . $telegramFile->getFilePath();
	}

}
