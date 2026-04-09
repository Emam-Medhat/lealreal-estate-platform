<?php

/**
 * Mass Translation Tool
 * 
 * This script performs bulk replacement of Arabic text with translation keys
 * across all Blade files in the project.
 */

class MassTranslator
{
    private $projectPath;
    private $translations = [];
    private $processedFiles = 0;
    private $replacements = 0;

    public function __construct($projectPath)
    {
        $this->projectPath = $projectPath;
        $this->loadTranslations();
    }

    /**
     * Execute mass translation
     */
    public function execute()
    {
        echo "🚀 Starting mass translation...\n\n";

        $this->processAllBladeFiles();
        $this->generateReport();

        echo "\n✅ Mass translation completed!\n";
        echo "📊 Processed {$this->processedFiles} files\n";
        echo "🔄 Made {$this->replacements} replacements\n";
    }

    /**
     * Load all translation files
     */
    private function loadTranslations()
    {
        echo "📂 Loading translation files...\n";

        $langPath = $this->projectPath . '/resources/lang';
        $categories = ['auth', 'admin', 'dashboard', 'property', 'user', 'common', 'messages', 'validation', 'buttons', 'navigation', 'forms', 'errors'];

        foreach ($categories as $category) {
            $arFile = $langPath . "/ar/$category.php";
            if (file_exists($arFile)) {
                $translations = include $arFile;
                foreach ($translations as $key => $value) {
                    $this->translations[$value] = "$category.$key";
                }
            }
        }

        echo "   Loaded " . count($this->translations) . " translations\n";
    }

    /**
     * Process all Blade files
     */
    private function processAllBladeFiles()
    {
        echo "\n📄 Processing Blade files...\n";

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->projectPath . '/resources/views')
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'blade') {
                $this->processFile($file->getPathname());
            }
        }
    }

    /**
     * Process individual file
     */
    private function processFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $replacements = 0;

        // Skip if already processed or no Arabic text
        if (strpos($content, '__(') !== false || !preg_match('/[\x{0600}-\x{06FF}]/u', $content)) {
            return;
        }

        $lines = explode("\n", $content);

        foreach ($lines as $lineNumber => $line) {
            // Skip comments and already translated lines
            if ($this->shouldSkipLine($line)) {
                continue;
            }

            $newLine = $this->replaceInLine($line);
            if ($newLine !== $line) {
                $lines[$lineNumber] = $newLine;
                $replacements++;
            }
        }

        if ($replacements > 0) {
            $newContent = implode("\n", $lines);
            file_put_contents($filePath, $newContent);
            
            $relativePath = str_replace($this->projectPath . '/', '', $filePath);
            echo "   ✅ $relativePath ($replacements replacements)\n";
            
            $this->processedFiles++;
            $this->replacements += $replacements;
        }
    }

    /**
     * Check if line should be skipped
     */
    private function shouldSkipLine($line)
    {
        // Skip comments
        if (preg_match('/^\s*{{--|^\s*\*\/|^\s*\*|^\s*\/\*|^\s*\/\//', $line)) {
            return true;
        }

        // Skip already translated lines
        if (strpos($line, '__(') !== false || strpos($line, 'trans(') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Replace Arabic text in line
     */
    private function replaceInLine($line)
    {
        foreach ($this->translations as $arabicText => $translationKey) {
            // Create regex pattern for exact match
            $pattern = '/["\'](' . preg_quote($arabicText, '/') . ')["\']/u';
            
            if (preg_match($pattern, $line)) {
                // Replace with translation function
                $replacement = "__('$translationKey')";
                $line = preg_replace($pattern, $replacement, $line);
            }
        }

        return $line;
    }

    /**
     * Generate report
     */
    private function generateReport()
    {
        echo "\n📊 Generating report...\n";

        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'processed_files' => $this->processedFiles,
            'total_replacements' => $this->replacements,
            'translations_loaded' => count($this->translations)
        ];

        file_put_contents(
            $this->projectPath . '/mass_translation_report.json',
            json_encode($report, JSON_PRETTY_PRINT)
        );

        echo "   📄 Report saved to: mass_translation_report.json\n";
    }
}

// Execute if run directly
if (php_sapi_name() === 'cli') {
    $translator = new MassTranslator(__DIR__);
    $translator->execute();
}
