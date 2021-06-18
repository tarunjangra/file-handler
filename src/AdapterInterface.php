<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

interface AdapterInterface
{

    public function save(string $location, $content): void;
    public function delete(string $location): void;
    public function deleteDirectory(string $location): void;
    public function read(string $location): string;
}
