<?php
namespace HomeCEU\Tests;

use DateTime;
use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Entity\Template;

class TestCase extends \PHPUnit\Framework\TestCase {

  protected static $APP_ROOT;

  public static function setUpBeforeClass(): void {
    parent::setUpBeforeClass();
    self::$APP_ROOT = realpath(__DIR__.'/../');
    if (!defined('APP_ROOT')) {
      define('APP_ROOT', self::$APP_ROOT);
    }
  }

  public static function faker() {
    return Faker::generator();
  }

  protected function fakeTemplateArray($docType = null, $key = null) {
    return $this->templateArray([
        'docType' => $docType ?: __FUNCTION__,
        'templateKey' => $key ?: uniqid()
    ]);
  }

  protected function newTemplate(array $overwrite=[]): Template {
    return Template::fromState($this->templateArray($overwrite));
  }

  protected function templateArray(array $overwrite=[]) {
    $base = [
        'templateId' => self::faker()->uuid,
        'docType' => __FUNCTION__.'-'.time(),
        'templateKey' => uniqid(__FUNCTION__),
        'name' => self::faker()->monthName,
        'author' => self::faker()->name,
        'createdAt' => new DateTime('yesterday'),
        'body' => 'hi {{name}}'
    ];
    return array_merge($base, $overwrite);
  }

  protected function newDocData(array $overwrite=[]): DocData {
    return DocData::fromState($this->docDataArray($overwrite));
  }

  protected function docDataArray(array $overwrite=[]) {
    $base = [
        'dataId'     => self::faker()->uuid,
        'docType'    => uniqid(__FUNCTION__),
        'dataKey'    => self::faker()->colorName,
        'createdAt'  => $this->createdAtDateTime(),
        'data'       => [
            "firstName" => self::faker()->firstName,
            "lastName"  => self::faker()->lastName,
            "email"     => self::faker()->email
        ]
    ];
    return array_merge($base, $overwrite);
  }

  protected function createdAtDateTime(): DateTime {
    static $date = '2000-01-01';
    $dt = new DateTime($date.' + 1day');
    $date = $dt->format('Y-m-d');
    return $dt;
  }
}
