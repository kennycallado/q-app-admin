<?php declare(strict_types=1);

namespace Core\Interfaces;

interface IRepository
{
    public function find(string $id);
    public function store($entity);
    public function delete($entity);
}
