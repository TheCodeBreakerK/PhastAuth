<?php

namespace App\Domain\Repositories;

interface UserRepository
{
    public function create(array $data): array;
    public function auth(array $data): array;
    public function refresh(mixed $authorization): array;
    public function fetch(int $authorization): array;
    public function update(mixed $authorization, array $data): array;
    public function delete(mixed $authorization): array;
}