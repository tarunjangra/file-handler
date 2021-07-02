<?php

namespace TJangra\FileHandler\Tests;

use Intervention\Image\Image;
use League\Flysystem\UnableToReadFile;
use TJangra\FileHandler\Adapter\S3Adapter;
use TJangra\FileHandler\FileProcessor;

class S3HandlerTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected FileProcessor $processor;

    protected function _before(): void
    {
        $this->processor = new FileProcessor(MATRIX, new S3Adapter(['region' => 'ap-south-1', 'version' => 'latest'], 'yii-filesystem-handler'));
        parent::_before();
    }

    public function testImageSave()
    {
        $this->processor->configure(SOURCE_PATH . '/test.jpg', ['jpg' => 'image/jpeg'], '798789wuewio', 'profile')->process(function (Image $sourceImage, FileProcessor &$processor) {
            foreach ($processor->getMatrix() as $fileInfo) {
                $processor->save($fileInfo['filePath'], (string) $sourceImage->resize($fileInfo['size']['width'], $fileInfo['size']['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode());
                $this->assertTrue($processor->getFileName() === "{$fileInfo['size']['width']}x{$fileInfo['size']['height']}." . $processor->getExtension());
            }
        });

        $this->assertNotEmpty($this->processor->configure('798789wuewio/profile/16x16.jpg', ['jpg' => 'image/jpeg'])->read());
        $this->assertNotEmpty($this->processor->configure('798789wuewio/profile/50x50.jpg', ['jpg' => 'image/jpeg'])->read());
        $this->assertNotEmpty($this->processor->configure('798789wuewio/profile/100x100.jpg', ['jpg' => 'image/jpeg'])->read());
        $this->assertNotEmpty($this->processor->configure('798789wuewio/profile/200x200.jpg', ['jpg' => 'image/jpeg'])->read());
    }
    public function testImageRead()
    {
        $this->assertNotEmpty($this->processor->configure('798789wuewio/profile/16x16.jpg', ['jpg' => 'image/jpeg'])->read());
        $this->assertTrue($this->processor->getMimeType() === 'image/jpeg');
        $this->assertTrue($this->processor->getExtension() === 'jpg');
    }

    public function testImageDelete()
    {
        $this->processor->delete('798789wuewio/profile/16x16.jpg');
        $this->assertThrows(UnableToReadFile::class, function () {
            $this->processor->configure('798789wuewio/profile/16x16.jpg', ['jpg' => 'image/jpeg'])->read();
        });
    }

    public function testDeleteDirectory()
    {
        $this->processor->deleteDirectory('798789wuewio/profile');
        $this->assertThrows(UnableToReadFile::class, function () {
            $this->processor->configure('798789wuewio/profile/16x16.jpg', ['jpg' => 'image/jpeg'])->read();
            $this->processor->configure('798789wuewio/profile/50x50.jpg', ['jpg' => 'image/jpeg'])->read();
            $this->processor->configure('798789wuewio/profile/100x100.jpg', ['jpg' => 'image/jpeg'])->read();
            $this->processor->configure('798789wuewio/profile/200x200.jpg', ['jpg' => 'image/jpeg'])->read();
        });
    }

    public function testSavePDF()
    {
        $this->processor->configure(SOURCE_PATH . '/sample.pdf', ['pdf' => 'application/pdf'], '798789wuewio')->save();
    }

    public function testMoreImageProcessings()
    {
        $this->processor->configure(SOURCE_PATH . '/test.jpg', ['jpg' => 'image/jpeg'])->process(function (Image $sourceImage, FileProcessor &$processor) {
            $processor->save('798789wuewio/profile/test-flip.jpg', $sourceImage->flip()->encode());
        });

        $this->processor->configure(SOURCE_PATH . '/test.jpg', ['jpg' => 'image/jpeg'])->process(function (Image $sourceImage, FileProcessor &$processor) {
            $processor->save('798789wuewio/profile/test-flip-verticale.jpg', $sourceImage->flip('v')->encode());
        });

        $this->processor->configure(SOURCE_PATH . '/test.jpg', ['jpg' => 'image/jpeg'])->process(function (Image $sourceImage, FileProcessor &$processor) {
            $processor->save('798789wuewio/profile/test-rotate.jpg', $sourceImage->rotate(-85)->encode());
        });
    }

    public function testSaveCSV()
    {
        $this->processor->configure(SOURCE_PATH . '/sample.csv', ['csv' => 'text/csv'], '798789wuewio', 'profile')->targetFilename('new.csv')->save();
    }
}
