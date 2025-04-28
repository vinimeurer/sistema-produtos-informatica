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
    <title>Listar Usuários</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/list-usuario.css" rel="stylesheet" type="text/css">
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
        <h2>Gerenciar Usuários
            <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
        </h2>



        <div id="infoModal" class="modal-legenda">
            <div class="modal-legenda-content">
                <span class="modal-legenda-close" id="closeModal">&times;</span>
                <h2>Gerenciar Usuários</h2>
                <p>Através dessa tela é possível visulizar, editar e adicionar cadastro de usuários.</p>
                <ul>
                    <li><strong>Adicionar Usuário:</strong> Clique no botão com <i class="fa-solid fa-plus"></i> para cadastrar um novo usuário. Será aberta uma tela de cadastro para prencher os dados.</li>
                    <br>
                    <li><strong>Editar Usuário:</strong> Clique no botão com  <i class="fas fa-pen"></i> na linha do usuário que deseja editar. Após isso, será aberta uma tela para edição do cadastro selecionado.</li>
                    <br>
                </ul>
                <button class="btn btn-secondary" id="closeModalButton">Fechar</button>
            </div>
        </div>
        <div>
            <div class="header-content">
                <a href="../public/index.php?controller=admin&action=createUsuario" class="btn btn-primary"><i class="fa-solid fa-plus"></i></a>
                <form action="" method="GET" id="filter-form">
                    <input type="hidden" name="controller" value="admin">
                    <input type="hidden" name="action" value="listUsuarios">
                    <div class="form-group">
                        <label for="searchType">Pesquisar Por:</label>
                        <select name="searchType">
                            <option value="nome" <?php echo (isset($_GET['searchType']) && $_GET['searchType'] == 'nome') ? 'selected' : ''; ?>>Nome</option>
                            <option value="documento" <?php echo (isset($_GET['searchType']) && $_GET['searchType'] == 'documento') ? 'selected' : ''; ?>>Documento</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="search">Pesquisar:</label>
                        <input type="text" name="search" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status">
                            <option value="todos" <?php echo (!isset($_GET['status']) || $_GET['status'] == 'todos') ? 'selected' : ''; ?>>Todos</option>
                            <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Ativo</option>
                            <option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>Inativo</option>
                        </select>
                    </div>
                    <div id="filter-buttons">
                        <label for="reset-filter">ㅤ</label>
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        <label for="reset-filter">ㅤ</label>
                        <button onclick="location.href='../public/index.php?controller=admin&action=listUsuarios'" type="button" class="btn btn-primary">Limpar filtros</button>
                    </div>
                </form>
            </div>

            <div>
                <?php if (isset($_GET['success'])): ?>
                <div class="success-animation fade-out">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                </div>
                <script>
                    setTimeout(function() {
                        const successAnimation = document.querySelector('.success-animation');
                        successAnimation.classList.add('hidden'); 

                        setTimeout(function() {
                            window.location.href = "../public/index.php?controller=admin&action=listUsuarios"; 
                        }, 1000); 
                    }, 2000); 
                </script>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-animation fade-out">
                    <div class="container">
                        <div class="svg-box">
                            <svg class="circular red-stroke">
                                <circle class="path" cx="75" cy="75" r="50" fill="none" stroke-width="5" stroke-miterlimit="10"/>
                            </svg>
                            <svg class="cross red-stroke">
                                <g transform="matrix(0.79961,8.65821e-32,8.39584e-32,0.79961,-502.652,-204.518)">
                                    <path class="first-line" d="M634.087,300.805L673.361,261.53" fill="none"/>
                                </g>
                                <g transform="matrix(-1.28587e-16,-0.79961,0.79961,-1.28587e-16,-204.752,543.031)">
                                    <path class="second-line" d="M634.087,300.805L673.361,261.53"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
                <script>
                    setTimeout(function() {
                        const errorAnimation = document.querySelector('.error-animation');
                        errorAnimation.classList.add('hidden'); 

                        setTimeout(function() {
                            window.location.href = "../public/index.php?controller=home&action=index&error=1"; 
                        }, 1000); 
                    }, 2000); 
                </script>
            <?php endif; ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Documento</th>
                            <th>Login</th>
                            <th>Tipo do Login</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(substr($usuario['nome'], 0, 35)) . (strlen($usuario['nome']) > 35 ? '...' : ''); ?></td>
                        <td><?php echo htmlspecialchars(formatarCPF($usuario['documento'])); ?></td>
                        <td><?php echo htmlspecialchars($usuario['login']); ?></td>
                        <td>
                            <div class="<?php echo ($usuario['idTipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador'); ?>">
                                <?php echo ($usuario['idTipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador'); ?>
                            </div>
                        </td>
                        <td>
                            <div class="<?php echo ($usuario['idSituacaoUsuario'] == 1 ? 'ativo' : 'inativo'); ?>">
                                <?php echo ($usuario['idSituacaoUsuario'] == 1 ? 'Ativo' : 'Inativo'); ?>
                            </div>
                        </td>

                        <td>
                            <?php if (isset($usuario['idUsuario'])): ?>
                                <div class="action-buttons">
                                    <a href="../public/index.php?controller=admin&action=editUsuario&id=<?php echo htmlspecialchars($usuario['idUsuario']); ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="btn btn-sm btn-secondary disabled"><i class="fas fa-pen"></i></span>
                                <p>ID não definido</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src ="../public/assets/js/ListarUsuario.js"></script>
</body>
</html>