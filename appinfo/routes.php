<?php
return [
    'routes' => [
        ['name' => 'page#settings', 'url' => '/settings', 'verb' => 'GET'],
        ['name' => 'image#getRandomImage', 'url' => '/random-image/{uuid}', 'verb' => 'GET'],
        ['name' => 'rss#getFeed', 'url' => '/image-feed/{uuid}', 'verb' => 'GET'],
        ['name' => 'config#getConfig', 'url' => '/get-config/{uuid}', 'verb' => 'GET'],
        ['name' => 'config#setConfig', 'url' => '/set-config', 'verb' => 'POST'],
        ['name' => 'config#regenerateUuid', 'url' => '/regenerate-uuid', 'verb' => 'POST'],
        ['name' => 'metrics#getMetrics', 'url' => '/get-metrics', 'verb' => 'GET'],
        ['name' => 'metrics#logImageRequest', 'url' => '/log-image-request/{uuid}/{imagePath}', 'verb' => 'POST'],
    ]
];
