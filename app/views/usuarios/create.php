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
        <title>Cadastrar Usuário</title>
        <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
        <link href="../public/assets/css/create-usuario.css" rel="stylesheet" type="text/css">
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


            <h2>Cadastrar Novo Usuário
                <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
            </h2>

            <div id="infoModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeModal">&times;</span>
                    <h2>Instruções de Cadastro</h2>
                    <p>Preencha todos os campos obrigatórios:</p>
                    <ul>
                        <li><strong>Nome:</strong> Nome completo do usuário.</li>
                        <br>
                        <li><strong>CPF:</strong> Documento de identificação pessoal do usuário. Deve ser um CPF documento válido.</li>
                        <br>
                        <li><strong>Login:</strong> O login será gerado automaticamente a partir do CPF, sendo o mesmo valor do documento, mas sem pontuações.</li>
                        <br>
                        <li><strong>Senha:</strong> Senha que será para acessar o sistema. Deve conter obrigatoriamente:</li>
                        <ul>
                            <li>Mínimo 8 caracteres;</li>
                            <li>Mínimo 1 letra maiúscula (A-Z);</li>
                            <li>Mínimo 1 letra minúscula (a-z);</li>
                            <li>Mínimo 1 número (0-9);</li>
                            <li>Mínimo 1 caractere especial (@, #, $, %, &, *).</li>
                        </ul>
                        <br>
                        <li><strong>Tipo de Login:</strong> Determina o tipo de login do usuário. Pode ser Administrador ou Usuário Comum.</li>
                        <br>
                    </ul>
                    <p>Com os campos devidamente preenchidos, clique em "Cadastrar Usuário" para salvar. Após isso, será feito o redirecionamento para a página de listagem indicando se o cadastro foi bem sucedido ou não.</p>
                    <button class="btn btn-secondary" id="closeModalButton">Fechar</button>

                </div>
            </div>

            <div>
                <form onsubmit="removerMascara()" action="../public/index.php?controller=admin&action=createUsuario" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome:</label>
                            <input type="text" id="nome" name="nome" maxlength="150" required>
                        </div>
                        
                        <div class="form-group">
                            <div class="label-cpf">
                                <label for="documento">CPF:</label>
                                <span id="cpfError" class="error-message">CPF inválido</span>
                            </div>
                            <input oninput="mascara(this); atualizarLogin(this); validarDocumento(this)" type="text" id="documento" name="documento" maxlength="14" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="login">Login:</label>
                            <input type="text" id="login" name="login" required readonly>
                        </div>
                        
                        <div class="form-group">
                            <div class="label-senha">
                                <label for="senha">Senha:</label>
                                <span id="senhaError" class="error-message">Senha fraca. Consulte os critérios</span>
                                <span class="senha-info-icon"><i class="fas fa-info-circle"></i></span>
                            </div>
                            <input type="password" id="senha" name="senha" maxlength="255">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipologin">Tipo de Login:</label>
                            <select id="tipoLogin" name="tipoLogin" required>
                                <option value="1">Usuário Comum</option>
                                <option value="2">Administrador</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="label-cep">
                                <label for="cep">CEP:</label>
                                <span id="cepError" class="error-message">CEP inválido</span>
                            </div>
                            <input type="text" id="cep" name="cep" maxlength="9" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="uf">UF:</label>
                            <input type="text" id="uf" name="uf" maxlength="2" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="municipio">Município:</label>
                            <input type="text" id="municipio" name="municipio" maxlength="100" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label for="rua">Rua:</label>
                            <input type="text" id="rua" name="rua" maxlength="150" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero">Número:</label>
                            <input type="text" id="numero" name="numero" maxlength="10" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="complemento">Complemento:</label>
                            <input type="text" id="complemento" name="complemento" maxlength="100">
                        </div>
                    </div>
                    
                    <input type="submit" id="submitBtn" value="Cadastrar Usuário" disabled style="background-color: #85ad98; color: #ffffff; cursor: not-allowed;">
                </form>
            </div>

        <script src ="../public/assets/js/CadastrarUsuario.js"> 
            
        </script>
    </body>
</html>
