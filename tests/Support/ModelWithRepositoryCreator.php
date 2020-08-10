<?php

namespace Zebrainsteam\LaravelRepos\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Prozorov\Repositories\Contracts\HasRepositoryInterface;
use Prozorov\Repositories\Contracts\RepositoryInterface;

class ModelWithRepositoryCreator extends Model implements HasRepositoryInterface
{
    public static function getRepository(): RepositoryInterface
    {
        return new SelfRepository();
    }
}
