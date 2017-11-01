<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class RatingMigration_120 extends Migration
{
	private $_tableName = 'rating';

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
                        'user_id',
                        [
                            'type' => Column::TYPE_INTEGER,
	                        'notNull' => true,
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
		                'like',
		                [
			                'type' => Column::TYPE_BOOLEAN,
			                'notNull' => true,
//			                'default' => false,
		                ]
	                ),
	                new Column(
		                'dislike',
		                [
			                'type' => Column::TYPE_BOOLEAN,
			                'notNull' => true,
//			                'default' => false,
		                ]
	                ),
                ],
                'indexes' => [
                    new Index('rating_pkey', ['id'], 'PRIMARY KEY'),
                    new Index('rating_user_id', ['user_id']),
	                new Index('rating_track_id', ['track_id']),
	                new Index('rating_like', ['like']),
	                new Index('rating_dislike', ['dislike'])
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
