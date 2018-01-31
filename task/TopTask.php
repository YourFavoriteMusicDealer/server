<?php

use Phalcon\Cli\Task;
use Phalcon\Mvc\Model\Resultset\Simple;

class TopTask extends Task
{
  private $_rusMonth = [
    "Январь",
    "Февраль",
    "Март",
    "Апрель",
    "Май",
    "Июнь",
    "Июль",
    "Август",
    "Сентябрь",
    "Октябрь",
    "Ноябрь",
    "Декабрь"
  ];

  public function monthAction($params)
  {
    $count = isset($params[0]) ? (int) $params[0] : 10;

    $tracks = $this->_getTopTrackByLastMonth($count);

    /** @var \Core\Telegraph $telegraph */
    $telegraph = $this->getDI()->get('telegraph');

    $content = $this->_constructContent($tracks);
//    $title = "Топ $count за " . strtolower($this->_rusMonth[date( "n" ) - 1]) . ' ' . date( "Y" );
    $title = 'test';

    $post = $telegraph->createPage($title, $content);
    $postUrl = $post->url;

    $this->_editMainPage($postUrl);

    return \Longman\TelegramBot\Request::sendMessage([
      'chat_id'               => 325275444,
      'parse_mode'            => 'Markdown',
      'text'                  => '[Топ ' . $count . ' за ' . mb_strtolower($this->_rusMonth[date( "n" ) - 1]) . "]($postUrl)"
    ]);
  }

  private function _getTopTrackByLastMonth($count)
  {
    $sqlQuery = "SELECT track.*, COALESCE(SUM(lik::integer), 0) as likes, COALESCE(SUM(dislik::integer), 0) as dislikes, COALESCE(SUM(lik::integer) - SUM(dislik::integer), 0) as rating FROM track
		LEFT JOIN rating ON track.id = rating.track_id
		WHERE date_trunc('day', LOCALTIMESTAMP) - date_trunc('day', track.datetime_create) <= interval '30 days'
		GROUP BY track.id
		ORDER BY rating desc, likes desc, datetime_create desc LIMIT $count;";


    $arr =  (new Simple(
      null,
      null,
      (new \Track())->getReadConnection()->query($sqlQuery)
    ))->toArray();

    if (!$arr) {
      throw new Exception('Нет песен за последний месяц');
    }

    return $arr;
  }

  private function _constructContent($tracks)
  {
    $olChildren = [];

    foreach ($tracks as $track) {
      $olChildren[] = [
        'tag' => 'li',
        'children' => [
          [
            'tag' => 'a',
            'attrs' => [
              'href' => 'https://t.me/jonkofee_music/' . $track['telegram_message_id']
            ],
            'children' => [
              $track['artist'] . ' - ' . $track['title']
            ]
          ]
        ]
      ];
    }


    return [
      [
        'tag' => 'ol',
        'children' => $olChildren
      ]
    ];
  }

  private function _editMainPage($newUrl)
  {
    /** @var \Core\Telegraph $telegraph */
    $telegraph = $this->getDI()->get('telegraph');

    $mainPagePath = $this->getDI()->get('config')->telegraph->main_page_path;

    $mainPage = $telegraph->getPage($mainPagePath);

    $content = $mainPage->content;

    array_unshift($content[0]['children'], [
      'tag' => 'li',
      'children' => [
        [
          'tag' => 'a',
          'attrs' => [
            'href' => $newUrl
          ],
          'children' => [
            $this->_rusMonth[date( "n" ) - 1] . ' ' . date( "Y" )
          ]
        ]
      ]
    ]);

    $telegraph->editPage($mainPagePath, $mainPage->title, $content);
  }
}