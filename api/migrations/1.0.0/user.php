<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class UserMigration_100 extends Migration
{
	private $_tableName = 'user';

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
                        'username',
                        [
                            'type' => Column::TYPE_TEXT,
	                        'notNull' => true,
                        ]
                    ),
                ],
                'indexes' => [
                    new Index('user_pkey', ['id'], 'PRIMARY KEY'),
                    new Index('user_username_key', ['username'], '')
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
