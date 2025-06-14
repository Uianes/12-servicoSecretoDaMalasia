<?php
session_start();
require_once '../includes/toast.php';
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include '../includes/bootstrap_styles.php' ?>
  <link rel="stylesheet" href="../assets/css/bootstrap_custom.css">
  <title>Login - Ker.IA</title>
  <link rel="icon" type="image/x-icon" href="../assets/logo_ker.svg">
</head>

<body class="bg-light">
  <?php render_toast(); ?>

  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
      <div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
        <div class="card shadow-lg rounded-4 p-4">
          <div class="row">
            <div class="col-md-5 d-flex flex-column align-items-center justify-content-center border-end border-light-subtle">
              <img src="../assets/logo_ker.svg" alt="Ker.IA Logo" class="img-fluid mb-3" style="max-width: 200px;">
            </div>

            <div class="col-md-7 p-3">
              <form method="POST" action="../actions/login.php">
                <div class="mb-4">
                  <label for="floating_user" class="form-label">E-mail</label>
                  <div class="input-group">
                    <input type="email" name="user_login" class="form-control rounded-3" id="floating_user" required>
                    <span class="input-group-text bg-transparent border-start-0">
                      <i class="bi bi-person-fill text-secondary"></i>
                    </span>
                  </div>
                </div>

                <div class="mb-4">
                  <label for="password_login" class="form-label">Senha</label>
                  <div class="input-group">
                    <input id="password_login" type="password" name="password_login" class="form-control rounded-3" required>
                    <button type="button" class="input-group-text bg-transparent border-start-0" id="togglePassword">
                      <i class="bi bi-eye text-secondary" aria-hidden="true"></i>
                    </button>
                  </div>
                </div>

                <div class="d-grid mb-3">
                  <button type="submit" name="submit_login" class="btn btn-primary btn-lg rounded-3">Entrar</button>
                </div>

                <div class="text-end d-flex justify-content-between">
                  <a href="../pages/register.php" class="text-decoration-none text-primary">Criar Conta</a>
                  <a href="#" class="text-decoration-none text-primary">Esqueci minha Senha</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require_once '../includes/bootstrap_script.php' ?>
  <script src="../assets/js/toast.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password_login');

      togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
      });
    });
  </script>
</body>

</html>