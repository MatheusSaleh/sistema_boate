<?php 
require_once "../MODEL/conexao.php";

function validarEmail($email){
    $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    return preg_match($regex, $email);
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nome = $_POST['nome'];
    $site = $_POST['site'];
    $contato = $_POST['contato'];
    $data_proximo_show = $_POST['data_proximo_show'];

    if(!validarEmail($contato)){
        echo "Email invalido";
        exit();
    }

    $sql = "INSERT INTO artistas (nome,site,contato,data_proximo_show) VALUES ('$nome','$site','$contato','$data_proximo_show')";

    if(mysqli_query($conexao, $sql)){
        echo "Cliente adicionado com sucesso";
        header("Location: artistas.php");
        exit();
    } else {
        echo "Artista não adicionado. Erro: " . mysqli_error($conexao);
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
    <title>Adicionar Artista</title>
</head>
<body>
    <h1 class = "text-center mt-2">Adicionar Artistas</h1>
    <div class = "container">
        <form method="POST" action="">
            <div class = "mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="site" class="form-label">Site</label>
                <input type="text" class="form-control" id="site" name="site" required>
            </div>
            <div class="mb-3">
                <label for="contato" class="form-label">Contato (E-mail)</label>
                <input type="email" class="form-control" id="contato" name="contato" required>
            </div>
            <div class="mb-3">
                <label for="data_proximo_show" class="form-label">Data do Proximo Show</label>
                <input type="date" class="form-control" id="data_proximo_show" name="data_proximo_show" required>
            </div>
            <a class="btn btn-danger" href="artistas.php"><i class="bi bi-arrow-left"></i> Voltar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Confirmar</button>
        </form>
    </div>
</body>
</html>