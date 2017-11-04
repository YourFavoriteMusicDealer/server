<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class RatingMigration_220 extends Migration
{
	private $_tableName = 'rating';

	public function up()
	{
		self::$_connection->addColumn(
			$this->_tableName,
			'public',
			new Column(
				'username',
				[
					'type' => Column::TYPE_TEXT
				]
			)
		);
	}

	public function down()
	{
		self::$_connection->dropColumn(
			$this->_tableName,
			'public',
			'username'
		);
	}

}
