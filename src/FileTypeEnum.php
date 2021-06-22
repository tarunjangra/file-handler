<?php
use MyCLabs\Enum\Enum;

/**
 * @method static Action IMAGE()
 * @method static Action PDF()
 * @method static Action CSV()
 * @method static Action JS()
 * @method static Action CSS()
 */
final class FileTypeEnum extends Enum
{
    private const IMAGE = 'IMAGE';
    private const PDF = 'PDF';
    private const CSV = 'CSV';
    private const JS = 'JS';
    private const CSS = 'CSS';
}