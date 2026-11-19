<?php
require_once "../MODEL/conexao.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $preco = $_POST['preco'];

    $sql = "INSERT INTO bebidas (nome, tipo, preco) VALUES ('$nome', '$tipo', '$preco')";

    if(mysqli_query($conexao, $sql)){
        echo "Bebida adicionada com sucesso";
        header("Location: bebibas.php");
    } else{
        echo "Erro ao adicionar bebida";
    }

    mysqli_close($conexao);
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
    <title>Adicionar Bebidas</title>
</head>
<body>
    <h1 class="text-center mt-2">Adicionar Bebidas</h1>
    <div class="container">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" name="tipo" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preco</label>
                <input type="number" class="form-control" id="preco" name="preco" required>
            </div>
            <a class="btn btn-danger" href="bebibas.php"><i class="bi bi-arrow-left"></i> Voltar</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Confirmar</button>
        </form>
    </div>
</body>
</html>