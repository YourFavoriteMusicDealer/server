<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class TrackMigration_110 extends Migration
{
	private $_tableName = 'track';

    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
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
                        'artist',
                        [
                            'type' => Column::TYPE_TEXT,
	                        'notNull' => true,
                        ]
                    ),
	                new Column(
		                'title',
		                [
			                'type' => Column::TYPE_TEXT,
			                'notNull' => true,
		                ]
	                ),
	                new Column(
		                'img',
		                [
			                'type' => Column::TYPE_TEXT,
			                'notNull' => true,
		                ]
	                ),
	                new Column(
		                'telegram_file_id',
		                [
			                'type' => Column::TYPE_INTEGER,
			                'notNull' => true,
		                ]
	                ),
	                new Column(
		                'telegram_message_id',
		                [
			                'type' => Column::TYPE_INTEGER,
			                'notNull' => true,
		                ]
	                ),
                ],
                'indexes' => [
                    new Index('track_pkey', ['id'], 'PRIMARY KEY'),
                    new Index('track_artist', ['artist']),
	                new Index('track_title', ['title']),
	                new Index('track_img', ['img']),
	                new Index('track_telegram_file_id', ['telegram_file_id']),
	                new Index('track_telegram_message_id', ['telegram_message_id'])
                ]
            ]
        );
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        self::$_connection->dropTable($this->_tableName);
    }

}
