<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class User extends Model
{
	protected $_tableName = 'user';

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    public $telegram_id;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=true)
	 */
	public $username;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		parent::initialize();

		$this->hasMany('id', 'Rating', 'user_id', ['alias' => 'Rating']);
	}

}
