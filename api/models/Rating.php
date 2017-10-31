<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class Rating extends Model
{
	protected $_tableName = 'rating';

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    public $user_id;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", nullable=false)
	 */
	public $track_id;

	/**
	 *
	 * @var boolean
	 * @Column(type="boolean", nullable=false)
	 */
	public $like;

	/**
	 *
	 * @var boolean
	 * @Column(type="boolean", nullable=false)
	 */
	public $dislike;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		parent::initialize();

		$this->belongsTo('user_id', '\User', 'id', ['alias' => 'User']);

		$this->belongsTo('track_id', '\Track', 'id', ['alias' => 'Track']);
	}

}
