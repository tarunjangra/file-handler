<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

use Exception;
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
    private ?string $targetFilename = null;

    private string $fileName;
    private string $extension;

    public function __construct(array $matrixConfig, AdapterInterface $adapter, string $driver = 'gd')
    {
        $this->matrixConfig = $matrixConfig;
        $this->adapter = $adapter ?? new LocalAdapter(dirname(__DIR__));
        $this->driver = $driver;
        $this->fileType = FileTypeEnum::NON_IMAGE();
    }

    public function configure(string $sourcePath, string $uniqueIdentifire = null, string $fileCategory = null, string $fileName = null, string $mimeType = null): FileProcessor
    {
        $this->sourcePath = $sourcePath;
        $this->uniqueIdentifire = $uniqueIdentifire;
        $this->fileCategory = $fileCategory;

        $mimeType ??=  mime_content_type($this->sourcePath);
        if (empty($mimeType)) {
            throw new Exception("MimeType Exception: Can not detect mimetype of the document being uploaded. Please mention in the argument list.");
        }
        $mimes = new \Mimey\MimeTypes;
        $this->extension = $mimes->getExtension($mimeType);

        $this->fileName = $fileName ?? (string) microtime(true);
        $this->fileMatrix = (new Matrix($this->matrixConfig, $mimeType, $this->uniqueIdentifire))($this->extension, $this->fileCategory, $this->fileName);
        return $this;
    }

    public function getMatrix(): array
    {
        return $this->fileMatrix['files'];
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }


    public function process($callback = null): FileProcessor
    {
        $mimeType = mime_content_type($this->sourcePath);
        if (preg_match("/image/", $mimeType) && $callback) {
            $this->fileType = FileTypeEnum::IMAGE();
            ImageManagerStatic::configure(array('driver' => $this->driver));
            $callback(ImageManagerStatic::make($this->sourcePath), $this);

            if(isset($this->matrixConfig[]))

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
            $filePath = $this->targetFilename ? $this->getMatrix()[0]['location'] . DIRECTORY_SEPARATOR . $this->targetFilename : $this->getMatrix()[0]['filePath'];
            $this->adapter->save($filePath, file_get_contents($this->sourcePath));
        }
    }

    public function targetFilename(string $fileName): FileProcessor
    {
        $this->targetFilename = $fileName;
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
