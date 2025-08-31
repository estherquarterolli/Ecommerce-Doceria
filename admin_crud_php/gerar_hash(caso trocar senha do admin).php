<?php

$senha = "estherfeia"; 
$hash = password_hash($senha, PASSWORD_DEFAULT);
echo "Senha original: $senha\n";
echo "Hash gerado: $hash\n";
?>