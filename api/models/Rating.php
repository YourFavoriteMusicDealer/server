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
	public $lik;

	/**
	 *
	 * @var boolean
	 * @Column(type="boolean", nullable=false)
	 */
	public $dislik;

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

		$this->belongsTo('user_id', '\User', 'id', ['alias' => 'User']);

		$this->belongsTo('track_id', '\Track', 'id', ['alias' => 'Track']);
	}
	
	public function beforeSave()
	{
		$this->datetime_create = 'now';
	}

}
