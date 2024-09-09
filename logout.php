Nessa página que o usuário é direcionado ao clicar no boão sair ,a sessão é encerrada
<?php 
session_start();
session_destroy();
header ("location: index.php");
?>