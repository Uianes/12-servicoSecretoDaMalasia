<?php
require_once '../includes/toast.php';
require_once '../private/db_connection.php';
session_start();

$results = $_SESSION['search_result'] ?? '';

// Verifica se a pesquisa foi enviada
if (!isset($results) || empty($results)) {
  redirect_with_toast('./first_search.php', 'Nenhum resultado encontrado', 'warning ');
}

// Função para formatar o texto da IA
function formatAiText($text)
{
  // Basic markdown conversion (this is simplified)
  $text = htmlspecialchars($text);

  // Convert headers
  $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
  $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
  $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);

  // Convert bold text
  $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);

  // Convert lists
  $text = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $text);
  $text = str_replace('<li>', '<ul><li>', $text);
  $text = str_replace('</li>', '</li></ul>', $text);
  $text = str_replace('</ul><ul>', '', $text);

  // Convert line breaks
  $text = str_replace("\n\n", '<br><br>', $text);
  $text = str_replace("\n", '<br>', $text);

  return $text;
}
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include '../includes/bootstrap_styles.php' ?>
  <link rel="stylesheet" href="../assets/css/bootstrap_custom.css">
  <title> - Dashboard</title>
</head>

<body class="bg-light">

  <?php render_toast(); ?>

  <div class="container py-5">
    <div class="mb-4">
      <h2 class="mb-0">Resultados da Pesquisa</h2>
    </div>

    <div class="row g-4">
      <div class="col-12">
        <div class="card h-100 shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Texto do Edital:</h5>
            </div>
          </div>
          <div class="card-body">
            <div class="ai-response">
              <?php echo formatAiText($results); ?>
            </div>
          </div>
          <div class="card-footer bg-white border-0">
            <div class="d-flex justify-content-end">
              <button class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-cloud-download"></i> Download
              </button>
              <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i> Excluir
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="row">
      <div class="col-12 text-center">
        <a href="/private/generate_doc.php" class="btn btn-primary">
          Gerar Documento
        </a>
      </div>
    </div>

  </div>

  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
</body>

</html>