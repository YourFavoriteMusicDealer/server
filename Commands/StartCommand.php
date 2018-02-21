<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $response = Request::sendMessage([
          'chat_id'               => $chat_id,
          'parse_mode'            => 'Markdown',
          'text'                  => 'Привет, *' . $message->getFrom()->getFirstName() . '*!' . PHP_EOL .
                                     'Чутка музыки не хочешь?😎',
          'disable_notification'  => true,
          'reply_markup'          => new Keyboard([
            'keyboard' => [
              ['text' => '⏯ Плейлист'],
              ['text' => '🔝10 месяца']
            ],
            'resize_keyboard' => true
          ])
        ]);

        if ($message->getText(true) === 'myplaylist') {
	        $this->telegram->executeCommand('myplaylist');
        }

        return $response;
    }
}
