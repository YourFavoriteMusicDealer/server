<?php

/**
 * @RoutePrefix('/webhook')
 */
class WebhookController extends Controller
{

	public function indexAction()
	{
		return $this->bot->handle();
	}

	/**
	 * @Get('/set')
	 */
	public function setAction()
	{
		$result = $this->bot->setWebhook("https://{$_SERVER['HTTP_HOST']}/webhook");

		if (!$result->isOk()) throw new \Core\Exception\ServerError($result);

		return "https://{$_SERVER['HTTP_HOST']}/webhook";
	}

	/**
	 * @Get('/unset')
	 */
	public function unsetAction()
	{
		$result = $this->bot->deleteWebhook();

		if (!$result->isOk()) throw new \Core\Exception\ServerError($result);

		return $result->getDescription();
	}

	/**
	 * @Get('/info')
	 */
	public function infoAction()
	{
		return \Longman\TelegramBot\Request::getWebhookInfo();
	}
}