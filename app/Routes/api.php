<?php

use DI\Container;
use Slim\App;
use App\Services\GroupService;
use App\Services\MessageService;
use App\Models\GroupModel;
use App\Models\MessageModel;

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

    // GroupService bağımlılığını tanımlayın
    $container->set(\App\Services\GroupService::class, function (Container $container) {
        $groupModel = $container->get(GroupModel::class);
        return new GroupService($groupModel);
    });
    
    $container->set(\App\Services\MessageService::class, function (Container $container) {
        $messageModel = $container->get(MessageModel::class);
        $groupModel = $container->get(GroupModel::class);
        return new MessageService($messageModel, $groupModel);
    });

    $app->get('/groups', [\App\Controllers\GroupController::class, 'getAllGroups']);
    $app->post('/groups', [\App\Controllers\GroupController::class, 'createGroup']);
    $app->post('/messages', [\App\Controllers\MessageController::class, 'addMessage']);
    $app->get('/messages/{group_id}', [\App\Controllers\MessageController::class, 'getMessagesByGroup']);
    $app->get('/messages/{group_id}/{user_id}', [\App\Controllers\MessageController::class, 'getMessagesByGroupAndUser']);
    $app->post('/groups/join', [\App\Controllers\GroupController::class, 'joinGroup']);
};
