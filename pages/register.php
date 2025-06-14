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
  <title>Cadastro de Usuário - Ker.IA</title>
  <link rel="icon" type="image/x-icon" href="../assets/logo_ker.svg">
</head>

<body class="bg-light">
  <?php render_toast(); ?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg rounded-4 p-4">
          <div class="row">
            <div class="col-md-5 d-flex flex-column align-items-center justify-content-center border-end border-light-subtle">
              <img src="../assets/logo_ker.svg" alt="Ker.IA Logo" class="img-fluid mb-3" style="max-width: 200px;">
            </div>

            <div class="col-md-7 p-3">
              <form action="../actions/register.php" method="post" id="registerForm">
                <div class="mb-4">
                  <label for="email" class="form-label">E-mail</label>
                  <div class="input-group">
                    <input type="email" class="form-control rounded-3" id="email" name="email" required>
                    <span class="input-group-text bg-transparent border-start-0">
                      <i class="bi bi-person-fill text-secondary"></i>
                    </span>
                  </div>
                </div>

                <div class="mb-4">
                  <label for="nome" class="form-label">Nome Completo</label>
                  <input type="text" class="form-control rounded-3" id="nome" name="nome" required>
                </div>

                <div class="mb-3">
                  <label for="senha" class="form-label">Senha</label>
                  <div class="input-group">
                    <input id="senha" type="password" class="form-control rounded-3" name="senha" required>
                    <button type="button" class="input-group-text bg-transparent border-start-0 password-toggle" data-target="senha">
                      <i class="bi bi-eye text-secondary" aria-hidden="true"></i>
                    </button>
                  </div>
                  <div class="form-text text-muted">A senha deve ter pelo menos 8 caracteres</div>
                  <div id="senhaError" class="invalid-feedback">A senha deve ter pelo menos 8 caracteres</div>
                </div>

                <div class="mb-4">
                  <label for="confirma_senha" class="form-label">Confirmar Senha</label>
                  <div class="input-group">
                    <input type="password" class="form-control rounded-3" id="confirma_senha" name="confirma_senha" required>
                    <button type="button" class="input-group-text bg-transparent border-start-0 password-toggle" data-target="confirma_senha">
                      <i class="bi bi-eye text-secondary" aria-hidden="true"></i>
                    </button>
                  </div>
                  <div id="confirmSenhaError" class="invalid-feedback">As senhas não coincidem</div>
                </div>

                <div class="mb-4">
                  <label for="cargo" class="form-label">Cargo</label>
                  <input type="text" class="form-control rounded-3" id="cargo" name="cargo" required>
                </div>

                <div class="mb-4">
                  <label for="local" class="form-label">Local/Secretaria</label>
                  <input type="text" class="form-control rounded-3" id="local" name="local" required>
                </div>

                <div class="d-grid mb-3">
                  <button type="submit" name="submit_register" id="submitBtn" class="btn btn-primary btn-lg rounded-3" disabled>Cadastrar</button>
                </div>

                <div class="text-end">
                  <a href="../pages/login.php" class="text-decoration-none text-primary">Voltar para Login</a>
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
      // Password toggle functionality
      const toggleButtons = document.querySelectorAll('.password-toggle');
      toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
          const targetId = this.getAttribute('data-target');
          const passwordInput = document.getElementById(targetId);
          
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          
          this.querySelector('i').classList.toggle('bi-eye');
          this.querySelector('i').classList.toggle('bi-eye-slash');
        });
      });

      // Form validation
      const senhaInput = document.getElementById('senha');
      const confirmaSenhaInput = document.getElementById('confirma_senha');
      const submitBtn = document.getElementById('submitBtn');
      const senhaError = document.getElementById('senhaError');
      const confirmSenhaError = document.getElementById('confirmSenhaError');

      function validateForm() {
        let isValid = true;
        
        // Check password length
        if (senhaInput.value.length < 8) {
          senhaInput.classList.add('is-invalid');
          senhaInput.classList.remove('is-valid');
          senhaError.style.display = 'block';
          isValid = false;
        } else {
          senhaInput.classList.remove('is-invalid');
          senhaInput.classList.add('is-valid');
          senhaError.style.display = 'none';
        }
        
        // Check if passwords match
        if (senhaInput.value !== confirmaSenhaInput.value || confirmaSenhaInput.value === '') {
          confirmaSenhaInput.classList.add('is-invalid');
          confirmaSenhaInput.classList.remove('is-valid');
          confirmSenhaError.style.display = 'block';
          isValid = false;
        } else {
          confirmaSenhaInput.classList.remove('is-invalid');
          confirmaSenhaInput.classList.add('is-valid');
          confirmSenhaError.style.display = 'none';
        }
        
        // Enable or disable submit button
        submitBtn.disabled = !isValid;
        
        return isValid;
      }

      // Add event listeners
      senhaInput.addEventListener('input', validateForm);
      confirmaSenhaInput.addEventListener('input', validateForm);

      // Initial form validation
      validateForm();
    });
  </script>
</body>

</html>