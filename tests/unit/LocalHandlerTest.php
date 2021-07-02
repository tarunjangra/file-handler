<?php

namespace TJangra\FileHandler\Tests;

use Intervention\Image\Image;
use TJangra\FileHandler\Adapter\LocalAdapter;
use TJangra\FileHandler\FileProcessor;

class LocalHandlerTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected FileProcessor $processor;

    protected function _before(): void
    {
        $this->processor = new FileProcessor(MATRIX, new LocalAdapter(DESTINATION_PATH), true);
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
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/16x16.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/50x50.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/100x100.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/200x200.jpg'));
    }
    public function testImageRead()
    {
        $fileData = $this->processor->configure('798789wuewio/profile/16x16.jpg', ['jpg' => 'image/jpeg'])->read();
        $this->assertTrue($fileData === file_get_contents(DESTINATION_PATH . '/798789wuewio/profile/16x16.jpg'));
        $this->assertTrue($this->processor->getMimeType() === 'image/jpeg');
        $this->assertTrue($this->processor->getExtension() === 'jpg');
    }

    public function testImageDelete()
    {
        $this->processor->delete('798789wuewio/profile/16x16.jpg');
        $this->assertFalse(file_exists(DESTINATION_PATH . '/798789wuewio/profile/16x16.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/50x50.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/100x100.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/200x200.jpg'));
    }

    public function testDeleteDirectory()
    {
        $this->processor->deleteDirectory('798789wuewio');
        $this->assertFalse(file_exists(DESTINATION_PATH . '/798789wuewio/profile/16x16.jpg'));
        $this->assertFalse(file_exists(DESTINATION_PATH . '/798789wuewio/profile/50x50.jpg'));
        $this->assertFalse(file_exists(DESTINATION_PATH . '/798789wuewio/profile/100x100.jpg'));
        $this->assertFalse(file_exists(DESTINATION_PATH . '/798789wuewio/profile/200x200.jpg'));
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


    public function testImageSaveWithPreserveOriginal()
    {
        $this->processor->configure(SOURCE_PATH . '/test.jpg', ['jpg' => 'image/jpeg'], '798789wuewio', 'profile')->process(function (Image $sourceImage, FileProcessor $processor) {
            foreach ($processor->getMatrix() as $fileInfo) {
                $processor->save($fileInfo['filePath'], (string) $sourceImage->resize($fileInfo['size']['width'], $fileInfo['size']['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode());
            }
        });
        $this->assertTrue(file_exists(DESTINATION_PATH . '/798789wuewio/profile/original.jpg'));
    }
}
