<?php

require_once '../vendor/autoload.php';

$autoloadPaths = [
    __DIR__ . '/../../autoload.php',
    getenv('HOME') . '/.composer/vendor/autoload.php'
];

foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

if (!class_exists('Parsedown')) {
    die("Erro: Biblioteca Parsedown não encontrada. Execute 'composer require erusev/parsedown'");
}

if (isset($_POST['markdown'])) {
    $markdown = trim($_POST['markdown']);
    if (!empty($markdown)) {
        generateCleanDoc($markdown);
    }
}

function generateCleanDoc($markdown) {
    $parsedown = new Parsedown();
    $html = $parsedown->text($markdown);
    
    $rtf = <<<RTF
{\\rtf1\\ansi\\ansicpg1252\\deff0\\nouicompat\\deflang1046
{\\fonttbl{\\f0\\fnil\\fcharset0 Calibri;}}
{\\colortbl ;\\red0\\green0\\blue0;}
{\\*\\generator Markdown2Word 1.0}\\viewkind4\\uc1
\\f0\\fs24\\lang1046
RTF;

    // 3. Conversão segura para RTF
    $rtf .= convertToSafeRtf($html);
    $rtf .= "}";

    // Enviar cabeçalhos
    header('Content-Type: application/msword; charset=Windows-1252');
    header('Content-Disposition: attachment; filename="documento.doc"');
    header('Content-Length: ' . strlen($rtf));
    echo $rtf;
    exit;
}

function convertToSafeRtf($html) {
    $html = strip_tags($html, '<h1><h2><h3><strong><b><em><i><u><ul><ol><li><p><br>');
    
    $replacements = [

        // Cabecalhos
        '/<h1>(.*?)<\/h1>/i' => "\\fs36\\b $1\\b0\\fs24\\par\\par",
        '/<h2>(.*?)<\/h2>/i' => "\\fs32\\b $1\\b0\\fs24\\par\\par",
        '/<h3>(.*?)<\/h3>/i' => "\\fs28\\b $1\\b0\\fs24\\par\\par",
        
        '/<(strong|b)>(.*?)<\/(strong|b)>/i' => "\\b $2\\b0",
        '/<(em|i)>(.*?)<\/(em|i)>/i' => "\\i $2\\i0",
        '/<u>(.*?)<\/u>/i' => "\\ul $1\\ul0",
        
        '/<li>(.*?)<\/li>/i' => "\\bullet $1\\par",
        '/<(ul|ol)>/i' => "",
        '/<\/(ul|ol)>/i' => "\\par",
        
        '/<p>(.*?)<\/p>/i' => "$1\\par\\par",
        '/<br\s?\/?>/i' => "\\par",
    ];
    
    $rtf = preg_replace(array_keys($replacements), array_values($replacements), $html);
    
    $rtf = mb_convert_encoding($rtf, 'Windows-1252', 'UTF-8');
    
    $rtf = str_replace(
        ["\\", "{", "}", "«", "»", "“", "”"],
        ["\\\\", "\\{", "\\}", "\\'ab", "\\'bb", "\\'93", "\\'94"],
        $rtf
    );
    
    return $rtf;
}