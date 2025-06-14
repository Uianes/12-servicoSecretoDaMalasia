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
  <title>Ker.AI - Dashboard</title>
  <link rel="icon" type="image/x-icon" href="../assets/logo_ker.svg">
  <style>
    .main-container {
      display: flex;
      border: 1px solid #ddd;
      border-radius: 10px;
      overflow: hidden;
      height: 90vh;
      max-width: 1200px;
      margin: 0 auto;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .content-area {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      font-size: 16px;
    }

    .sidebar {
      width: 300px;
      background-color: #fff;
      border-left: 1px solid #ddd;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
    }

    .logo {
      margin-bottom: 30px;
      width: 160px;
    }

    .sidebar-button {
      width: 100%;
      margin-bottom: 15px;
      padding: 10px 20px;
      border-radius: 25px;
      border: 1px solid #50629c;
      background-color: #fff;
      color: #50629c;
      text-align: center;
      text-decoration: none;
      font-weight: 500;
    }

    .sidebar-button:hover {
      background-color: #f0f2f8;
    }

    /* Scrollbar styling */
    .content-area::-webkit-scrollbar {
      width: 8px;
    }

    .content-area::-webkit-scrollbar-thumb {
      background-color: #aaa;
      border-radius: 10px;
    }

    .content-area::-webkit-scrollbar-track {
      background-color: #f0f0f0;
    }
  </style>
</head>

<body class="bg-light">
  <?php render_toast(); ?>

  <div class="position-fixed top-0 end-0 p-3">
    <a href="../actions/logout.php" class="btn btn-sm btn-outline-danger">
      <i class="bi bi-box-arrow-right"></i> Sair
    </a>
  </div>

  <div class="container py-4">
    <div class="main-container">
      <!-- Scrollable Content Area -->
      <div class="content-area">
        <?php
        if (!empty($results)) {
          echo formatAiText($results);
        } else {
          // Placeholder content to demonstrate scrolling
          for ($i = 0; $i < 15; $i++) {
            echo "<p>aqui vai o resultado da busca</p>";
          }
        }
        ?>
      </div>

      <!-- Static Sidebar -->
      <div class="sidebar">
        <img src="../assets/logo_ker.svg" alt="Ker.IA Logo" class="logo">

        <a href="../private/price.php" class="sidebar-button">Pesquisar Preço Médio</a>
        <a href="./first_search.php" class="sidebar-button">Voltar</a>
        <a href="../private/generate_doc.php" class="sidebar-button">Download</a>
      </div>
    </div>
  </div>

  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
</body>

</html>