<?php
session_start();
require_once '../vendor/autoload.php'; // For TCPDF

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  session_unset();
  redirect_with_toast('../index.php', "Você não está logado. Faça login para acessar esta página.");
}

define('GEMINI_API_KEY', 'AIzaSyBtK_pWJTHqgGew6tDf2iuKeIJ3E0chYWQ');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . GEMINI_API_KEY);

function generateGeminiFlashContent(string $prompt): ?string
{
    $postData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $jsonPayload = json_encode($postData);
    if ($jsonPayload === false) return null;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GEMINI_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $body = json_decode($response, true);

    if ($httpCode === 200 && isset($body['candidates'][0]['content']['parts'][0]['text'])) {
        return $body['candidates'][0]['content']['parts'][0]['text'];
    }

    return null;
}

/**
 * Convert Markdown to HTML
 */
function markdownToHtml($text) {
    // Process headers
    $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
    
    // Process bold and italic
    $text = preg_replace('/\*\*(.*?)\*\*/m', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/m', '<em>$1</em>', $text);
    
    // Process bullet lists
    $text = preg_replace('/^- (.*?)$/m', '• $1<br>', $text);
    $text = preg_replace('/^\* (.*?)$/m', '• $1<br>', $text);
    $text = preg_replace('/^\+ (.*?)$/m', '• $1<br>', $text);
    
    // Convert markdown tables to HTML tables
    if (preg_match_all('/^\|(.+)\|$/m', $text, $tableRows)) {
        foreach ($tableRows[0] as $row) {
            $cells = explode('|', trim($row, '|'));
            $htmlRow = '<tr>';
            foreach ($cells as $cell) {
                $htmlRow .= '<td>' . trim($cell) . '</td>';
            }
            $htmlRow .= '</tr>';
            
            // Replace the original Markdown table row with HTML
            $text = str_replace($row, $htmlRow, $text);
        }
        
        // Find consecutive <tr> elements and wrap them in a table
        $text = preg_replace('/(<tr>.+?<\/tr>)(\s*)(<tr>.+?<\/tr>)/s', '<table border="1" cellpadding="5">$1$3</table>', $text);
        
        // Replace separator rows (|----|----|----|) with nothing
        $text = preg_replace('/<tr><td>[\s\-:]+<\/td><\/tr>/', '', $text);
    }
    
    // Convert line breaks
    $text = nl2br($text);
    
    return $text;
}

// Check if user prompt exists in session
if (!isset($_SESSION['user_prompt']) || empty($_SESSION['user_prompt'])) {
    die("Erro: Nenhuma consulta encontrada na sessão.");
}

// Retrieve query from session
$produto = $_SESSION['user_prompt'];
$prompt = "Seja muito direto em sua resposta e apresente os dados de forma estruturada. Pesquise o preço médio praticado em licitações públicas brasileiras para o produto: $produto. 
           Cite fontes ou valores estimados usados em editais. Inclua preço unitário, quantidade típica e valor total quando disponível. Formate a resposta usando listas e tabelas simples em markdown para facilitar a leitura.";

// Get response from Gemini API
$resposta = generateGeminiFlashContent($prompt);

if (!$resposta) {
    die("Erro ao obter resposta da API.");
}

// Generate PDF using TCPDF
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Sistema de Consulta de Preços');
$pdf->SetAuthor('Sistema Automático');
$pdf->SetTitle('Pesquisa de Preço: ' . $produto);
$pdf->SetSubject('Pesquisa de Preço em Licitações');
$pdf->SetKeywords('licitação, preço médio, ' . $produto);

// Set default header data
$pdf->SetHeaderData('', 0, 'Pesquisa de Preço em Licitações', 'Data: ' . date('d/m/Y'));

// Set header and footer fonts
$pdf->setHeaderFont(Array('helvetica', '', 10));
$pdf->setFooterFont(Array('helvetica', '', 8));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// Set margins
$pdf->SetMargins(15, 25, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set image scale factor
$pdf->setImageScale(1.25);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 16);

// Title
$pdf->Cell(0, 10, 'Relatório de Preços em Licitações', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Produto/Serviço: ' . $produto, 0, 1, 'C');
$pdf->Ln(10);

// Content
$pdf->SetFont('helvetica', '', 11);
$pdf->writeHTML('<h2>Resultados da pesquisa</h2>', true, false, true, false, '');

// Format the response: convert Markdown to HTML
$html = '<div style="line-height: 1.5;">' . markdownToHtml($resposta) . '</div>';

// Write the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Add notes
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Write(0, 'Nota: Os preços apresentados são baseados em pesquisa automática e devem ser verificados antes de qualquer decisão de compra.', '', false, 'L', true);
$pdf->Write(0, 'Documento gerado automaticamente em ' . date('d/m/Y H:i:s'), '', false, 'L', true);

// Generate filename
$filename = 'pesquisa_preco_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $produto) . '_' . date('Y-m-d') . '.pdf';

// Close and output PDF document
$pdf->Output($filename, 'D'); // 'D' means download

exit;
?>