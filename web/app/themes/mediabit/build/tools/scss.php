<?php

use ScssPhp\ScssPhp\Compiler;

class SCSSCompiler
{
    public static function compileIfNeeded($inputFile, $outputFile)
    {
        $inputPath = get_template_directory() . $inputFile;
        $outputPath = get_template_directory() . $outputFile;
        $importPath = get_template_directory() . '/sources/scss/';

        if (self::hasModifiedFiles($importPath, $outputPath)) {
            self::compileSCSS($inputPath, $outputPath);
        }
    }

    private static function hasModifiedFiles($importPath, $outputPath)
    {
        $outputModifiedTime = file_exists($outputPath) ? filemtime($outputPath) : 0;

        $directory = new RecursiveDirectoryIterator($importPath);
        $iterator = new RecursiveIteratorIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() > $outputModifiedTime) {
                return true;
            }
        }
        return false;
    }

    private static function compileSCSS($inputPath, $outputPath)
    {
        $compiler = new Compiler();
        $compiler->setImportPaths(dirname($inputPath));

        $scssContent = file_get_contents($inputPath);
        $cssContent = $compiler->compile($scssContent);

        file_put_contents($outputPath, $cssContent);
    }
}

// Usage
if (isset($_GET['compilescss']) && $_GET['compilescss'] == 1) {
    SCSSCompiler::compileIfNeeded('/sources/scss/theme.scss', '/assets/css/theme.css');
}