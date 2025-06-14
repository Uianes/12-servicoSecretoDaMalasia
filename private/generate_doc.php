<?php
session_start();
require_once '../includes/toast.php';

$result = $_SESSION['search_result'] ?? null;
if (!$result) {
  redirect_with_toast('./first_search.php', 'Nenhum resultado encontrado', 'warning');
}

// converter o html em docx com PHPWord
require_once '../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

try {
  // Criar novo documento PHPWord
  $phpWord = new PhpWord();

  // Adicionar seção
  $section = $phpWord->addSection();

  // Processar o conteúdo linha por linha
  $lines = explode("\n", $result);

  foreach ($lines as $line) {
    $line = trim($line);

    if (empty($line)) {
      // Linha vazia - adicionar espaço
      $section->addTextBreak();
      continue;
    }

    // Headers
    if (preg_match('/^### (.+)$/', $line, $matches)) {
      $section->addTitle($matches[1], 3);
    } elseif (preg_match('/^## (.+)$/', $line, $matches)) {
      $section->addTitle($matches[1], 2);
    } elseif (preg_match('/^# (.+)$/', $line, $matches)) {
      $section->addTitle($matches[1], 1);
    }
    // Listas
    elseif (preg_match('/^[\*\-\+] (.+)$/', $line, $matches)) {
      $textRun = $section->addTextRun();
      $textRun->addText('• ');
      $textRun->addText(processInlineFormatting($matches[1]));
    }
    // Texto normal
    else {
      $textRun = $section->addTextRun();
      addFormattedText($textRun, $line);
    }
  }

  // Gerar nome do arquivo
  $filename = 'documento_' . date('Y-m-d_H-i-s') . '.docx';
  $temp_file = sys_get_temp_dir() . '/' . $filename;

  // Salvar documento
  $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
  $objWriter->save($temp_file);

  // Fazer download do arquivo
  if (file_exists($temp_file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($temp_file));

    // Limpar buffer de saída
    ob_clean();
    flush();

    // Enviar arquivo
    readfile($temp_file);

    // Remover arquivo temporário
    unlink($temp_file);
    exit;
  } else {
    redirect_with_toast('./first_search.php', 'Erro ao gerar documento', 'error');
  }
} catch (Exception $e) {
  error_log('Erro ao gerar documento: ' . $e->getMessage());
  redirect_with_toast('../pages/first_search.php', 'Erro ao gerar documento: ' . $e->getMessage(), 'error');
}

// Função para processar formatação inline
function processInlineFormatting($text)
{
  // Remover formatação markdown para texto simples
  $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
  $text = preg_replace('/\*(.*?)\*/', '$1', $text);
  $text = preg_replace('/`([^`]+)`/', '$1', $text);
  $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);

  return $text;
}

// Função para adicionar texto formatado
function addFormattedText($textRun, $text)
{
  // Processar bold
  $parts = preg_split('/(\*\*.*?\*\*)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

  foreach ($parts as $part) {
    if (preg_match('/\*\*(.*?)\*\*/', $part, $matches)) {
      $textRun->addText($matches[1], ['bold' => true]);
    } elseif (!empty($part)) {
      // Processar italic
      $italicParts = preg_split('/(\*.*?\*)/', $part, -1, PREG_SPLIT_DELIM_CAPTURE);

      foreach ($italicParts as $italicPart) {
        if (preg_match('/\*(.*?)\*/', $italicPart, $italicMatches)) {
          $textRun->addText($italicMatches[1], ['italic' => true]);
        } elseif (!empty($italicPart)) {
          // Processar código
          $codeParts = preg_split('/(`[^`]+`)/', $italicPart, -1, PREG_SPLIT_DELIM_CAPTURE);

          foreach ($codeParts as $codePart) {
            if (preg_match('/`([^`]+)`/', $codePart, $codeMatches)) {
              $textRun->addText($codeMatches[1], ['name' => 'Courier New']);
            } elseif (!empty($codePart)) {
              $textRun->addText($codePart);
            }
          }
        }
      }
    }
  }
}
