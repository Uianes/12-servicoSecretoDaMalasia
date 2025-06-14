<?php
session_start();

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

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto'])) {
    $produto = trim($_POST['produto']);
    $prompt = "Seja muito direto em sua resposta e de preferência use somente a tabela como resultado. Pesquise o preço médio praticado em licitações públicas brasileiras para o produto: $produto. 
               Cite fontes ou valores estimados usados em editais.";

    $resposta = generateGeminiFlashContent($prompt);
    $_SESSION['produto'] = $produto;
    $_SESSION['resposta'] = $resposta;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!-- FORMULÁRIO HTML E RESULTADO -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Preço em Licitações com Gemini</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        input[type=text] { width: 300px; padding: 8px; }
        input[type=submit] { padding: 8px 16px; }
        table { border-collapse: collapse; width: 100%; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Consulta de Preço Médio em Licitações</h1>

    <form method="POST">
        <label for="produto">Produto ou serviço:</label><br>
        <input type="text" name="produto" id="produto" required>
        <input type="submit" value="Consultar preço médio">
    </form>

    <?php if (!empty($_SESSION['resposta'])): ?>
        <h2>Resultado para: <?= htmlspecialchars($_SESSION['produto']) ?></h2>

        <table>
            <tr>
                <th>Produto</th>
                <th>Preços praticados</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($_SESSION['produto']) ?></td>
                <td><?= nl2br(htmlspecialchars($_SESSION['resposta'])) ?></td>
            </tr>
        </table>

        <?php
        // Limpa a sessão após exibir
        unset($_SESSION['produto'], $_SESSION['resposta']);
        ?>
    <?php endif; ?>
</body>
</html>