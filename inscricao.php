<?php
session_start();

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


function carregarInscricoes($email) {
    $inscricoes = [];
    $filename = "inscricoes_$email.txt";
    if (file_exists($filename)) {
        $inscricoes = file($filename, FILE_IGNORE_NEW_LINES);
    }
    return $inscricoes;
}


function salvarInscricao($email, $curso) {
    $filename = "inscricoes_$email.txt";
    $horario = date('Y-m-d H:i:s');
    $conteudo = "$horario - Curso: $curso\n";
    
    $file = fopen($filename, 'a'); 
    fwrite($file, $conteudo); 
    fclose($file); 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $unidade = $_POST['unidade'];
    $periodos = isset($_POST['periodo']) ? (array)$_POST['periodo'] : []; 

    $inscricoes = carregarInscricoes($emailSessao);

    
    $jaInscrito = false;
    foreach ($inscricoes as $inscricao) {
        if (strpos($inscricao, "Curso: $curso") !== false) {
            $jaInscrito = true;
            break;
        }
    }

    if (!$jaInscrito) {
        salvarInscricao($emailSessao, $curso);

        
        $_SESSION['ultima_inscricao'] = [
            'name' => $name,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'endereco' => $endereco,
            'cidade' => $cidade,
            'estado' => $estado,
            'unidade' => $unidade,
            'periodos' => implode(', ', $periodos), 
            'curso' => $curso,
            'horario' => date('Y-m-d H:i:s')
        ];
    } else {
        echo "<script>alert('Você já está inscrito neste curso!');</script>";
    }
}


$inscricoesAtualizadas = carregarInscricoes($emailSessao);
$ultimaInscricao = isset($_SESSION['ultima_inscricao']) ? $_SESSION['ultima_inscricao'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        header {
            width: 100%;
            background-color: #EEEBE6;
            color: white;
            padding: 10px 20px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header .user {
            padding-left: 0px;
        }

        header .user-menu {
            display: flex;
            align-items: center;
        }

        header .user-menu select {
            background-color: #EEEBE6;
            border: none !important;
            padding: 5px;
            font-size: 16px;
            margin-right: 10px;
            cursor: pointer;
        }
        header .user-menu select option :hover {
            background-color: #FF4500;
          
        }


        header button {
            background-color: #FF6347;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        header button:hover {
            background-color: #FF4500;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 70px; 
            width: 90%;
            max-width: 1200px;
        }

        main {
            width: 65%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
            box-sizing: border-box;
        }

        aside {
            width: 30%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        h1, h2 {
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form label {
            font-size: 16px;
            margin-bottom: 5px;
            color: #555;
        }

        form input[type="text"],
        form input[type="email"],
        form select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: calc(100% - 22px);
            box-sizing: border-box;
        }

        form input[type="radio"],
        form input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .radio-group, .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .radio-group label, .checkbox-group label {
            margin: 0;
            margin-left: 5px;
            font-size: 16px;
            color: #555;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        aside h2 {
            margin-bottom: 15px;
        }

        aside span {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>Área do candidato</h1>
        <div class="user-menu">
            <h2>Bem-vindo,</h2>
            <select name="user-action" onchange="handleMenuChange(this)">
                <option value="" disabled selected> <?php echo htmlspecialchars($emailSessao); ?></option>
                <option value="inscricao.php">Inscrições</option>
                <option value="minha_conta.php">Minha Conta</option>
            </select>
        <!-- <h1 class="user"><?php echo htmlspecialchars($emailSessao); ?></h1> -->
        <form action="logout.php"><button>Sair</button></form>
        
    </header>

    <div class="container">
        <main>
            <form id="form" method="POST" action="">
                <label for="name">Nome</label>
                <input type="text" name="name" required>

                <label for="lastname">Sobrenome</label>
                <input type="text" name="lastname" required>

                <label for="username">Usuário</label>
                <input type="text" name="username" required>

                <label for="email">E-mail</label>
                <input type="email" name="email" required>

                <label for="endereco">Endereço</label>
                <input type="text" name="endereco" required>

                <label for="cidade">Cidade</label>
                <input type="text" name="cidade" required>

                <label for="estado">Estado</label>
                <select name="estado" required>
                    <?php 
                    $estados = array(
                        'AC' => 'Acre',
                        'AL' => 'Alagoas',
                        'AP' => 'Amapá',
                        'AM' => 'Amazonas',
                        'BA' => 'Bahia',
                        'CE' => 'Ceará',
                        'DF' => 'Distrito Federal',
                        'ES' => 'Espírito Santo',
                        'GO' => 'Goiás',
                        'MA' => 'Maranhão',
                        'MT' => 'Mato Grosso',
                        'MS' => 'Mato Grosso do Sul',
                        'MG' => 'Minas Gerais',
                        'PA' => 'Pará',
                        'PB' => 'Paraíba',
                        'PR' => 'Paraná',
                        'PE' => 'Pernambuco',
                        'PI' => 'Piauí',
                        'RJ' => 'Rio de Janeiro',
                        'RN' => 'Rio Grande do Norte',
                        'RS' => 'Rio Grande do Sul',
                        'RO' => 'Rondônia',
                        'RR' => 'Roraima',
                        'SC' => 'Santa Catarina',
                        'SP' => 'São Paulo',
                        'SE' => 'Sergipe',
                        'TO' => 'Tocantins'
                    );

                    foreach ($estados as $sigla => $nome) {
                        echo "<option value=\"$sigla\">$nome</option>";
                    }
                    ?>
                </select>

                <label><h3>Unidade:</h3></label>
                <div class="radio-group">
                    <input type="radio" name="unidade" value="Sede" required> Sede
                </div>
                <div class="radio-group">
                    <input type="radio" name="unidade" value="Extensao" required> Extensão
                </div>

                <label for="periodo"><h3>Período:</h3></label>
                <div class="checkbox-group">
                    <input type="checkbox" name="periodo[]" value="vespertino"> Vespertino
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="periodo[]" value="matutino"> Matutino
                </div>

                <label for="curso">Curso</label>
                <select name="curso" required>
                    <option value="GE">GE</option>
                    <option value="ADS">ADS</option>
                    <option value="DSM">DSM</option>
                    <option value="PQ">PQ</option>
                </select>

                <input type="submit" value="Enviar">
            </form>
        </main>

        <aside>
            <h2>Resumo</h2> <br>
            Nome: 
            <?php 
            if ($ultimaInscricao) {
                echo "<span>{$ultimaInscricao['name']} {$ultimaInscricao['lastname']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
            Usuário: 
            <?php 
            if ($ultimaInscricao) {
                echo "<span>{$ultimaInscricao['username']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
            E-mail: 
            <?php 
            if ($ultimaInscricao) {
                echo "<span>{$ultimaInscricao['email']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
            Endereço: 
            <?php 
            if ($ultimaInscricao) {
                echo "<span>Logradouro: {$ultimaInscricao['endereco']} Cidade: {$ultimaInscricao['cidade']} Estado: {$ultimaInscricao['estado']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
            Unidade e Período: 
            <?php 
            if ($ultimaInscricao) {
                echo "<span>Unidade: {$ultimaInscricao['unidade']}  <br> Período: {$ultimaInscricao['periodos']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
            Curso:
            <?php 
            if ($ultimaInscricao) {
                echo "<span>{$ultimaInscricao['curso']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
            Data da Inscrição: 
            <?php 
            if ($ultimaInscricao) {
                echo "<span>{$ultimaInscricao['horario']}</span>";
            } else {
                echo "<span>Não disponível</span>";
            }
            ?>
        </aside>

        <aside>
            <h2>Inscrições</h2>
            <?php
            if (!empty($inscricoesAtualizadas)) {
                foreach ($inscricoesAtualizadas as $inscricao) {
                    echo "<span>" . htmlspecialchars($inscricao) . "</span>";
                }
            } else {
                echo "<span>Não há inscrições registradas.</span>";
            }
            ?>
        </aside>
    </div>
</body>
<script>
    document.getElementById('form').addEventListener('submit', function(event) {
        const checkboxes = document.querySelectorAll('input[name="periodo[]"]');
        const checked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (!checked) {
            alert('Pelo menos um período deve ser selecionado.');
            event.preventDefault(); 
        }
    });
</script>
<script>
    // Redireciona o usuário após 15min 
    setTimeout(function(){
        alert('Sua sessão expirou. Você será redirecionado.');
        window.location.href = 'index.php';
    }, 900000); // 15 minutos em milisegundos 
</script>
</html>
