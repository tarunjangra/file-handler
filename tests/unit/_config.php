<?php

use TJangra\FileHandler\Adapter\LocalAdapter;
use TJangra\FileHandler\Adapter\S3Adapter;
use yii\FileSystem\Handler;

return [
    'id' => 'file-handler-tests',
    'class' => \yii\console\Application::class,
    'basePath' => \Yii::getAlias('@tests'),
    'runtimePath' => \Yii::getAlias('@tests/_output'),
];
