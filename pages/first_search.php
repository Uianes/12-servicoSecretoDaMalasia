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
  <title> - Pesquisa</title>
</head>

<body class="bg-light">

  <?php render_toast(); ?>

  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
      <div class="col-12 mx-auto">

        <div class="text-center mb-3">
          <h3>Pesquise sobre um item</h3>
        </div>

        <form action="../private/search.php" method="post">

          <div class="form-floating">
            <textarea class="form-control"
              oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"
              style="overflow: hidden; resize: none; min-height: 120px;"
              placeholder="Pesquise sobre um item"
              name="user_prompt"
              id="input_pesquisa"
              required></textarea>
            <label for="input_pesquisa">Pesquise sobre um item</label>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary btn-lg d-block mx-auto">Pesquisar</button>
          </div>

        </form>

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