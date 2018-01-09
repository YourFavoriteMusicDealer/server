<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class PostMigration_240 extends Migration
{
	private $_tableName = 'post';

	public function up()
	{
		self::$_connection->addColumn(
			$this->_tableName,
			'public',
			new Column(
				'status',
				[
					'type' => Column::TYPE_INTEGER
				]
			)
		);
	}

	public function down()
	{
		self::$_connection->dropColumn(
			$this->_tableName,
			'public',
			'status'
		);
	}

}
