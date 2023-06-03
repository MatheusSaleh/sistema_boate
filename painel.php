<?php
include('protect.php');
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
    <link rel="stylesheet" href="styles/painel.css">
    <title>Painel</title>
</head>

<body>
    <div class="p-3 mb-2 bg-dark text-white mb-3">
        SISTEMA ASSIS NIGHT CLUB
        <div class="d-flex flex-row-reverse">
            <div class="p-2">
                <a class="btn btn-danger" href="logout.php"><i class="bi bi-box-arrow-left"></i> Sair</a>
            </div>
        </div>

    </div>
    <div class="text-center">Bem vindo ao Painel, <b><?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?></b></div>
    <div class="text-center mt-2">
        O que voce deseja fazer ?
    </div>

    <div class="container mt-3">
        <div class="row card-deck">
            <div class="col-3">
                <div class="card">
                    <img src="https://www.diariodoaco.com.br/images/noticias/75617/20200207113911_ht3Er50xjk.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Funcionarios</h5>
                        <a href="funcionarios.php" class="btn btn-primary"><i class="bi bi-table"></i> Abrir</a>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <img src="https://soubh.uai.com.br/uploads/place/image/266/blobal-bebidas.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title cartao-bebidas">Gerenciar Bebibas</h5>
                        <a href="bebidas.php" class="btn btn-primary"><i class="bi bi-table"></i> Abrir</a>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <img src="https://images.pexels.com/photos/2034851/pexels-photo-2034851.jpeg?cs=srgb&dl=pexels-edoardo-tommasini-2034851.jpg&fm=jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Clientes</h5>
                        <a href="clientes.php" class="btn btn-primary"><i class="bi bi-table"></i> Abrir</a>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <img src="https://static1.purepeople.com.br/articles/6/35/81/86/@/4103761-ana-castela-se-apresentou-pela-primeira-950x0-2.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Artistas</h5>
                        <a href="artistas.php" class="btn btn-primary"><i class="bi bi-table"></i> Abrir</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>