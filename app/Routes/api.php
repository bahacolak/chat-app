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

    $container->set(\App\Controllers\GroupController::class, function (Container $container) {
        $groupModel = $container->get(\App\Models\GroupModel::class);
        return new \App\Controllers\GroupController($groupModel);
    });

    $container->set(\App\Controllers\MessageController::class, function (Container $container) {
        $messageModel = $container->get(\App\Models\MessageModel::class);
        $groupModel = $container->get(\App\Models\GroupModel::class);
        return new \App\Controllers\MessageController($messageModel, $groupModel);
    });

    $app->get('/groups', [\App\Controllers\GroupController::class, 'getGroups']);
    $app->post('/groups', [\App\Controllers\GroupController::class, 'createGroup']);
    $app->post('/messages', [\App\Controllers\MessageController::class, 'addMessage']);
    $app->get('/messages/{group_id}', [\App\Controllers\MessageController::class, 'getMessagesByGroup']);
    $app->get('/messages/{group_id}/{user_id}', [\App\Controllers\MessageController::class, 'getMessagesByGroupAndUser']);
    $app->post('/groups/join', [\App\Controllers\GroupController::class, 'joinGroup']);
};
