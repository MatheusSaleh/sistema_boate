<?php
require_once "../MODEL/conexao.php";

function validarEmail($email)
{
    $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    return preg_match($regex, $email);
}

if(isset($_FILES['imagem'])){
    $diretorio = '../IMG';


    $nomeArquivo = time() . '-' . $_FILES['imagem']['name'];

    $caminhoCompleto = $diretorio . $nomeArquivo;

    if(move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)){
        echo "Imagem enviada com sucesso";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nome = $_POST['nome'];
            $site = $_POST['site'];
            $contato = $_POST['contato'];
            $data_proximo_show = $_POST['data_proximo_show'];
            $biografia = $_POST['biografia'];
            $instagram = $_POST['instagram'];
            $youtube = $_POST['youtube'];
            $spotify = $_POST['spotify'];

            if (!validarEmail($contato)) {
                echo "Email inválido";
                exit();
            }

            $sql = "INSERT INTO artistas (nome, site, contato, data_proximo_show, image, biografia, instagram, youtube, spotify) VALUES ('$nome', '$site', '$contato', '$data_proximo_show', '$caminhoCompleto', '$biografia', '$instagram', '$youtube', '$spotify')";

            if (mysqli_query($conexao, $sql)) {
                mysqli_close($conexao);
                header("Location: artistas.php");
                exit();
            } else {
                echo "Artista não adicionado. Erro: " . mysqli_error($conexao);
            }
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
    <title>Adicionar Artista</title>
</head>

<body>
    <h1 class="text-center mt-2">Adicionar Artistas</h1>
    <div class="container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
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
                <label for="data_proximo_show" class="form-label">Data do Próximo Show</label>
                <input type="date" class="form-control" id="data_proximo_show" name="data_proximo_show" required>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem do artista</label>
                <input class="form-control" type="file" id="imagem" name="imagem" required>
            </div>
            <div class="mb-3">
                <label for="instagram" class="form-label">Link Instagram</label>
                <input class="form-control" type="text" id="instagram" name="instagram" required>
            </div>
            <div class="mb-3">
                <label for="youtube" class="form-label">Link Youtube</label>
                <input type="text" class="form-control" id="youtube" name="youtube" required>
            </div>
            <div class="mb-3">
                <label for="spotify" class="form-label">Link Spotify</label>
                <input type="text" class="form-control" id="spotify" name="spotify" required>
            </div>
            <div class="mb-3">
                <label for="biografia" class="form-label">Biografia</label>
                <textarea class="form-control" rows="3" id="biografia" name="biografia" required></textarea>
            </div>
            <a class="btn btn-danger" href="artistas.php"><i class="bi bi-arrow-left"></i> Voltar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Confirmar</button>
        </form>
    </div>
</body>

</html>
