<?php
function formatarCPF($cpf) {

    $cpf = preg_replace('/\D/', '', $cpf);
    
    if (strlen($cpf) === 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf; 
}


function formatarCNPJ($cnpj) {

    $cnpj = preg_replace('/\D/', '', $cnpj);
    
    if (strlen($cnpj) === 14) {
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    } 
    return $cnpj; 
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/profile.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body class="loggedin">
    <nav class="navtop">
        <div class="nav-left">
            <img src="../public/assets/img/logo2.png" alt="">
        </div>
        <!-- <div class="nav-login-info">
            <p>
                <?php 
                if (isset($usuario)) {
                    $documentoUsuario = htmlspecialchars($usuario['documento']);
                    $nomeUsuario = htmlspecialchars($usuario['nome']);
                    echo "$documento - $nome";
                }
                ?>
            </p>
        </div> -->
        <div class="nav-right">
            <a href="../public/index.php?controller=home&action=index"><i class="fas fa-house"></i>Página Inicial</a>
            <a href="../public/index.php?controller=profile&action=index"><i class="fas fa-user-circle"></i>Meu Perfil</a>
            <a href="../public/index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt"></i>Sair</a>
        </div>
    </nav>
    <div class="content">
        <h2>Meu Perfil</h2>
        <div>
            <p><strong>Detalhes da conta:</strong></p>
            <table>
                
                <?php if ($_SESSION['idTipoLogin'] == 1): ?>
                    <tr>
                        <td>Nome do Usuário:</td>
                        <td><?=htmlspecialchars($userDetails['nome'] ?? '', ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <td>Login:</td>
                        <td><?=htmlspecialchars($userDetails['login'] ?? '', ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <td>Tipo de perfil:</td>
                        <td><?=htmlspecialchars($userDetails['idTipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador')?></td>
                    </tr>
                    <tr>
                        <td>Data de criação:</td>
                        <td><?=htmlspecialchars($userDetails['dataCriacao'] ?? '', ENT_QUOTES)?></td>
                    </tr>

                <?php endif; ?>
                <?php if ($_SESSION['idTipoLogin'] == 2): ?>
                    <tr>
                        <td>Nome do Usuário:</td>
                        <td><?=htmlspecialchars($userDetails['nome'] ?? '', ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <td>Login:</td>
                        <td><?=htmlspecialchars($userDetails['login'] ?? '', ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <td>Tipo de perfil:</td>
                        <td><?=htmlspecialchars($userDetails['idTipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador')?></td>
                    </tr>
                    <tr>
                        <td>Data de criação:</td>
                        <td><?=htmlspecialchars($userDetails['dataCriacao'] ?? '', ENT_QUOTES)?></td>
                    </tr>

                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>