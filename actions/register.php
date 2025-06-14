<?php
session_start();
require_once '../private/db_connection.php';
require_once '../includes/toast.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_register'])) {
  // Get and sanitize form data
  $email = trim($_POST['email']);
  $nome = trim($_POST['nome']);
  $senha = trim($_POST['senha']);
  $confirma_senha = trim($_POST['confirma_senha']);
  $cargo = trim($_POST['cargo']);
  $local = trim($_POST['local']);

  // Validate required fields
  if (empty($email) || empty($nome) || empty($senha) || empty($confirma_senha) || empty($cargo) || empty($local)) {
    redirect_with_toast('../pages/register.php', 'Todos os campos são obrigatórios', 'danger');
  }

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_toast('../pages/register.php', 'Formato de email inválido', 'danger');
  }

  // Check if passwords match
  if ($senha !== $confirma_senha) {
    redirect_with_toast('../pages/register.php', 'As senhas não coincidem', 'danger');
  }

  // Password strength check (optional)
  if (strlen($senha) < 8) {
    redirect_with_toast('../pages/register.php', 'A senha deve ter pelo menos 8 caracteres', 'danger');
  }

  try {
    $conn = open_connection();
    
    // Check if email already exists
    $check_result = mysqli_execute_query($conn, "SELECT id FROM user WHERE email = ?", [$email]);
    if (mysqli_num_rows($check_result) > 0) {
      redirect_with_toast('../pages/register.php', 'Este email já está cadastrado', 'danger');
    }

    // Hash password
    $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
    
    // Insert new user
    $insert_result = mysqli_execute_query(
      $conn, 
      "INSERT INTO user (email, nome, senha, cargo, local) VALUES (?, ?, ?, ?, ?)",
      [$email, $nome, $hashed_password, $cargo, $local]
    );

    if (!$insert_result) {
      redirect_with_toast('../pages/register.php', 'Erro ao registrar usuário', 'danger');
    }

    close_connection($conn);
    redirect_with_toast('../pages/login.php', 'Cadastro realizado com sucesso! Faça login para continuar.', 'success');
  } catch (Exception $e) {
    redirect_with_toast('../pages/register.php', 'Erro ao processar o cadastro', 'danger');
  }
} else {
  redirect_with_toast('../index.php', 'Método de requisição inválido', 'danger');
}