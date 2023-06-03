<?php 
require_once "../MODEL/conexao.php";

if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

$sql = "SELECT * FROM funcionarios";
$resultado = mysqli_query($conexao, $sql);

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
    <title>Funcionários</title>
</head>
<body>
<div class="p-3 mb-2 bg-dark text-white mb-3">
        SISTEMA ASSIS NIGHT CLUB
        <div class="d-flex flex-row-reverse">
            <div class="p-2">
                <a class="btn btn-danger" href="../VIEW/painel.php"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a class="btn btn-success" href="criar_funcionario.php"><i class="bi bi-plus-lg"></i>Adicionar Funcionário</a>
            </div>
        </div>

    </div>
    <h1 class="text-center mb-3">Funcionários</h1>
    <div class="container">
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Idade</th>
                <th>Cargo</th>
                <th>Ações</th> 
            </tr>
            <?php
                if(mysqli_num_rows($resultado) > 0){
                    while($row = mysqli_fetch_assoc($resultado)){
                        echo "<tr>";
                        echo "<td>" . $row["id"]. "</td>";
                        echo "<td>" . $row["nome"]. "</td>";
                        echo "<td>" . $row["idade"]. "</td>";
                        echo "<td>" . $row["cargo"]. "</td>";
                        echo "<td>
                                <button class='btn btn-primary'><i class='bi bi-pencil'></i></button>
                                <button class='btn btn-danger'><i class='bi bi-trash'></i></button>
                              </td>"; 
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Não foram encontrados registros.</td></tr>";
                }
                mysqli_close($conexao);
            ?>
        </table>
    </div>
</body>
</html>
