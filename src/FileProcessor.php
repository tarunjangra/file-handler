<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

use FileTypeEnum;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;
use TJangra\FileHandler\Adapter\LocalAdapter;

class FileProcessor
{
    private array $matrixConfig;
    private AdapterInterface $adapter;
    private string $driver;
    private string $sourcePath;
    private string $uniqueIdentifire;
    private string $fileCategory;
    public array $fileMatrix;

    public function __construct(array $matrixConfig, AdapterInterface $adapter, string $driver = 'gd')
    {
        $this->matrixConfig = $matrixConfig;
        $this->adapter = $adapter ?? new LocalAdapter(dirname(__DIR__));
        $this->driver = $driver;
    }

    public function configure(string $sourcePath, string $uniqueIdentifire, string $fileCategory = null): FileProcessor {
        $this->sourcePath = $sourcePath;
        $this->uniqueIdentifire = $uniqueIdentifire;
        $this->fileCategory = $fileCategory;

        $ext = pathinfo($this->sourcePath, PATHINFO_EXTENSION);
        $mimeType = mime_content_type($this->sourcePath);
        $this->fileMatrix = (new Matrix($this->matrixConfig, $mimeType, $this->uniqueIdentifire))($ext, $this->fileCategory);
        return $this;
    }


    public function process($callback = null): FileProcessor
    {
        ImageManagerStatic::configure(array('driver' => $this->driver));
        $mimeType = mime_content_type($this->sourcePath);
        if (preg_match("/image/", $mimeType) && $callback) {
            $callback(ImageManagerStatic::make($this->sourcePath),$this->fileMatrix['files'], $this->adapter);
        }
        return $this;
    }

    public function save($location, $data): void
    {
        $this->adapter->save($location, $data);
    }
    
    public function delete(string $location): void
    {
        $this->adapter->delete($location);
    }

    public function deleteDirectory(string $location): void
    {
        $this->adapter->deleteDirectory($location);
    }

    public function read(string $location): string
    {
        return $this->adapter->read($location);
    }
}
