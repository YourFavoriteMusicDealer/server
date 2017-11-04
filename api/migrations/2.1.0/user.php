<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class UserMigration_210 extends Migration
{
	private $_tableName = 'user';

	public function up()
	{
		self::$_connection->dropTable(
			$this->_tableName,
			'public'
		);
	}

	public function down()
	{

	}

}
