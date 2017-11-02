<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class UserMigration_170 extends Migration
{
	private $_tableName = 'user';

	public function up()
	{
		self::$_connection->dropTable(
			$this->_tableName,
			'public'
		);

		$this->morphTable($this->_tableName, [
				'columns' => [
					new Column(
						'id',
						[
							'type' => Column::TYPE_INTEGER,
							'notNull' => true,
							'autoIncrement' => true,
							'first' => true
						]
					),
					new Column(
						'telegram_id',
						[
							'type' => Column::TYPE_INTEGER,
							'notNull' => true,
						]
					),
					new Column(
						'username',
						[
							'type' => Column::TYPE_TEXT
						]
					),
				],
				'indexes' => [
					new Index('user_pkey', ['id'], 'PRIMARY KEY'),
					new Index('user_username_key', ['username'], ''),
					new Index('user_telegram_id', ['telegram_id'], '')
				],
			]
		);
	}

	public function down()
	{
//		self::$_connection->dropColumn($this->_tableName, 'public', 'user_id');

//		self::$_connection->dropIndex($this->_tableName, 'public', 'user_user_id');
	}

}
