<?php
namespace Core;

use Deploykit\Telegraph\Client;
use Deploykit\Telegraph\Entities\Account;

class Telegraph
{

  /**
   * @var Client
   */
  private $_client;

  /**
   * @var Account
   */
  public $_account;

  use \Core\Singleton;

  public function __construct($config)
  {
    $this->_client = new Client();
    $this->_account = new Account($config->toArray());
  }

  public function editAccountInfo($shortName = '', $authorName = '', $authorUrl = '')
  {
    return $this->_client->editAccountInfo($this->_account, $shortName, $authorName, $authorUrl);
  }

  public function getAccountInfo($fields = ['short_name', 'author_name', 'author_url'])
  {
    return $this->_client->getAccountInfo($this->_account, $fields);
  }

  public function createPage($title, $content, $returnContent = false)
  {
    return $this->_client->createPage($this->_account, $title, $content, $this->_account->author_name, $this->_account->author_url, $returnContent);
  }

  public function getPage($path)
  {
    return $this->_client->getPage($path, true);
  }

  public function editPage($path, $title, $content, $returnContent = false)
  {
    return $this->_client->editPage($this->_account, $path, $title, $content, $this->_account->author_name, $this->_account->author_url, $returnContent);
  }
}