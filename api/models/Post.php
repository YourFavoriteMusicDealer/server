<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class Post extends Model
{
	protected $_tableName = 'post';

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    public $owner_id;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", nullable=false)
	 */
	public $post_id;

}
