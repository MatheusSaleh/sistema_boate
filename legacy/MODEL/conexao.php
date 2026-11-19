<?php

$usuario = 'root';
$senha = '';
$database = 'login';
$host = 'localhost';

$conexao = new mysqli($host, $usuario, $senha, $database);

if($conexao->error){
    die("Falha ao conectar ao banco de dados: " . $conexao->error);
}
