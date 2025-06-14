<?php
session_start();
require_once '../private/db_connection.php';
require_once '../includes/toast.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_login'])) {
  $user_login = trim($_POST['user_login']);
  $password_login = trim($_POST['password_login']);

  if (empty($user_login) || empty($password_login)) {
    redirect_with_toast('../pages/login.php', 'Todos os campos são obrigatórios', 'danger');
  }

  try {
    $conn = open_connection();

    $result = mysqli_execute_query($conn, "SELECT * FROM user WHERE email = ?", [$user_login]);

    if (!$result) {
      redirect_with_toast('../pages/login.php', 'Erro ao consultar o banco de dados', 'danger');
    }

    if (mysqli_num_rows($result) === 0) {
      redirect_with_toast('../pages/login.php', 'Usuário ou senha invalidos', 'danger');
    }

    $user = mysqli_fetch_assoc($result);
    if (!password_verify($password_login, $user['senha'])) { 
      redirect_with_toast('../pages/login.php', 'Usuário ou senha invalidos', 'danger');
    }

    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_nome'] = $user['nome'];
    $_SESSION['user_cargo'] = $user['cargo'];
    $_SESSION['user_secretaria'] = $user['local'];

    close_connection($conn);
    redirect_with_toast('../pages/first_search.php', 'Login realizado com sucesso', 'success');
  } catch (Exception $e) {
    redirect_with_toast('../pages/login.php', 'Erro ao processar o login', 'danger');
  }
} else {
  redirect_with_toast('../index.php', 'Método de requisição inválido', 'danger');
}
