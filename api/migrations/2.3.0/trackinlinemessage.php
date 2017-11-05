<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class TrackinlinemessageMigration_230 extends Migration
{
	private $_tableName = 'track_inlinemessage';

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
                        'track_id',
                        [
                            'type' => Column::TYPE_INTEGER,
	                        'notNull' => true,
                        ]
                    ),
	                new Column(
		                'inline_message_id',
		                [
			                'type' => Column::TYPE_INTEGER,
			                'notNull' => true,
		                ]
	                )
                ],
                'indexes' => [
                    new Index('track_inlinemessage_pkey', ['id'], 'PRIMARY KEY'),
	                new Index('track_inlinemessage_track_id', ['track_id']),
	                new Index('track_inlinemessage_lik', ['inline_message_id']),
                ],
		        'references' => [
			        new \Phalcon\Db\Reference(

				        'track_inlinemessage_track_id',
				        [
					        'referencedSchema'  => 'public',
					        'referencedTable'   => 'track',
					        'columns'           => ['track_id'],
					        'referencedColumns' => ['id'],
				        ]
			        ),
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
