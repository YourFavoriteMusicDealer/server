<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class TrackMigration_190 extends Migration
{
	private $_tableName = 'track';

	public function up()
	{
		self::$_connection->dropColumn(
			$this->_tableName,
			'public',
			'url'
		);
	}

	public function down()
	{

	}

}
