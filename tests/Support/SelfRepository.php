<?php

namespace Zebrainsteam\LaravelRepos\Tests\Support;

use Prozorov\Repositories\Contracts\RepositoryInterface;

class SelfRepository implements RepositoryInterface
{
    public function get($params): ?iterable
    {
    }

    public function first(array $filter)
    {
    }

    public function getById(int $id, array $select = null)
    {
    }

    public function getByIdOrFail(int $id, array $select = null)
    {
    }

    public function create(array $data)
    {
    }

    public function update($model, array $data): void
    {
    }

    public function delete($model): void
    {
    }

    public function exists(array $filter): bool
    {
    }

    public function count(array $filter = []): int
    {
    }

    public function openTransaction(): void
    {
    }

    public function commitTransaction(): void
    {
    }

    public function rollbackTransaction(): void
    {
    }
}
