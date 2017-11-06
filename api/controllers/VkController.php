<?php

/**
 * @RoutePrefix('/vk')
 */
class VkController extends Controller
{

	/**
	 * @Post('/callback')
	 */
	public function callbackAction()
	{
		(new VkTask())->syncAction();
	}

	public function notFoundAction()
	{
		throw new \Core\Exception\NotFound();
	}

	/**
	 * @Get('/deploy')
	 */
	public function deployAction()
	{
		return exec('git pull && php composer.phar update && ./vendor/phalcon/devtools/phalcon.php migration run --config=./api/config/config.ini');
	}

}