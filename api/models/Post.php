<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class Post extends Model
{
  const STATUS_IN_PROCESS  = 1;
  const STATUS_COMPLETE = 2;
  const STATUS_ERROR    = 0;

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

  /**
   *
   * @var integer
   * @Column(type="integer", nullable=false)
   */
  public $status;

  public function isCompleted()
  {
    return $this->status == self::STATUS_COMPLETE;
  }

  public function isInProcess()
  {
    return $this->status == self::STATUS_IN_PROCESS;
  }

  public function isError()
  {
    return $this->status == self::STATUS_ERROR;
  }

}
