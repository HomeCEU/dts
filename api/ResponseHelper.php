<?php


namespace HomeCEU\DTS\Api;


use HomeCEU\DTS\Entity\DocData;
use HomeCEU\DTS\Entity\Partial;
use HomeCEU\DTS\Entity\Template;

class ResponseHelper {
  private const ROUTE = '/api/v1';

  public static function templateDetailModel(Template $t): array {
    return [
        'templateId' => $t->templateId,
        'templateKey' => $t->templateKey,
        'docType' => $t->docType,
        'author' => $t->author,
        "createdAt" => $t->createdAt,
        'bodyUri' => self::ROUTE . "/template/{$t->templateId}"
    ];
  }

  public static function partialDetailModel(Partial $partial): array {
    return [
        'id' => $partial->id,
        'name' => $partial->name,
        'docType' => $partial->docType,
        'author' => $partial->author,
        'metadata' => $partial->metadata,
        "createdAt" => $partial->createdAt,
        'bodyUri' => self::ROUTE . "/partial/{$partial->id}",
    ];
  }

  public static function docDataDetailModel(DocData $d): array {
    return [
        'id' => $d->id,
        'key' => $d->key,
        'docType' => $d->docType,
        "createdAt" => $d->createdAt,
        "link" => self::ROUTE . "/docdata/{$d->id}"
    ];
  }

  public static function docDataModel(DocData $d) {
    return [
        'id' => $d->id,
        'key' => $d->key,
        'docType' => $d->docType,
        "createdAt" => $d->createdAt,
        "data" => $d->data
    ];
  }
}
