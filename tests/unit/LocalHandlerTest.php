<?php
namespace TJangra\FileHandler\Tests;

use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;
use TJangra\FileHandler\Adapter\LocalAdapter;
use TJangra\FileHandler\AdapterInterface;
use TJangra\FileHandler\FileProcessor;
use Yii;
use yii\FileSystem\Handler;

class LocalHandlerTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected $processor;

    protected function _before(): void
    {
       $this->processor = new FileProcessor(MATRIX,new LocalAdapter(DESTINATION_PATH));
        parent::_before();
    }

    public function testImageSave() 
    {
        $this->processor->configure(SOURCE_PATH . '/test.jpg', '798789wuewio', 'profile')->process(function (Image $sourceImage, array $fileMatrix, AdapterInterface $adp) {
            foreach ($fileMatrix as $fileInfo) {
                $adp->save($fileInfo['location'], (string) $sourceImage->resize($fileInfo['size']['width'], $fileInfo['size']['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode());
            }
        });
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/16x16.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/50x50.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/100x100.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/200x200.jpg'));
    }
    public function testImageRead()
    {   
        $fileData = $this->processor->read('798789wuewio/profile/16x16.jpg');
        $this->assertTrue($fileData === file_get_contents(DESTINATION_PATH.'/798789wuewio/profile/16x16.jpg'));
    }

    public function testImageDelete()
    {   
        $this->processor->delete('798789wuewio/profile/16x16.jpg');
        $this->assertFalse(file_exists(DESTINATION_PATH.'/798789wuewio/profile/16x16.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/50x50.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/100x100.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/200x200.jpg'));
    }

    public function testDeleteDirectory()
    {   
        $this->processor->deleteDirectory('798789wuewio');
        $this->assertFalse(file_exists(DESTINATION_PATH.'/798789wuewio/profile/16x16.jpg'));
        $this->assertFalse(file_exists(DESTINATION_PATH.'/798789wuewio/profile/50x50.jpg'));
        $this->assertFalse(file_exists(DESTINATION_PATH.'/798789wuewio/profile/100x100.jpg'));
        $this->assertFalse(file_exists(DESTINATION_PATH.'/798789wuewio/profile/200x200.jpg'));
    }
    
}
