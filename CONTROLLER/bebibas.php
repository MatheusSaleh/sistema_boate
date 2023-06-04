<?php
require_once "../MODEL/conexao.php";

if (!$conexao) {
    die("Falha na conexao com o banco de dados" . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM bebidas WHERE id = '$id'";

    if (mysqli_query($conexao, $sql)) {
        header("Location: bebibas.php");
        exit();
    } else {
        echo "Erro ao excluir bebida";
    }
}

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $preco = $_POST['preco'];

    $sql = "UPDATE bebidas SET nome = '$nome', tipo = '$tipo', preco = '$preco' WHERE id = '$id'";

    if (mysqli_query($conexao, $sql)) {
        header("Location: bebibas.php");
        exit();
    } else {
        echo "Erro ao atualizar bebida" . mysqli_error($conexao);
    }
}

$sql = "SELECT * FROM bebidas";
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
    <title>Bebibas</title>
</head>

<body>
    <div class="p-3 mb-2 bg-dark text-white mb-3">
        SISTEMA ASSIS NIGHT CLUB
        <div class="d-flex flex-row-reverse">
            <div class="p-2">
                <a class="btn btn-danger" href="../VIEW/painel.php"><i class="bi bi-arrow-left"></i> Voltar</a>
                <a class="btn btn-success" href="criar_bebidas.php"><i class="bi bi-plus-lg"></i>Adicionar
                    Bebida</a>
            </div>
        </div>
    </div>
    <h1 class="text-center mb-3">Bebibas</h1>
    <div class="container">
        <table class="table table-striped table-bordered">
            <tr class="text-center">
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Preco (R$)</th>
                <th>Ações</th>
            </tr>
            <?php
            if (mysqli_num_rows($resultado) > 0) {
                while ($row = mysqli_fetch_assoc($resultado)) {
                    echo "<tr class = 'text-center'>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nome"] . "</td>";
                    echo "<td>" . $row["tipo"] . "</td>";
                    echo "<td>" . $row["preco"] . "</td>";
                    echo "<td>
                    <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editarBebidaModal" . $row['id'] . "'><i class='bi bi-pencil'></i></button>
                    <a href='?id=" . $row['id'] . "' class='btn btn-danger'><i class='bi bi-trash'></i></a>
                    </td>";
                    echo "</tr>";

                    echo "<div class='modal fade' id='editarBebidaModal" . $row['id'] . "' tabindex='-1' aria-labelledby='editarBebibaModalLabel" . $row['id'] . "' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='editarBebidaModalLabel" . $row['id'] . "'>Editar Bebida</h5>
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
                                                <label for='tipo" . $row['id'] . "' class='form-label'>Tipo</label>
                                                <input type='text' class='form-control' id='tipo" . $row['id'] . "' name='tipo' value='" . $row['tipo'] . "'>
                                            </div>
                                            <div class='mb-3'>
                                                <label for='preco" . $row['id'] . "' class='form-label'>Preco</label>
                                                <input type='text' class='form-control' id='preco" . $row['id'] . "' name='preco' value='" . $row['preco'] . "'>
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
            }
            ?>
        </table>
    </div>
</body>

</html>