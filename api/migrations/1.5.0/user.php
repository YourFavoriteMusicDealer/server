<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class UserMigration_150 extends Migration
{
	private $_tableName = 'user';

	public function up()
	{
		self::$_connection->modifyColumn(
			$this->_tableName,
			'public',
			new Column(
				'username',
				[
					'type' => Column::TYPE_INTEGER
				]
			),
			new Column(
				'username',
				[
					'type' => Column::TYPE_INTEGER,
					'notNull' => true
				]
			)
		);
	}

	public function down()
	{
		self::$_connection->modifyColumn(
			$this->_tableName,
			'public',
			new Column(
				'username',
				[
					'type' => Column::TYPE_INTEGER
				]
			),
			new Column(
				'username',
				[
					'type' => Column::TYPE_INTEGER,
					'notNull' => true
				]
			)
		);
	}

}
