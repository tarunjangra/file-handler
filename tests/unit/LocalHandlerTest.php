<?php
namespace TJangra\FileHandler\Tests;

use TJangra\FileHandler\Adapter\LocalAdapter;
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

    public function testSave() 
    {
        $this->processor->save(SOURCE_PATH.'/test.jpg','profile','798789wuewio');
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/16x16.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/50x50.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/100x100.jpg'));
        $this->assertTrue(file_exists(DESTINATION_PATH.'/798789wuewio/profile/200x200.jpg'));
    }
    public function testRead()
    {   
        $fileData = $this->processor->read('798789wuewio/profile/16x16.jpg');
        $this->assertTrue($fileData === file_get_contents(DESTINATION_PATH.'/798789wuewio/profile/16x16.jpg'));
    }

    public function testDelete()
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
