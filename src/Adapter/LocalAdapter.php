<?php

declare(strict_types=1);

namespace TJangra\FileHandler\Adapter;

use TJangra\FileHandler\AdapterInterface;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem;

class LocalAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    public $permissions = [];

    private Filesystem $fileSystem;

    public function __construct(string $rootPath)
    {
        $this->fileSystem = new Filesystem(new LocalFilesystemAdapter($rootPath));
    }

    public function save(string $location, $content): void
    {
        $this->fileSystem->write($location, $content);
    }

    public function delete(string $location): void
    {
        $this->fileSystem->delete($location);
        return;
    }

    public function read(string $location): string
    {
        return $this->fileSystem->read($location);
    }

    public function deleteDirectory(string $location): void
    {
        $this->fileSystem->deleteDirectory($location);
        return;
    }
}
