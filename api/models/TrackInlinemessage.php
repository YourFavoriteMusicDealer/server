<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class TrackInlinemessage extends Model
{
	protected $_tableName = 'track_inlinemessage';

	/**
	 *
	 * @var integer
	 * @Column(type="integer", nullable=false)
	 */
	public $track_id;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", nullable=false)
	 */
	public $inline_message_id;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		parent::initialize();

		$this->belongsTo('track_id', '\Track', 'id', ['alias' => 'Track']);
	}

}
