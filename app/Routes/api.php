<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $pdo = new PDO('sqlite:' . __DIR__ . '/../chatapp.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $groupModel = new \App\Models\GroupModel($pdo);
    $messageModel = new \App\Models\MessageModel($pdo);
    $apiController = new \App\Controllers\ApiController($groupModel, $messageModel);

    $app->get('/groups', [$apiController, 'getGroups']);
    $app->post('/groups', [$apiController, 'createGroup']);
    $app->post('/messages', [$apiController, 'addMessage']);
    $app->get('/messages/{group_id}', [$apiController, 'getMessagesByGroup']);
};





