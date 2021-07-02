<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

use Exception;
use Intervention\Image\ImageManagerStatic;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\GeneratedExtensionToMimeTypeMap;
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
    private bool $preserveOriginal = false;

    private string $fileName;
    private string $mimeType;
    private string $extension;

    public function __construct(array $matrixConfig, AdapterInterface $adapter, bool $preserveOriginal = false, string $driver = 'gd')
    {
        $this->matrixConfig = $matrixConfig;
        $this->adapter = $adapter ?? new LocalAdapter(dirname(__DIR__));
        $this->driver = $driver;
        $this->fileType = FileTypeEnum::NON_IMAGE();
        $this->preserveOriginal = $preserveOriginal;
    }

    public function configure(string $sourcePath, array $mimeTypeMap, string $uniqueIdentifire = null, string $fileCategory = null, string $fileName = null): FileProcessor
    {
        $this->sourcePath = $sourcePath;
        $this->uniqueIdentifire = $uniqueIdentifire;
        $this->fileCategory = $fileCategory;
        $this->mimeType =  current($mimeTypeMap);
        $this->extension = key($mimeTypeMap);
        $this->fileName = $fileName ?? (string) microtime(true);
        $this->fileMatrix = (new Matrix($this->matrixConfig, $this->mimeType, $this->uniqueIdentifire))($this->extension, $this->fileCategory, $this->fileName);
        return $this;
    }

    public function getMatrix(): array
    {
        return $this->fileMatrix['files'];
    }

    public function getFileName(): string
    {
        return pathinfo($this->targetFilename, PATHINFO_BASENAME);
    }

    public function getMimeTypeMap(): string
    {
        return json_encode([$this->extension => $this->mimeType]);
    }

    public function process($callback = null): FileProcessor
    {
        if (preg_match("/image/", $this->getMimeType()) && $callback) {
            $this->fileType = FileTypeEnum::IMAGE();
            ImageManagerStatic::configure(array('driver' => $this->driver));
            $sourceImage = ImageManagerStatic::make($this->sourcePath);
            $callback($sourceImage, $this);
            if ($this->preserveOriginal) {
                $this->save($this->getMatrix()[0]['location'] . DIRECTORY_SEPARATOR . 'original.' . $this->getExtension(), (string) $sourceImage->encode());
            }
        } else {
            throw new \Exception('Provided source is not an image. Skip "process" function call.');
        }
        return $this;
    }

    public function save(string $location = null, string $data = null): void
    {
        if ($this->fileType == FileTypeEnum::IMAGE()) {
            if ($location && $data) {
                $this->targetFilename = $location;
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

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function delete(string $location): void
    {
        $this->adapter->delete($location);
    }

    public function deleteDirectory(string $location): void
    {
        $this->adapter->deleteDirectory($location);
    }

    public function read(): string
    {
        if (empty($this->sourcePath)) {
            throw new \Exception("Required parameter: sourcePath is missing.");
        }
        return $this->adapter->read($this->sourcePath);
    }
}
