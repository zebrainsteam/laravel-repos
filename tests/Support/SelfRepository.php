<?php

namespace Zebrainsteam\LaravelRepos\Tests\Support;

use Repositories\Core\Contracts\RepositoryInterface;

class SelfRepository implements RepositoryInterface
{
    public function get($params): ?iterable
    {
    }

    public function first(array $filter)
    {
    }

    public function getById($id, array $select = null)
    {
    }

    public function getByIdOrFail($id, array $select = null)
    {
    }

    public function create(array $data)
    {
    }

    public function insert(iterable $data): void
    {
    }

    public function insertWithTransaction(iterable $data): void
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
