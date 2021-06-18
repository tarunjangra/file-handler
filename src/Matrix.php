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
        $this->uniqueIdentifire = $uniqueIdentifire ?? microtime(true);
        $this->mimeType = $mimeType;

        // only support jpeg, png
    }

    function __invoke(string $ext, string $fileType = null)
    {
        $matrixStructure = "{$this->uniqueIdentifire}/{$fileType}";
        if (preg_match("/image/", $this->mimeType)) {
            foreach ($this->matrix[$fileType] as $dimensions) {
                $return["{$dimensions['width']}x{$dimensions['height']}"] = [
                    'name' => "{$dimensions['width']}x{$dimensions['height']}.{$ext}",
                    'location' => "{$matrixStructure}/{$dimensions['width']}x{$dimensions['height']}.{$ext}",
                    'size' => $dimensions
                ];
            }

            return ['directory' => $matrixStructure, 'files' => $return];
        }
        $randomFileName = time();
        $fileType = $fileType ?? $ext;
        $return[$ext] = [
            'name' => "{$randomFileName}.{$ext}",
            'location' => "{$matrixStructure}/{$randomFileName}.{$ext}",
            'size' => null
        ];
        return ['directory' => "{$matrixStructure}", 'files' => $return];
    }
}
