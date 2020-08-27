<?php

return [
    'common' => [
        'resolvers' => [
            'Prozorov\Repositories\Resolvers\SelfResolver',
            'Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver',
            'Prozorov\Repositories\Resolvers\ContainerAwareResolver',
        ],
        'bindings' => [
            'users' => 'App\User',
        ],
    ],
//    'custom1' => [
//        'resolvers' => [],
//        'bindings' => [],
//    ]
];
