<?php declare(strict_types=1);

use HomeCEU\DTS\Api;

return [
  # DocType
    new Api\Route(['get'], '/doctype', Api\Template\ListDocTypes::class),
  # Template
    new Api\Route(['post'], '/template', Api\Template\AddTemplate::class),
    new Api\Route(['get'], '/template', Api\Template\ListTemplates::class),
    new Api\Route(['get'], '/template/{templateId}', Api\Template\GetTemplate::class),
    new Api\Route(['get'], '/template/{docType}/{templateKey}', Api\Template\GetTemplate::class),
    new Api\Route(['get'], '/template/{docType}/{templateKey}/history', Api\Template\ListVersions::class),
  # Partial
    new Api\Route(['post'], '/partial', Api\Partial\AddPartial::class),
    new Api\Route(['get'], '/partial', Api\Partial\ListPartials::class),
    new Api\Route(['get'], '/partial/{partialId}', Api\Partial\GetPartial::class),
  # DocData
    new Api\Route(['post'], '/docdata', Api\DocData\DocDataAdd::class),
    new Api\Route(['get'], '/docdata/{docType}/{key}/history', Api\DocData\ListVersions::class),
    new Api\Route(['get'], '/docdata/{id}', Api\DocData\GetDocDataById::class),
    new Api\Route(['get'], '/docdata/{docType}/{key}', API\DocData\GetDocDataByKey::class),
  # Render
    new Api\Route(['get'], '/render', Api\Render\Render::class),
    new Api\Route(['get'], '/render/{docType}/{templateKey}/{key}', Api\Render\Render::class),
    new Api\Route(['post'], '/hotrender', Api\Render\AddHotRender::class),
    new Api\Route(['get'], '/hotrender/{requestId}', Api\Render\HotRender::class),
  # API
    new Api\Route(['get'], '/status', Api\Status::class),
];
