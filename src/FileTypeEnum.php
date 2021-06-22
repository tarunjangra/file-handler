<?php

namespace TJangra\FileHandler;

use MyCLabs\Enum\Enum;

/**
 * @method static Action IMAGE()
 * @method static Action NON_IMAGE()
 */
final class FileTypeEnum extends Enum
{
    private const IMAGE = 'IMAGE';
    private const NON_IMAGE = 'NON_IMAGE';
}
