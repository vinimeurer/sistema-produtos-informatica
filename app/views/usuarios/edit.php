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
        <title>Editar Usuário</title>
        <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
        <link href="../public/assets/css/edit-usuario.css" rel="stylesheet" type="text/css">
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
            <h2>Atualizar Cadastro de Usuário
                <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
            </h2>

            <div id="infoModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeModal">&times;</span>
                    <h2>Instruções de Atualização de Cadastro</h2>
                    <p>Altere os campos que desejar:</p>
                    <ul>
                        <li><strong>Nome:</strong> Atualize o nome completo do usuário.</li>
                        <br>
                        <li><strong>CPF:</strong> CPF do usuá´rio, deve ser um documento válido.</li>
                        <br>
                        <li><strong>Login:</strong> O login será gerado automaticamente a partir do CPF, sem pontuações.</li>
                        <br>
                        <li><strong>Senha:</strong> Altere a senha de acesso do usuário através desse campo (a senha atual não é mostrada por questões de privacidade). Caso não queira atualizar a senha, deixar o campo em branco. Caso desejar alterar, deve, obrigatoriamente, conter:</li>
                        <ul>
                            <li>Mínimo 8 caracteres;</li>
                            <li>Mínimo 1 letra maiúscula (A-Z);</li>
                            <li>Mínimo 1 letra minúscula (a-z);</li>
                            <li>Mínimo 1 número (0-9);</li>
                            <li>Mínimo 1 caractere especial (@, #, $, %, &, *).</li>
                        </ul>
                        <br>
                        <li><strong>Endereço:</strong> Atualize os dados de endereço do usuário. Digite o CEP para preenchimento automático dos campos UF, Município e Rua.</li>
                        <br>
                        <li><strong>Status:</strong> Selecione o status do usuário. Lembrando que caso ele esteja inativo, não conseguirá acessar o sistema com as credenciais informadas.</li>
                        <br>
                        <li><strong>Tipo de Login:</strong> Determina o tipo de login do usuário. Pode ser Administrador ou Usuário Comum.</li>
                        <br>
                        
                    </ul>
                    <p>Após preencher os campos que deseja editar, clique em "Salvar Alterações" para salvar. Será feito o redirecionamento para a página de listagem indicando se as alterações foram bem sucedidas ou não.</p>
                    <button class="btn btn-secondary" id="closeModalButton">Fechar</button>

                </div>
            </div>
            <div>


                <form onsubmit="removerMascara()" action="../public/index.php?controller=admin&action=editUsuario&id=<?= $usuario['idUsuario'] ?>" method="post">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" maxlength="150" required>

                    <div class="label-cpf">
                        <label for="documento">CPF:</label>
                        <span id="cpfError" class="error-message">CPF inválido</span>
                    </div>
                    <input oninput="mascara(this); atualizarLogin(this); validarDocumento(this)" type="text" id="documento" name="documento" value="<?= htmlspecialchars($usuario['documento']) ?>" maxlength="14" required>



                    <label for="login">Login:</label>
                    <!-- <span class="info-icon"><i class="fas fa-info-circle"></i></span> -->
                    <input type="text" id="login" name="login" value="<?= htmlspecialchars($usuario['documento']) ?>" required readonly>

                    <div class="label-senha">
                        <label for="senha">Senha:</label>
                        <span id="senhaError" class="error-message">Senha fraca. Consulte os critérios</span>
                        <span class="senha-info-icon"><i class="fas fa-info-circle"></i></span>
                    </div>
                    <input type="password" id="senha" name="senha"> 

                    <label for="tipologin">Tipo de Login:</label>
                    <select id="tipoLogin" name="tipoLogin" required>
                        <!-- <option value="" hidden selected>-- Selecione o Tipo do Login --</option> -->
                        <option value="1" <?php echo ($usuario['idTipoLogin'] === 1) ? 'selected' : ''; ?>>Usuário Comum</option>
                        <option value="2" <?php echo ($usuario['idTipoLogin'] === 2) ? 'selected' : ''; ?>>Administrador</option>
                    </select>

                    <!-- Adicionando os campos de endereço -->
                    <div class="label-cep">
                        <label for="cep">CEP:</label>
                        <span id="cepError" class="error-message">CEP inválido</span>
                    </div>
                    <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($usuario['cep'] ?? '') ?>" maxlength="9" required>

                    <div class="endereco-grid">
                        <div>
                            <label for="uf">UF:</label>
                            <input type="text" id="uf" name="uf" value="<?= htmlspecialchars($usuario['uf'] ?? '') ?>" maxlength="2" required>
                        </div>
                        <div>
                            <label for="municipio">Município:</label>
                            <input type="text" id="municipio" name="municipio" value="<?= htmlspecialchars($usuario['municipio'] ?? '') ?>" maxlength="100" required>
                        </div>
                    </div>

                    <label for="rua">Rua:</label>
                    <input type="text" id="rua" name="rua" value="<?= htmlspecialchars($usuario['rua'] ?? '') ?>" maxlength="150" required>

                    <div class="endereco-grid">
                        <div>
                            <label for="numero">Número:</label>
                            <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($usuario['numero'] ?? '') ?>" maxlength="10" required>
                        </div>
                        <div>
                            <label for="complemento">Complemento:</label>
                            <input type="text" id="complemento" name="complemento" value="<?= htmlspecialchars($usuario['complemento'] ?? '') ?>" maxlength="100">
                        </div>
                    </div>

                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1" <?= ($usuario['idSituacaoUsuario'] == 1) ? 'selected' : ''; ?>>Ativo</option>
                        <option value="2" <?= ($usuario['idSituacaoUsuario'] == 2) ? 'selected' : ''; ?>>Inativo</option>
                    </select>

                    <input type="submit" id="submitBtn" value="Salvar Alterações" disabled style="background-color: #85ad98; color: #ffffff; cursor: not-allowed;">
                </form>
            </div>
        </div>
    
        
        <script src ="../public/assets/js/EditarUsuario.js"></script>
        <script>
            function mascaraCpf(cpf) {
                cpf = cpf.replace(/\D/g, ""); // Remove caracteres não numéricos
                cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2"); // Coloca o primeiro ponto
                cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2"); // Coloca o segundo ponto
                cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2"); // Coloca o traço
                return cpf;
            }

            function aplicarMascara() {
                const documentoInput = document.getElementById('documento');
                documentoInput.value = mascaraCpf(documentoInput.value);
                
                // Aplicar máscara ao CEP
                const cepInput = document.getElementById('cep');
                if (cepInput && cepInput.value) {
                    mascararCEP(cepInput);
                }
            }

            window.onload = function() {
                aplicarMascara();
                validarFormulario();
            };
        </script>
    </body>
</html>