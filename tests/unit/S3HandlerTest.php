<?php

namespace TJangra\FileHandler\Tests;

use Intervention\Image\Image;
use League\Flysystem\UnableToReadFile;
use TJangra\FileHandler\Adapter\S3Adapter;
use TJangra\FileHandler\FileProcessor;

class S3HandlerTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected $processor;

    protected function _before(): void
    {
        $this->processor = new FileProcessor(MATRIX, new S3Adapter(['region' => 'ap-south-1', 'version' => 'latest'], 'yii-filesystem-handler'));
        parent::_before();
    }

    public function testImageSave()
    {
        $this->processor->configure(SOURCE_PATH . '/test.jpg', '798789wuewio', 'profile')->process(function (Image $sourceImage, FileProcessor &$processor) {
            foreach ($processor->getMatrix() as $fileInfo) {
                $processor->save($fileInfo['location'], (string) $sourceImage->resize($fileInfo['size']['width'], $fileInfo['size']['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode());
            }
        });

        $this->assertNotEmpty($this->processor->read('798789wuewio/profile/16x16.jpg'));
        $this->assertNotEmpty($this->processor->read('798789wuewio/profile/50x50.jpg'));
        $this->assertNotEmpty($this->processor->read('798789wuewio/profile/100x100.jpg'));
        $this->assertNotEmpty($this->processor->read('798789wuewio/profile/200x200.jpg'));
    }

    public function testImageRead()
    {
        $this->assertNotEmpty($this->processor->read('798789wuewio/profile/16x16.jpg'));
    }
    public function testImageDelete()
    {
        $this->processor->delete('798789wuewio/profile/16x16.jpg');
        $this->assertThrows(UnableToReadFile::class, function () {
            $this->processor->read('798789wuewio/profile/16x16.jpg');
        });
    }

    public function testDeleteDirectory()
    {
        $this->processor->deleteDirectory('798789wuewio/profile');
        $this->assertThrows(UnableToReadFile::class, function () {
            $this->processor->read('798789wuewio/profile/16x16.jpg');
            $this->processor->read('798789wuewio/profile/50x50.jpg');
            $this->processor->read('798789wuewio/profile/100x100.jpg');
            $this->processor->read('798789wuewio/profile/200x200.jpg');
        });
    }

    public function testSavePDF()
    {
        $this->processor->configure(SOURCE_PATH . '/sample.pdf', '798789wuewio')->save();
    }
}
