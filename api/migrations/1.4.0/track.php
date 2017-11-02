<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class TrackMigration_140 extends Migration
{
	private $_tableName = 'track';

	public function up()
	{
		self::$_connection->modifyColumn(
			$this->_tableName,
			'public',
			new Column(
				'img',
				[
					'type' => Column::TYPE_INTEGER
				]
			),
			new Column(
				'img',
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
				'img',
				[
					'type' => Column::TYPE_INTEGER
				]
			),
			new Column(
				'img',
				[
					'type' => Column::TYPE_INTEGER,
					'notNull' => true
				]
			)
		);
	}

}
