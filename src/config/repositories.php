<?php

return [
    'resolvers' => [
        'Repositories\Core\Resolvers\ExistingRepositoryResolver',
        'Zebrainsteam\LaravelRepos\Resolvers\EloquentAwareResolver',
        'Repositories\Core\Resolvers\ContainerAwareResolver',
    ],
    'bindings' => [
        'users' => 'App\User',
    ],
];
