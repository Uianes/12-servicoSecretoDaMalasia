<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  session_unset();
  redirect_with_toast('../index.php', "Você não está logado. Faça login para acessar esta página.");
}

// --- CONFIGURAÇÃO DE SEGURANÇA (MUITO IMPORTANTE) ---
// Em um ambiente de produção, JAMAIS coloque sua chave de API diretamente no código.
// Use variáveis de ambiente (getenv('GEMINI_API_KEY')) ou um arquivo de configuração seguro
// fora do diretório público do seu site.
define('GEMINI_API_KEY', 'AIzaSyBtK_pWJTHqgGew6tDf2iuKeIJ3E0chYWQ'); // <--- SUBSTITUA PELA SUA CHAVE REAL

// URL da API Gemini para o modelo 'gemini-2.0-flash'
// Verifique sempre a documentação oficial da Google AI Studio para os endpoints mais recentes.
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . GEMINI_API_KEY);

/**
 * Envia um prompt de texto para o modelo Gemini 2.0 Flash e retorna o texto gerado.
 *
 * @param string $prompt O texto do prompt a ser enviado para a Gemini.
 * @return string|null O texto gerado pela Gemini ou null em caso de erro.
 */
function generateGeminiFlashContent(string $prompt): ?string
{
    // Dados a serem enviados no corpo da requisição JSON
    $postData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        // Opcional: Adicione configurações de geração para controlar a resposta
        // 'generationConfig' => [
        //     'temperature' => 0.7, // Controla a aleatoriedade (0.0 a 1.0)
        //     'maxOutputTokens' => 500, // Limita o tamanho da resposta
        //     'topP' => 0.9,
        //     'topK' => 40,
        // ],
        // Opcional: Adicione configurações de segurança para filtrar conteúdo
        // 'safetySettings' => [
        //     ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
        //     // Adicione outras categorias de segurança conforme necessário
        // ],
    ];

    $jsonPayload = json_encode($postData);

    if ($jsonPayload === false) {
        error_log("Erro ao codificar JSON para a API Gemini (gemini-2.0-flash).");
        return null;
    }

    $ch = curl_init(); // Inicializa uma nova sessão cURL

    // Configurações da requisição cURL
    curl_setopt($ch, CURLOPT_URL, GEMINI_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);
    // Definir um tempo limite para a requisição (opcional, mas recomendado para evitar travamentos)
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 segundos

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Verifica por erros cURL
    if (curl_errno($ch)) {
        error_log("Erro na requisição cURL para Gemini 2.0 Flash: " . curl_error($ch));
        curl_close($ch);
        return null;
    }

    curl_close($ch); // Fecha a sessão cURL

    // Decodifica a resposta JSON
    $body = json_decode($response, true);

    // Verifica se a resposta foi bem-sucedida (código HTTP 200)
    if ($httpCode === 200) {
        // Verifica se a resposta contém o texto gerado
        if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
            return $body['candidates'][0]['content']['parts'][0]['text'];
        } else {
            // Se a resposta 200 não contiver o texto esperado, pode ser um erro de formatação da resposta
            // ou um problema com o prompt que resultou em uma resposta vazia.
            error_log("Resposta 200 da API Gemini 2.0 Flash não contém o texto esperado. Resposta: " . $response);
            return null;
        }
    } else {
        // Trata códigos de erro HTTP diferentes de 200
        error_log("Erro HTTP {$httpCode} da API Gemini 2.0 Flash. Resposta: " . $response);
        return null;
    }
}

// --- EXEMPLO DE USO EM UM FORMULÁRIO HTML SIMPLES ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_prompt'])) {
    $userPrompt = "Gerar um Edital de Licitação completo (Pregão Eletrônico), incluindo o Termo de Referência detalhado, para a aquisição de: " . trim($_POST['user_prompt']) . ". Considere as informações sobre o usuário que está solicitando os dados: Email: " . $_SESSION['user_email'] . ", Nome: " . $_SESSION['user_nome'] . ", Cargo: " . $_SESSION['user_cargo'] . ", Secretaria: " . $_SESSION['user_secretaria']  ;
    $_SESSION['user_prompt'] = $_POST['user_prompt']; 
    if (!empty($userPrompt)) {
        $generatedText = generateGeminiFlashContent($userPrompt);

        if ($generatedText) {
            $_SESSION['search_result'] = '';
            $_SESSION['search_result'] = $generatedText;
            header('Location: ../pages/dashboard.php');
            exit;
        } else {
            echo "<p class='error-message'>Não foi possível obter uma resposta da Gemini 2.0 Flash. Por favor, tente novamente.</p>";
        }
    } else {
        echo "<p class='error-message'>Por favor, digite um prompt válido.</p>";
    }
}
