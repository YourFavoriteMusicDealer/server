<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class User extends Model
{
	protected $_tableName = 'user';

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
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
