<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class RatingMigration_200 extends Migration
{
	private $_tableName = 'rating';

	public function up()
	{
		self::$_connection->addColumn(
			$this->_tableName,
			'public',
			new Column(
				'datetime_create',
				[
					'type' => Column::TYPE_TIMESTAMP,
					'default' => "CURRENT_TIMESTAMP",
					'notNull' => true,
				]
			)
		);
	}

	public function down()
	{

	}

}
