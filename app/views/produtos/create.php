
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
        <title>Cadastro de Produto</title>
        <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
        <link href="../public/assets/css/create-produto.css" rel="stylesheet" type="text/css">
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


            <h2>Cadastrar Produto
                <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
            </h2>

            <div id="infoModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeModal">&times;</span>
                    <h2>Instruções de Cadastro</h2>
                    <p>Preencha todos os campos obrigatórios:</p>
                    <ul>
                        <li><strong>Código do produto:</strong> Código do produto a ser cadastrado.</li>
                        <br>
                        <li><strong>Tipo do produto:</strong> Seleciona o tipo do produto. Pode ser:</li>
                            <ul>
                                <li>Produto;</li>
                                <li>Peça;</li>
                                <li>Serviço;</li>
                            </ul>
                        <br>
                        <li><strong>Nome do produto:</strong> Nome do produto a ser cadastrado.</li>
                        <br>
                        <li><strong>Valor:</strong> Valor do produto a ser cadastrado.</li>
                        <br>
                        <li><strong>Valor mínimo à vista:</strong> Valor do produto caso seja pago à vista.</li>
                        <br>
                        <li><strong>Valor para locação:</strong> Valor do produto caso seja usado para locação.</li>
                        <br>
                        <li><strong>Descrição:</strong> Descrição do produto a ser cadastrado. Pode conter até 450 caracteres.</li>
                        <br>
                        <li><strong>Imagem:</strong> Fotos do produto. Deve possuir no máximo 4MB e ter os formatos de imagem (PNG, JPG, JPEG), com limite de 10 imagens por produto.</li>
  
                    </ul>
                    <p>Após preencher os campos, clique em "Cadastrar Produto" para salvar. Após isso, será feito o redirecionamento para a página de listagem indicando se o cadastro foi bem sucedido ou não.</p>
                    <button class="btn btn-secondary" id="closeModalButton">Fechar</button>

                </div>
            </div>

            <div>
                <form id="equipForm" action="../public/index.php?controller=produto&action=createProdutos" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        
                        <div class="form-group">
                            <label for="codigo">Código do Produto:*</label>
                            <input type="text" id="codigo" name="codigo" maxlength="30">
                            <span class="error-message">Campo obrigatório</span>
                        </div>

                        <div class="form-group">
                            <label for="tipoProduto">Tipo de Produto:*</label>
                            <div class="select-with-button">
                                <select id="tipoProduto" name="tipoProduto" required>
                                    <option value="" hidden selected>Selecione...</option>
                                    <?php foreach ($tiposProduto as $tipo): ?>
                                        <option value="<?php echo htmlspecialchars($tipo['idTipoProduto']); ?>">
                                            <?php echo htmlspecialchars($tipo['descricao']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" id="addCategoryBtn" title="Adicionar nova categoria">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <span class="error-message">Campo obrigatório</span>
                        </div>

                        <div class="form-group">
                            <label for="nome">Nome do Produto:*</label>
                            <input type="text" id="nome" name="nome" maxlength="120">
                            <span class="error-message">Campo obrigatório</span>
                        </div>

                        <div class="form-group">
                            <label for="valor">Valor:*</label>
                            <input type="text" 
                                id="valor" 
                                name="valor" 
                                maxlength="8"
                                placeholder="0,00"
                                required>
                        </div>
                        
                        
                    </div>
                        

                    <div class="form-row">
                        <div class="form-group">
                            <label for="descricao">Descrição:*</label>
                            <textarea id="descricao" name="descricao" maxlength="450" rows="1"></textarea>
                            <span class="error-message">Campo obrigatório</span>
                        </div>
                    </div>

                    <div class="upload-container">
                        <input type="file" id="imagens" name="imagens[]" accept="image/png, image/jpeg" multiple>
                        <label for="imagens">Adicionar Imagem (0/10)</label>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>

                    <input type="submit" id="submitBtn" value="Cadastrar Produto" disabled style="background-color: #85ad98; color: #ffffff; cursor: not-allowed;">
                </form>
            </div>
            <div id="categoryModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeCategoryModal">&times;</span>
                    <h2>Adicionar Nova Categoria</h2>
                    <div class="form-group">
                        <label for="newCategory">Nome da Categoria:</label>
                        <input type="text" id="newCategory" name="newCategory" maxlength="50">
                        <span class="error-message" id="categoryErrorMsg"></span>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" id="saveCategoryBtn" class="btn btn-primary">Salvar</button>
                        <button type="button" id="cancelCategoryBtn" class="btn btn-secondary">Cancelar</button>
                    </div>
                </div>
            </div>                                
        </div>
        <script src ="../public/assets/js/CadastrarProduto.js"> 
            
        </script>
    </body>
</html>
