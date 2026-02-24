# PowerShell Script for Mass Arabic Text Translation
# This script finds and replaces Arabic text with translation keys

# Configuration
$ViewsPath = "f:\larvel state big\resources\views"
$LangPath = "f:\larvel state big\resources\lang"
$LogFile = "f:\larvel state big\translation_log.txt"

# Load translations
Write-Host "📂 Loading translation files..." -ForegroundColor Green

$Translations = @()
$Categories = @('auth', 'admin', 'dashboard', 'property', 'user', 'common', 'messages', 'validation', 'buttons', 'navigation', 'forms', 'errors')

foreach ($category in $Categories) {
    $arFile = Join-Path $LangPath "ar\$category.php"
    if (Test-Path $arFile) {
        $content = Get-Content $arFile -Raw
        # Simple parsing - extract PHP array
        if ($content -match "return \[([\s\S]*?)\];") {
            $phpContent = $matches[1]
            # Extract key-value pairs (simplified)
            $lines = $phpContent -split "`n"
            foreach ($line in $lines) {
                if ($line -match "'([^']+)'\s*=>\s*'([^']*)'") {
                    $key = $matches[1]
                    $value = $matches[2]
                    $Translations[$value] = "$category.$key"
                }
            }
        }
    }
}

Write-Host "   Loaded $($Translations.Count) translations" -ForegroundColor Yellow

# Process all Blade files
Write-Host "`n📄 Processing Blade files..." -ForegroundColor Green

$BladeFiles = Get-ChildItem -Path $ViewsPath -Recurse -Filter "*.blade.php"
$ProcessedCount = 0
$ReplacementCount = 0

foreach ($file in $BladeFiles) {
    $content = Get-Content $file -Raw
    $originalContent = $content
    $fileReplacements = 0

    # Skip if no Arabic text
    if ($content -notmatch "[\u0600-\u06FF]") {
        continue
    }

    # Process each translation
    foreach ($arabicText in $Translations.Keys) {
        $translationKey = $Translations[$arabicText]
        
        # Create regex pattern for exact match
        $pattern = "['`"]$([Regex]::Escape($arabicText))['`"]"
        
        if ($content -match $pattern) {
            # Replace with translation function
            $replacement = "__('$translationKey')"
            $content = $content -replace $pattern, $replacement
            $fileReplacements++
        }
    }

    # Save file if changes were made
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8
        $relativePath = $file.FullName.Replace($ViewsPath, "").Replace("\", "/")
        Write-Host "   ✅ $relativePath ($fileReplacements replacements)" -ForegroundColor Cyan
        
        $ProcessedCount++
        $ReplacementCount += $fileReplacements
    }
}

# Generate report
Write-Host "`n📊 Translation Summary:" -ForegroundColor Green
Write-Host "   Processed files: $ProcessedCount" -ForegroundColor Yellow
Write-Host "   Total replacements: $ReplacementCount" -ForegroundColor Yellow

$Report = @{
    timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    processed_files = $ProcessedCount
    total_replacements = $ReplacementCount
    translations_loaded = $Translations.Count
}

$Report | ConvertTo-Json -Depth 3 | Out-File -FilePath "f:\larvel state big\batch_translation_report.json" -Encoding UTF8

Write-Host "   📄 Report saved to: batch_translation_report.json" -ForegroundColor Green
Write-Host "`n✅ Batch translation completed!" -ForegroundColor Green
