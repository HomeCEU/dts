<?php

use HomeCEU\DTS\Api;

return [
  # DocType
    new Api\Route('get', '/doctype', Api\Template\ListDocTypes::class),
  # Template
    new Api\Route('post', '/template', Api\Template\AddTemplate::class),
    new Api\Route('get', '/template', Api\Template\ListTemplates::class),
    new Api\Route('get', '/template/{templateId}', Api\Template\GetTemplate::class),
    new Api\Route('get', '/template/{docType}/{templateKey}', Api\Template\GetTemplate::class),
    new Api\Route('get', '/template/{docType}/{templateKey}/history', Api\Template\ListVersions::class),
  # DocData
    new Api\Route('post', '/docdata', Api\DocData\DocDataAdd::class),
    new Api\Route('get', '/docdata/{docType}/{dataKey}/history', Api\DocData\ListVersions::class),
    new Api\Route('get', '/docdata/{dataId}', Api\DocData\GetDocDataById::class),
    new Api\Route('get', '/docdata/{docType}/{dataKey}', API\DocData\GetDocDataByKey::class),
  # Render
    new Api\Route('get', '/render', Api\Render\Render::class),
    new Api\Route('get', '/render/{docType}/{templateKey}/{dataKey}', Api\Render\Render::class),
    new Api\Route('post', '/hotrender', Api\Render\AddHotRender::class),
    new Api\Route('get', '/hotrender/{requestId}', Api\Render\HotRender::class),
  # API
    new Api\Route('get', '/status', Api\Status::class),
];

