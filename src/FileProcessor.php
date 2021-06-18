<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

use Intervention\Image\ImageManagerStatic as Image;
use TJangra\FileHandler\Adapter\LocalAdapter;

class FileProcessor
{
    private array $matrix;
    private AdapterInterface $adapter;
    private string $driver;

    public function __construct(?array $matrix = null, ?AdapterInterface $adapter = null, string $driver = 'gd')
    {
        $this->matrix = $matrix;
        $this->adapter = $adapter ?? new LocalAdapter(dirname(__DIR__));
        $this->driver = $driver;
    }

    public function save(string $source, string $fileType, ?string $uniqueIdentifire = null)
    {
        $ext = pathinfo($source, PATHINFO_EXTENSION);
        $mimeType = mime_content_type($source);
        $matrix = (new Matrix($this->matrix, $mimeType, $uniqueIdentifire))($ext, $fileType);
        if (preg_match("/image/", $mimeType)) {
            Image::configure(array('driver' => $this->driver));
            foreach ($matrix['files'] as $fileInfo) {
                $resizedImageData = (string) Image::make($source)->resize($fileInfo['size']['width'], $fileInfo['size']['height'])->encode();
                $this->adapter->save($fileInfo['location'], $resizedImageData);
            }
        }
        return $this;
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
