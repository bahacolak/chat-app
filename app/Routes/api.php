<?php

use DI\Container;
use Slim\App;

return function (App $app, Container $container) {
    $container->set(PDO::class, function () {
        $dsn = 'sqlite:' . __DIR__ . '/../chatapp.sqlite';
        $username = null;
        $password = null;
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        return new PDO($dsn, $username, $password, $options);
    });

    $container->set(\App\Models\GroupModel::class, function (Container $container) {
        $pdo = $container->get(PDO::class);
        return new \App\Models\GroupModel($pdo);
    });

    $container->set(\App\Models\MessageModel::class, function (Container $container) {
        $pdo = $container->get(PDO::class);
        return new \App\Models\MessageModel($pdo);
    });

    $container->set(\App\Controllers\ApiController::class, function (Container $container) {
        $groupModel = $container->get(\App\Models\GroupModel::class);
        $messageModel = $container->get(\App\Models\MessageModel::class);
        return new \App\Controllers\ApiController($groupModel, $messageModel);
    });


    $app->get('/groups', [\App\Controllers\ApiController::class, 'getGroups']);
    $app->post('/groups', [\App\Controllers\ApiController::class, 'createGroup']);
    $app->post('/messages', [\App\Controllers\ApiController::class, 'addMessage']);
    $app->get('/messages/{group_id}', [\App\Controllers\ApiController::class, 'getMessagesByGroup']);
    $app->get('/messages/{group_id}/{user_id}', [\App\Controllers\ApiController::class, 'getMessagesByGroupAndUser']);
    $app->post('/groups/join', [\App\Controllers\ApiController::class, 'joinGroup']);
};
