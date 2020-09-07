<?php

namespace Zebrainsteam\LaravelRepos\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Repositories\Core\Contracts\HasRepositoryInterface;
use Repositories\Core\Contracts\RepositoryInterface;

class ModelWithRepositoryCreator extends Model implements HasRepositoryInterface
{
    public static function getRepository(): RepositoryInterface
    {
        return new SelfRepository();
    }
}
