<?php
session_start();
require 'conexao.php';
date_default_timezone_set('America/Sao_Paulo'); 

$session_duration = 60 * 15; 


if (isset($_SESSION['email'])) {
    $emailSessao = $_SESSION['email'];

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $session_duration) {
        session_unset();     
        session_destroy();   
        header("Location: index.php");
        exit();
    }

    $_SESSION['LAST_ACTIVITY'] = time(); 
} else {
    header("Location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja</title>
    <link rel="stylesheet" href="style_loja.css">
    
        
       
</head>
<body>
    <header>
    <h1>Área do candidato</h1>
    <div class="user-menu">
        <h2>Bem-vindo,</h2>
        <div class="dropdown">
            <button class="dropbtn"><?php echo htmlspecialchars($emailSessao); ?></button>
            <div class="dropdown-content">
                <a href="inscricao.php">Inscrições</a>
                <a href="minhaconta.php">Minha Conta</a>
                <a href="consumirLoja.php">Loja</a>
            </div>
        </div>
        <form action="logout.php"><button>Sair</button></form>
    </div>
        
    </header>

    <?php 
$urlAPI = "https://fakestoreapi.com/products";

$imagens = file_get_contents($urlAPI);

$produtos = json_decode($imagens, true);
$produtos_eletronicos = array_filter($produtos, function($produto) {
    return $produto["category"] === "electronics";
});
?>
<div class="container-produtos">
<?php
foreach($produtos_eletronicos as $p){
    ?>
    <div class="card-produto">
        <img src="<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>">
        <h3><?php echo htmlspecialchars($p['title']); ?></h3>
        <p class="preco">R$<?php echo ($p['price']); ?></p>
    </div>
    <?php
}
?>
</div>
<?php

echo "<br>";

?>
  
</body>
<script>
    // Redireciona o usuário após 15min 
    setTimeout(function(){
        alert('Sua sessão expirou. Você será redirecionado.');
        window.location.href = 'index.php';
    }, 900000); // 15 minutos em milisegundos 
</script>
</html>
