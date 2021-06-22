<?php

declare(strict_types=1);

namespace TJangra\FileHandler;

class Matrix
{
    private ?string $uniqueIdentifire;
    private ?string $mimeType;
    private ?array $matrix;

    public function __construct(array $matrix, string $mimeType, ?string $uniqueIdentifire = null)
    {
        $this->matrix = $matrix;
        $this->uniqueIdentifire = $uniqueIdentifire ?? (string) microtime(true);
        $this->mimeType = $mimeType;

        // only support jpeg, png
    }

    function __invoke(string $ext, string $fileCategory = null, string $fileName = null)
    {

        $fileCategory = $fileCategory ?? $ext;
        $matrixStructure = "{$this->uniqueIdentifire}/{$fileCategory}";
        if (preg_match("/image/", $this->mimeType)) {
            if (isset($this->matrix[$fileCategory])) {
                foreach ($this->matrix[$fileCategory] as $dimensions) {
                    $return[] = [
                        'category' => $fileCategory,
                        'name' => "{$dimensions['width']}x{$dimensions['height']}.{$ext}",
                        'location' => "{$matrixStructure}/{$dimensions['width']}x{$dimensions['height']}.{$ext}",
                        'size' => $dimensions
                    ];
                }
                return ['directory' => $matrixStructure, 'files' => $return];
            }
        }
        $randomFileName = $fileName??time();
        $return[] = [
            'category' => $fileCategory,
            'name' => "{$randomFileName}.{$ext}",
            'location' => "{$matrixStructure}/{$randomFileName}.{$ext}",
            'size' => null
        ];
        return ['directory' => "{$matrixStructure}", 'files' => $return];
    }
}
