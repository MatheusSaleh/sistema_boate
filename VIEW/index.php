<?php
include('../MODEL/conexao.php');

if(isset($_POST['email']) || isset($_POST['senha'])){
    if(strlen($_POST['email']) == 0){
        echo "Preencha seu e-mail";
    } else if(strlen($_POST['senha']) == 0){
        echo "Preencha sua senha";
    } else{
        /** @noinspection PhpUndefinedVariableInspection */
        $email = $conexao->real_escape_string($_POST['email']);
        $senha = $conexao->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $sql_query = $conexao->query($sql_code) or die("Falha na execucao do codigo SQL: ". $conexao->error);
        $quantidade = $sql_query->num_rows;

        if($quantidade == 1){

            $usuario = $sql_query->fetch_assoc();

            if(!isset($_SESSION)){
                session_start();
            }

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['name'] = $usuario['nome'];

            header("Location: painel.php");

        } else{
            echo "Falha ao logar! E-mail ou senha incorretos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/login.css">
    <title>Pagina de Login</title>
</head>

<body class="bg-success">

    <div class="p-3 mb-2 bg-dark text-white mb-3">SISTEMA ASSIS NIGHT CLUB</div>
    <div class="container-fluid w-50 p-4 rounded bg-white bg-opacity-75">
        <form action="" method="POST">
            <p>
                <label>
                    Digite o seu e-mail:
                </label>
                <br>
                <input type="text" class="form-control" name="email">
            </p>
            <p>
                <label>
                    Digite sua senha:
                </label>
                <br>
                <input type="password" class="form-control" name="senha">
            </p>
            <p>
                <button type="submit" class="btn btn-success"><i class="bi bi-box-arrow-in-right"></i> Fazer Login</button>
            </p>
        </form>
    </div>
</body>

</html>