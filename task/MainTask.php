<?php

use Phalcon\Cli\Task;

class MainTask extends Task
{
	public function mainAction()
	{
		echo 'Это задача по умолчанию и действие по умолчанию' . PHP_EOL;
	}

	/**
	 * @param array $params
	 */
	public function testAction(array $params)
	{
		echo sprintf('hello %s', $params[0]);

		echo PHP_EOL;

		echo sprintf('best regards, %s', $params[1]);

		echo PHP_EOL;
	}
}