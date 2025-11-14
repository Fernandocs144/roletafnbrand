<?php
// ================== CONFIGURAÇÃO DA BASE DE DADOS ==================
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'fnbrand';
$port = 3306;

// ================== CONEXÃO ==================
$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Erro na ligação à base de dados: " . $conn->connect_error);
}

// ================== RECEBER DADOS DO FORMULÁRIO ==================
$nome   = $_POST['nome'] ?? '';
$email  = $_POST['email'] ?? '';
$codigo = $_POST['codigo'] ?? '';
$premio = $_POST['premio'] ?? '';

if (empty($nome) || empty($email) || empty($codigo) || empty($premio)) {
    die("Dados em falta.");
}

// ================== INSERIR NA BASE DE DADOS ==================
$stmt = $conn->prepare("INSERT INTO emails_roleta (nome, email, codigo, premio) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $email, $codigo, $premio);

if ($stmt->execute()) {
    // ================== ENVIAR EMAIL ==================
    $assunto = "🎁 O teu prémio FN Brand chegou!";
    $mensagem = "
    <html>
    <body style='font-family:Poppins,Arial,sans-serif; background:#fafafa; padding:20px;'>
      <div style='max-width:600px; margin:auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1)'>
        <h2 style='color:#d19b23; text-align:center;'>Parabéns, {$nome}! 🎉</h2>
        <p style='font-size:16px; color:#333; text-align:center;'>Ganhaste <strong>{$premio}</strong> na Roda da Sorte FN Brand!</p>
        <div style='text-align:center; margin:20px 0;'>
          <span style='font-size:18px; background:#000; color:#fff; padding:10px 18px; border-radius:8px; font-weight:bold;'>
            CÓDIGO: {$codigo}
          </span>
        </div>
        <p style='color:#555; text-align:center;'>Podes usar este código em compras superiores a 30€.<br> Aproveita a tua sorte e usa-o já!</p>
        <hr style='margin:20px 0;'>
        <p style='font-size:12px; color:#888; text-align:center;'>FN Brand © ".date('Y')."</p>
      </div>
    </body>
    </html>";

    // Cabeçalhos de email
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: FN Brand <no-reply@fnbrand.local>\r\n";

    // Função mail()
    if (mail($email, $assunto, $mensagem, $headers)) {
        echo "success";
    } else {
        echo "guardado_sem_email";
    }
} else {
    echo "erro_bd";
}

$stmt->close();
$conn->close();
?>
