<?php


namespace HomeCEU\DTS\Repository;


use HomeCEU\DTS\Persistence;

class RepoHelper {
  /** @var Persistence */
  private $persistence;

  public function __construct(Persistence $persistence) {
    $this->persistence = $persistence;
  }

  public function findNewest(array $filter, array $cols=['*']) {
    $rows = $this->persistence->find($filter, $cols);
    if (count($rows) === 0) {
      throw new RecordNotFoundException(sprintf(
          "no record was found matching (%s)",
          $this->encode($filter)
      ));
    }
    $sortedRows = $this->sortRowsByDate($rows);
    return array_pop($sortedRows);
  }

  private function encode($data) {
    $json = json_encode($data);
    return preg_replace('/["{}]/','',$json);
  }

  public function sortRowsByDate($rows) {
    // used for usort - https://www.php.net/manual/en/function.usort.php
    $f = function ($a, $b) {
      $adate = $a['createdAt'];
      $bdate = $b['createdAt'];
      return $adate < $bdate ? -1 : 1;
    };
    usort($rows, $f);
    return $rows;
  }

  public function extractUniqueProperty(array $rows, string $column): array {
    return array_values(array_unique(array_column($rows, $column)));
  }
}
