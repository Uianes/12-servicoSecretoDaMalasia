<?php
require_once '../includes/toast.php';
session_start();
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include '../includes/bootstrap_styles.php' ?>
  <link rel="stylesheet" href="../assets/css/bootstrap_custom.css">
  <title>Ker.IA - Pesquisa</title>
  <link rel="icon" type="image/x-icon" href="../assets/logo_ker.svg">
  <style>
    .search-button {
      top: 50%;
      right: 8px;
      transform: translateY(-50%);
    }
  </style>
</head>

<body class="bg-light">
  <?php render_toast(); ?>

  <div class="container min-vh-100 d-flex flex-column justify-content-center">
    <div class="row mb-5">
      <div class="col-12 text-center">
        <img src="../assets/logo_ker.svg" alt="Ker.IA Logo" style="max-width: 200px;">
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="mx-auto" style="max-width: 800px;">
          <form action="../private/search.php" method="post" class="position-relative">
            <input type="text" class="form-control form-control-lg border border-2 rounded-pill py-3 ps-4 pe-5"
              name="user_prompt" id="input_pesquisa" placeholder="Qual Produto Deseja Licitar?"
              style="border-color: #3f51b5 !important;" required>
            <button type="submit"
              class="btn position-absolute search-button rounded-circle d-flex justify-content-center align-items-center"
              style="width: 50px; height: 50px; background-color: #3f51b5;">
              <i class="bi bi-search text-white"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Help button that triggers the offcanvas -->
  <button class="btn position-fixed bottom-0 start-0 ms-4 mb-4 fs-5 p-0 bg-transparent border-0"
    data-bs-toggle="offcanvas" data-bs-target="#helpOffcanvas">
    Precisa de <strong class="text-primary">Ajuda?</strong>
  </button>

  <!-- Offcanvas component for help -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="helpOffcanvas" aria-labelledby="helpOffcanvasLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="helpOffcanvasLabel">Como usar a Pesquisa</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="mb-4">
        <h6 class="fw-bold">O que inserir na pesquisa</h6>
        <p>Digite o nome do produto ou serviço que você deseja incluir em um edital de licitação.</p>
        <p>Exemplos:</p>
        <ul>
          <li>Computador Desktop i7</li>
          <li>Serviço de limpeza predial</li>
          <li>Fornecimento de material de escritório</li>
          <li>Contratação de empresa de segurança</li>
        </ul>
      </div>

      <div class="mb-4">
        <h6 class="fw-bold">Múltiplos itens em um único edital</h6>
        <p>Você pode solicitar múltiplos produtos e/ou serviços em uma única pesquisa. Todos serão incluídos no mesmo edital de licitação.</p>
        <p>Exemplos:</p>
        <ul>
          <li>10 computadores, 5 impressoras e 20 monitores</li>
          <li>Serviço de limpeza e vigilância para prédio administrativo</li>
          <li>Material de escritório e equipamentos de informática</li>
        </ul>
      </div>

      <div class="mb-4">
        <h6 class="fw-bold">Como ser específico</h6>
        <p>Para melhores resultados, seja específico sobre o que você precisa. Inclua:</p>
        <ul>
          <li>Especificações técnicas importantes</li>
          <li>Quantidade aproximada (quando relevante)</li>
          <li>Tipo de serviço ou produto</li>
        </ul>
      </div>

      <div class="mb-4">
        <h6 class="fw-bold">O que acontecerá</h6>
        <p>O sistema gerará automaticamente um edital de licitação completo (Pregão Eletrônico),
          incluindo o Termo de Referência detalhado para todos os itens especificados em um único documento.</p>
      </div>

      <div class="alert alert-info">
        <strong>Nota:</strong> O resultado gerado é um ponto de partida e pode precisar de revisões
        ou ajustes para atender às necessidades específicas da sua organização e cumprir todas as
        exigências legais aplicáveis.
      </div>
    </div>
  </div>

  <?php
  require_once '../includes/bootstrap_script.php';
  require_once '../includes/spinner.php';
  ?>
  <script src="../assets/js/toast.js"></script>
  <script src="../assets/js/spinner.js"></script>
</body>

</html>