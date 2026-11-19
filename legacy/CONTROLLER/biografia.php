<?php 
require_once "../MODEL/conexao.php";

if(!$conexao){
    die("Falha na conexao com o banco de dados" . mysqli_connect_error());
}

if(isset($_GET['id'])){
    $idArtista = $_GET['id'];

    $sql = "SELECT * FROM artistas WHERE id = '$idArtista'";
    $resultado = mysqli_query($conexao, $sql);

    if($resultado && mysqli_num_rows($resultado) > 0){
        $artista = mysqli_fetch_assoc($resultado);
        $nome = $artista['nome'];
        $biografia = $artista['biografia'];
        $urlImagem = $artista['image'];
        $instagram = $artista['instagram'];
        $youtube = $artista['youtube'];
        $spotify = $artista['spotify'];
    } else {
        echo "Erro ao encontrar artista";
        exit();
    } 
} else{
    echo "Erro ao fazer alguma coisa";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Biografia do artista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body{
            background-image: url("../IMG/background.jpg");
            background-size: cover;
        }
    </style>
</head>
<body>
<div class="p-3 mb-2 bg-dark text-white mb-3">
        SISTEMA ASSIS NIGHT CLUB
        <div class="d-flex flex-row-reverse">
            <div class="p-2">
                <a class="btn btn-danger" href="artistas.php"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
<div class="container">
  <div class="row">
    <div class="col">
    <div class="card pb mb-4" style="width: 18rem;">
          <img src="<?php echo $urlImagem?>" class="card-img-top" alt="...">
          <div class="card-body">
        <h5 class="card-title"><?php echo $nome?></h5>
        <p class="card-text"><?php echo $biografia; ?></p>
          </div>
          <ul class="list-group list-group-flush d-flex flex-row">
        <li class="list-group-item"><a href="<?php echo $instagram?>"><i class="bi bi-instagram"></i></a></li>
        <li class="list-group-item"><a href="<?php echo $youtube?>"><i class="bi bi-youtube"></i></a></li>
        <li class="list-group-item"><a href="<?php echo $spotify?>"><i class="bi bi-spotify"></i></a></li>
          </ul>
        </div>
    </div>
    <div class="col">
    </div>
  </div>
</div>
</body>
</html>
