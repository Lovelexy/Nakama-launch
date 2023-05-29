<?php
// Configure o prefixo do assunto e o destinatário aqui
$subjectPrefix = 'Contato';
$emailTo = ' projetos.amorin@gmail.com'; // Substitua com o seu endereço de e-mail do Outlook

$errors = array(); // array para armazenar os erros de validação
$data = array(); // array para retornar os dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = stripslashes(trim($_POST['email']));
  $spam = $_POST['textfield-nl'];

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email é inválido.';
  }

  // se houver algum erro no array de erros, retorna um booleano de sucesso ou falso
  if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
  } else {
    $subject = "Cadastro - $subjectPrefix";
    $body = '
      Email: ' . $email . '<br />
    ';

    $headers = "MIME-Version: 1.1" . PHP_EOL;
    $headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;
    $headers .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
    $headers .= "Date: " . date('r', $_SERVER['REQUEST_TIME']) . PHP_EOL;
    $headers .= "Message-ID: <" . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL;
    $headers .= "From: " . "=?UTF-8?B?" . "?=" . "$email" . PHP_EOL;
    $headers .= "Return-Path: $emailTo" . PHP_EOL;
    $headers .= "Reply-To: $email" . PHP_EOL;
    $headers .= "X-Mailer: PHP/" . phpversion() . PHP_EOL;
    $headers .= "X-Originating-IP: " . $_SERVER['SERVER_ADDR'] . PHP_EOL;

    if (empty($spam)) {
      if (mail($emailTo, "=?utf-8?B?" . base64_encode($subject) . "?=", $body, $headers)) {
        $data['success'] = true;
        $data['confirmation'] = 'Valeu pelo cadastro! Você será avisado quando o site for inaugurado. ';
      } else {
        $data['success'] = false;
        $data['errors']['email'] = 'Erro ao enviar o e-mail. Por favor, tente novamente.';
      }
    } else {
      $data['success'] = false;
      $data['errors']['email'] = 'Erro ao enviar o e-mail. Por favor, tente novamente.';
    }
  }

  // retorna todos os dados para uma chamada AJAX
  header('Content-type: application/json');
  echo json_encode($data);
  exit();
}
?>
