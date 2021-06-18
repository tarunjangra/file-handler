<?php

declare(strict_types=1);

namespace TJangra\FileHandler\Adapter;

use TJangra\FileHandler\AdapterInterface;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\Flysystem\Filesystem;

class S3Adapter implements AdapterInterface
{

    private Filesystem $fileSystem;

    public function __construct(array $options, string $bucketName, string $visibility = Visibility::PUBLIC)
    {

        // The internal adapter
        $adapter = new AwsS3V3Adapter(
            new S3Client($options),
            $bucketName,
            '',
            new PortableVisibilityConverter(
                $visibility
            )
        );

        // The FilesystemOperator
        $this->fileSystem = new Filesystem($adapter);
    }

    public function save(string $location, $content): void
    {
        $this->fileSystem->write($location, $content);
        return;
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
