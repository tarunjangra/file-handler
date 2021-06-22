<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

use Intervention\Image\ImageManagerStatic;
use TJangra\FileHandler\Adapter\LocalAdapter;
use TJangra\FileHandler\FileTypeEnum;

class FileProcessor
{
    private array $matrixConfig;
    private AdapterInterface $adapter;
    private string $driver;
    private string $sourcePath;
    private ?string $uniqueIdentifire;
    private ?string $fileCategory;
    private array $fileMatrix = [];
    private FileTypeEnum $fileType;

    public function __construct(array $matrixConfig, AdapterInterface $adapter, string $driver = 'gd')
    {
        $this->matrixConfig = $matrixConfig;
        $this->adapter = $adapter ?? new LocalAdapter(dirname(__DIR__));
        $this->driver = $driver;
        $this->fileType = FileTypeEnum::NON_IMAGE();
    }

    public function configure(string $sourcePath, string $uniqueIdentifire = null, string $fileCategory = null, string $fileName = null): FileProcessor
    {
        $this->sourcePath = $sourcePath;
        $this->uniqueIdentifire = $uniqueIdentifire;
        $this->fileCategory = $fileCategory;

        $ext = pathinfo($this->sourcePath, PATHINFO_EXTENSION);
        $fileName ??= pathinfo($this->sourcePath, PATHINFO_FILENAME);
        $mimeType = mime_content_type($this->sourcePath);
        $this->fileMatrix = (new Matrix($this->matrixConfig, $mimeType, $this->uniqueIdentifire))($ext, $this->fileCategory, $fileName);
        return $this;
    }

    public function getMatrix(): array
    {
        return $this->fileMatrix['files'];
    }


    public function process($callback = null): FileProcessor
    {
        $mimeType = mime_content_type($this->sourcePath);
        if (preg_match("/image/", $mimeType) && $callback) {
            $this->fileType = FileTypeEnum::IMAGE();
            ImageManagerStatic::configure(array('driver' => $this->driver));
            $callback(ImageManagerStatic::make($this->sourcePath), $this);
        } else {
            throw new \Exception('Provided source is not an image. Skip "process" function call.');
        }
        return $this;
    }

    public function save(string $location = null, string $data = null): void
    {
        if ($this->fileType == FileTypeEnum::IMAGE()) {
            if ($location && $data) {
                $this->adapter->save($location, $data);
            } else {
                throw new \Exception('Provided parameters are empty.');
            }
        } else {
            $this->adapter->save($this->getMatrix()[0]['location'], file_get_contents($this->sourcePath));
        }
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
