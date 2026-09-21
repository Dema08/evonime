<?php

use App\Providers\AppServiceProvider;
use App\Providers\CacheServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [
    AppServiceProvider::class,
    CacheServiceProvider::class,
    RepositoryServiceProvider::class,
];
