<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class UserMigration_160 extends Migration
{
	private $_tableName = 'user';

	public function up()
	{
		self::$_connection->addColumn(
			$this->_tableName,
			'public',
			new Column(
				'user_id',
				[
					'type' => Column::TYPE_INTEGER,
					'notNull' => true,
					'default' => 0,
					'after' => 'id'
				]
			));

		self::$_connection->addIndex(
			$this->_tableName,
			'public',
			new Index('user_user_id', ['user_id'])
		);
	}

	public function down()
	{
		self::$_connection->dropColumn($this->_tableName, 'public', 'user_id');

		self::$_connection->dropIndex($this->_tableName, 'public', 'user_user_id');
	}

}
