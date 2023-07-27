<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // Veritabanı bağlantısını sağlamak için SQLite dosya yolunu belirtin
    $pdo = new PDO('sqlite:' . __DIR__ . '/../chatapp.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // GrupModel ve MessageModel sınıflarını oluşturun ve ApiController'a enjekte edin
    $groupModel = new \App\Models\GroupModel($pdo);
    $messageModel = new \App\Models\MessageModel($pdo);
    $apiController = new \App\Controllers\ApiController($groupModel, $messageModel);

    // API rotalarını tanımlayın
    $app->get('/groups', [$apiController, 'getGroups']);
    $app->post('/groups', [$apiController, 'createGroup']);
    $app->post('/messages', [$apiController, 'addMessage']);
    $app->get('/messages/{group_id}', [$apiController, 'getMessagesByGroup']);
};





