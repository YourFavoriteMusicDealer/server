<?php

use Phalcon\Http\Response;

/**
 * @RoutePrefix('/index')
 */
class IndexController extends Controller
{

	/**
	 * @Get('/index')
	 */
	public function indexAction()
	{
		$this->response->redirect('https://t.me/jonkofee_music', true);
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