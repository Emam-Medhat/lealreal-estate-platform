<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use Illuminate\Support\Str;

class DocumentTemplateService
{
    /**
     * Parse and format variables from request
     *
     * @param array|null $variables
     * @return array
     */
    public function parseVariables(?array $variables): array
    {
        $parsedVariables = $variables ?? [];
        foreach ($parsedVariables as &$variable) {
            if (isset($variable['options_text']) && !empty($variable['options_text'])) {
                $variable['options'] = array_map('trim', explode(',', $variable['options_text']));
                unset($variable['options_text']);
            }
        }
        return $parsedVariables;
    }

    /**
     * Render template content with data
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @return string
     */
    public function render(DocumentTemplate $template, array $data): string
    {
        $content = $template->content;
        
        // 1. Replace defined variables
        foreach ($template->variables ?? [] as $variable) {
            $key = $variable['name'];
            $placeholder = '{{' . $key . '}}';
            $value = $data[$key] ?? '';
            
            if (is_array($value)) {
                $value = $this->processArrayVariable($key, $value);
            } elseif (is_object($value)) {
                $value = $this->processObjectVariable($value);
            } else {
                $value = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
            
            $content = str_replace($placeholder, $value, $content);
        }

        // 2. Replace any other keys in data that match placeholders (for data not in template variables definition)
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            if (strpos($content, $placeholder) !== false) {
                 if (is_array($value)) {
                    $value = $this->processArrayVariable($key, $value);
                } elseif (is_object($value)) {
                    $value = $this->processObjectVariable($value);
                } else {
                    $value = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                }
                $content = str_replace($placeholder, $value, $content);
            }
        }
        
        // 3. Process conditional blocks
        $content = $this->processConditionals($content, $data);
        
        // 4. Process loops
        $content = $this->processLoops($content, $data);
        
        return $content;
    }

    /**
     * Preview template content with sample data
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @return string
     */
    public function preview(DocumentTemplate $template, array $data): string
    {
        // Preview logic can be similar to render but might handle missing data more gracefully
        // For now, we reuse render but ensure placeholders are visible if missing
        $content = $template->content;
        
        // Replace variables
        foreach ($template->variables ?? [] as $variable) {
            $placeholder = '{{' . $variable['name'] . '}}';
            $value = $data[$variable['name']] ?? '[' . $variable['name'] . ']';
             if (is_array($value) || is_object($value)) {
                 $value = json_encode($value);
            }
            $content = str_replace($placeholder, $value, $content);
        }
        
        // Also try to replace from data keys
         foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            if (strpos($content, $placeholder) !== false) {
                 if (is_array($value) || is_object($value)) {
                     $value = json_encode($value);
                }
                $content = str_replace($placeholder, $value, $content);
            }
        }

        return $content;
    }

    private function processArrayVariable(string $key, array $value): string
    {
        $result = '';
        foreach ($value as $item) {
            if (is_array($item)) {
                foreach ($item as $subValue) {
                    $result .= $subValue . "\n";
                }
            } else {
                $result .= $item . "\n";
            }
        }
        return trim($result);
    }
    
    private function processObjectVariable($object): string
    {
        if (method_exists($object, 'toArray')) {
            return json_encode($object->toArray(), JSON_UNESCAPED_UNICODE);
        }
        return (string) $object;
    }

    private function processConditionals(string $content, array $data): string
    {
        // Process @if blocks
        // Regex to capture @if(condition)...@endif
        // Note: This is a simple regex and won't handle nested ifs correctly without a parser
        if (preg_match_all('/@if\((.*?)\)(.*?)@endif/s', $content, $matches)) {
            foreach ($matches[0] as $index => $fullMatch) {
                $condition = $matches[1][$index];
                $blockContent = $matches[2][$index];
                
                if ($this->evaluateCondition($condition, $data)) {
                    $content = str_replace($fullMatch, $blockContent, $content);
                } else {
                    $content = str_replace($fullMatch, '', $content);
                }
            }
        }
        return $content;
    }

    private function evaluateCondition(string $condition, array $data): bool
    {
        $condition = trim($condition);
        
        // Simple comparison: var !== 'value'
        if (strpos($condition, '!==') !== false) {
            list($var, $val) = explode('!==', $condition, 2);
            $var = trim($var);
            $val = trim($val, " '\"");
            return ($data[$var] ?? '') !== $val;
        }

        // Simple comparison: var == 'value'
        if (strpos($condition, '==') !== false) {
            list($var, $val) = explode('==', $condition, 2);
            $var = trim($var);
            $val = trim($val, " '\"");
            return ($data[$var] ?? '') == $val;
        }
        
        // Simple comparison: var != 'value'
        if (strpos($condition, '!=') !== false) {
             list($var, $val) = explode('!=', $condition, 2);
            $var = trim($var);
            $val = trim($val, " '\"");
            return ($data[$var] ?? '') != $val;
        }

        // Boolean check: var
        return !empty($data[$condition] ?? null);
    }

    private function processLoops(string $content, array $data): string
    {
        // Process @foreach blocks
        // Regex: @foreach(array as item)...@endforeach
        if (preg_match_all('/@foreach\((.*?) as (.*?)\)(.*?)@endforeach/s', $content, $matches)) {
            foreach ($matches[0] as $index => $fullMatch) {
                $arrayName = trim($matches[1][$index]);
                $itemName = trim($matches[2][$index]);
                $loopBody = $matches[3][$index];
                
                $items = $data[$arrayName] ?? [];
                if (!is_array($items)) {
                    $items = [];
                }
                
                $result = '';
                foreach ($items as $item) {
                    $itemContent = $loopBody;
                    if (is_array($item)) {
                        foreach ($item as $key => $val) {
                            $placeholder = '{{' . $itemName . '.' . $key . '}}';
                            $itemContent = str_replace($placeholder, $val, $itemContent);
                        }
                    } else {
                        $placeholder = '{{' . $itemName . '}}';
                        $itemContent = str_replace($placeholder, $item, $itemContent);
                    }
                    $result .= $itemContent;
                }
                
                $content = str_replace($fullMatch, $result, $content);
            }
        }
        return $content;
    }
}
