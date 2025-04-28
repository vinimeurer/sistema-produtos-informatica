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
        <title>Editar Produto</title>
        <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
        <link href="../public/assets/css/edit-produto.css" rel="stylesheet" type="text/css">
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
            <h2>Editar Produto
                <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
            </h2>

            <div id="infoModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="closeModal">&times;</span>
                    <h2>Instruções de Edição</h2>
                    <p>Atualize os campos desejados:</p>
                    <ul>
                        <li><strong>Código do produto:</strong> Código do produto a ser atualizado.</li>
                        <br>
                        <li><strong>Tipo do produto:</strong> Tipo do produto (Produto, Peça ou Serviço).</li>
                        <br>
                        <li><strong>Nome do produto:</strong> Nome do produto a ser atualizado.</li>
                        <br>
                        <li><strong>Valor:</strong> Valor do produto.</li>
                        <br>
                        <li><strong>Valor mínimo à vista:</strong> Valor do produto caso seja pago à vista.</li>
                        <br>
                        <li><strong>Valor para locação:</strong> Valor do produto caso seja usado para locação.</li>
                        <br>
                        <li><strong>Descrição:</strong> Descrição do produto a ser atualizado. Pode conter até 350 caracteres.</li>
                        <br>
                        <li><strong>Imagens:</strong> Novas fotos do produto (opcional). Deve possuir no máximo 4MB e ter os formatos de imagem (PNG, JPG, JPEG).</li>
                    </ul>
                    <p>Após atualizar os campos, clique em "Salvar Alterações" para atualizar o cadastro.</p>
                    <button class="btn btn-secondary" id="closeModalButton">Fechar</button>
                </div>
            </div>

            <div>
                <form action="../public/index.php?controller=produto&action=updateProduto" method="post" enctype="multipart/form-data" id="editProdutoForm">
                    <input type="hidden" name="idProduto" value="<?php echo htmlspecialchars($produto['idProduto']); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="codigo">Código do Produto:*</label>
                            <input type="text" id="codigo" name="codigo" maxlength="30" required 
                                value="<?php echo htmlspecialchars($produto['codigoProduto']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="tipoProduto">Tipo de Produto:*</label>
                            <select id="tipoProduto" name="tipoProduto" required>
                                <?php foreach ($tiposProduto as $tipo): ?>
                                    <option value="<?php echo htmlspecialchars($tipo['idTipoProduto']); ?>" 
                                        <?php echo ($produto['idTipoProduto'] == $tipo['idTipoProduto']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo['descricao']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nome">Nome do Produto:*</label>
                            <input type="text" id="nome" name="nome" maxlength="120" required 
                                value="<?php echo htmlspecialchars($produto['nomeProduto']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="valor">Valor:*</label>
                            <input type="text" 
                                id="valor" 
                                name="valor" 
                                maxlength="8"
                                placeholder="0,00"
                                required
                                value="<?php echo number_format($produto['valorProduto'], 2, ',', '.'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="visibilidadeProduto">Visibilidade:*</label>
                            <select id="visibilidadeProduto" name="visibilidadeProduto" required>
                                <option value="1" <?php echo ($produto['idVisibilidadeProduto'] == 1) ? 'selected' : ''; ?>>Visível</option>
                                <option value="2" <?php echo ($produto['idVisibilidadeProduto'] == 2) ? 'selected' : ''; ?>>Oculto</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group">
                            <label for="descricao">Descrição:*</label>
                            <textarea id="descricao" name="descricao" maxlength="450" rows="1" required><?php echo htmlspecialchars($produto['descricaoProduto']); ?></textarea>
                        </div>
                    </div>

                    <div class="upload-container">
                        <input type="file" id="imagens" name="imagens[]" accept="image/png, image/jpeg" multiple>
                        <label for="imagens">Adicionar/Substituir Imagens (0/10)</label>
                        <div id="imagePreview" class="image-preview">
                            <?php if (!empty($produto['imagens'])): ?>
                                <?php foreach ($produto['imagens'] as $index => $imagem): ?>
                                    <div class="preview-image" data-index="<?php echo $index; ?>">
                                        <img src="data:image/jpeg;base64,<?php echo $imagem['imagemProduto']; ?>" alt="Imagem do Produto">
                                        <button type="button" class="remove-image" onclick="removeExistingImage(<?php echo $index; ?>)">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <input type="hidden" name="imagensRemovidas" id="imagensRemovidas" value="">
                    <input type="submit" value="Salvar Alterações" id="submitBtn">
                </form>
            </div>
        </div>
        <script src="../public/assets/js/EditarProduto.js"></script>
    </body>
</html>