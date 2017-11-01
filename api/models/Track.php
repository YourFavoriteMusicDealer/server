<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

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
	 * Initialize method for model.
	 */
	public function initialize()
	{
		parent::initialize();

		$this->hasMany('id', 'Rating', 'track_id', ['alias' => 'Rating']);
	}

}
