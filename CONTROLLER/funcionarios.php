<?php
require_once "../MODEL/conexao.php";

if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM funcionarios WHERE id = '$id'";

    if (mysqli_query($conexao, $sql)) {
        header("Location: funcionarios.php");
        exit();
    } else {
        echo "Erro ao excluir o funcionário: " . mysqli_error($conexao);
    }
}

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $cargo = $_POST['cargo'];

    $sql = "UPDATE funcionarios SET nome = '$nome', idade = '$idade', cargo = '$cargo' WHERE id = '$id'";

    if (mysqli_query($conexao, $sql)) {
        header("Location: funcionarios.php");
        exit();
    } else {
        echo "Erro ao atualizar o funcionário: " . mysqli_error($conexao);
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Funcionários</title>
</head>

<body>
    <div class="p-3 mb-2 bg-dark text-white mb-3">
        SISTEMA ASSIS NIGHT CLUB
        <div class="d-flex flex-row-reverse">
            <div class="p-2">
                <a class="btn btn-danger" href="../VIEW/painel.php"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a class="btn btn-success" href="criar_funcionario.php"><i class="bi bi-plus-lg"></i>Adicionar
                    Funcionário</a>
            </div>
        </div>
    </div>
    <h1 class="text-center mb-3">Funcionários</h1>
    <div class="container">
        <table class="table table-striped table-bordered">
            <tr class="text-center">
                <th>ID</th>
                <th>Nome</th>
                <th>Idade</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
            <?php
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_assoc($resultado)) {
                    echo "<tr class='text-center'>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nome"] . "</td>";
                    echo "<td>" . $row["idade"] . "</td>";
                    echo "<td>" . $row["cargo"] . "</td>";
                    echo "<td>
                        <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editarFuncionarioModal" . $row['id'] . "'><i class='bi bi-pencil'></i></button>
                        <a href='?id=" . $row['id'] . "' class='btn btn-danger'><i class='bi bi-trash'></i></a>
                    </td>";
                    echo "</tr>";


                    echo "<div class='modal fade' id='editarFuncionarioModal" . $row['id'] . "' tabindex='-1' aria-labelledby='editarFuncionarioModalLabel" . $row['id'] . "' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='editarFuncionarioModalLabel" . $row['id'] . "'>Editar Funcionário</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Fechar'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <form method='post'>
                                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                                            <div class='mb-3'>
                                                <label for='nome" . $row['id'] . "' class='form-label'>Nome</label>
                                                <input type='text' class='form-control' id='nome" . $row['id'] . "' name='nome' value='" . $row['nome'] . "'>
                                            </div>
                                            <div class='mb-3'>
                                                <label for='idade" . $row['id'] . "' class='form-label'>Idade</label>
                                                <input type='text' class='form-control' id='idade" . $row['id'] . "' name='idade' value='" . $row['idade'] . "'>
                                            </div>
                                            <div class='mb-3'>
                                                <label for='cargo" . $row['id'] . "' class='form-label'>Cargo</label>
                                                <input type='text' class='form-control' id='cargo" . $row['id'] . "' name='cargo' value='" . $row['cargo'] . "'>
                                            </div>
                                            <button type='submit' name='editar' class='btn btn-success'><i class='bi bi-check-lg'></i> Confirmar</button>
                                        </form>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-danger' data-bs-dismiss='modal'><i class='bi bi-arrow-left'></i></i> Voltar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
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