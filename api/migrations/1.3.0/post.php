<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class PostMigration_130 extends Migration
{
	private $_tableName = 'post';

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
                        'owner_id',
                        [
                            'type' => Column::TYPE_INTEGER,
	                        'notNull' => true,
                        ]
                    ),
	                new Column(
		                'post_id',
		                [
			                'type' => Column::TYPE_INTEGER,
			                'notNull' => true,
		                ]
	                ),

                ],
                'indexes' => [
                    new Index('post_pkey', ['id'], 'PRIMARY KEY'),
                    new Index('post_owner_id', ['owner_id']),
	                new Index('post_post_id', ['post_id']),
                ],
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
