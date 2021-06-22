<?php
namespace TJangra\FileHandler\Tests;

use League\Flysystem\UnableToReadFile;
use TJangra\FileHandler\Adapter\S3Adapter;
use TJangra\FileHandler\FileProcessor;

class S3HandlerTest extends \Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected $processor;

    protected function _before(): void
    {
        $this->processor = new FileProcessor(MATRIX,new S3Adapter(['region'=> 'ap-south-1','version'=>'latest'],'smesol'));
        parent::_before();
    }

    public function testSave() 
    {
            $this->processor->save(SOURCE_PATH.'/test.jpg','profile','798789wuewio');

         $this->assertNotEmpty($this->processor->read('798789wuewio/profile/16x16.jpg'));
         $this->assertNotEmpty($this->processor->read('798789wuewio/profile/50x50.jpg'));
         $this->assertNotEmpty($this->processor->read('798789wuewio/profile/100x100.jpg'));
         $this->assertNotEmpty($this->processor->read('798789wuewio/profile/200x200.jpg'));
    }


    public function testRead()
    {   
        $this->assertNotEmpty($this->processor->read('798789wuewio/profile/16x16.jpg'));
    }
    public function testDelete()
    {   
        $this->processor->delete('798789wuewio/profile/16x16.jpg');
        $this->assertThrows(UnableToReadFile::class, function() {
            $this->processor->read('798789wuewio/profile/16x16.jpg');
        });
    }

    public function testDeleteDirectory()
    {   
        $this->processor->deleteDirectory('798789wuewio/profile');
        $this->assertThrows(UnableToReadFile::class, function() {
            $this->processor->read('798789wuewio/profile/16x16.jpg');
            $this->processor->read('798789wuewio/profile/50x50.jpg');
            $this->processor->read('798789wuewio/profile/100x100.jpg');
            $this->processor->read('798789wuewio/profile/200x200.jpg');
        });
    }
    
// }
}