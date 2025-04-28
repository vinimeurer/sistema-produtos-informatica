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
    <title>Produtos Disponíveis</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/list-produto.css" rel="stylesheet" type="text/css">
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
        <?php if ($_SESSION['idTipoLogin'] == 1): ?>
            <h2>Produtos Disponíveis
                <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
            </h2>
        <?php endif; ?> 


        <?php if ($_SESSION['idTipoLogin'] == 2): ?>
            <h2>Gerenciar Produtos
                <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
            </h2>
        <?php endif; ?> 

        <div id="infoModal" class="modal-legenda">
            <div class="modal-legenda-content">
                <span class="modal-legenda-close" id="closeModal">&times;</span>


                <?php if ($_SESSION['idTipoLogin'] == 1): ?>
                    <h2>Tela de Produtos Disponíveis</h2>
                        <p>Através dessa tela é possível visualizar os produtos disponíveis. Para visualizar Detalhes, clique sobre a foto ou nome do produto, então serão exibidas todas as informações do produto.</li>
                            <br>
                        </ul> 
                <?php endif; ?>   


                <?php if ($_SESSION['idTipoLogin'] == 2): ?>
                    <h2>Gerenciar Produtos</h2>
                        <p>Através dessa tela é possível visualizar, editar e adicionar cadastro de produtos.</p>
                        <ul>
                            <li><strong>Adicionar Produto:</strong> Clique no botão com <i class="fas fa-plus"></i> para cadastrar um novo produto.</li>
                            <br>
                            <li><strong>Visualizar Detalhes:</strong> Clique sobre a foto ou nome do produto para ver todas as informações.</li>
                            <br>
                            <li><strong>Editar Produto:</strong> Use o botão de edição em cada card para modificar as informações.</li>
                        </ul>
                <?php endif; ?> 

                <button class="btn btn-secondary" id="closeModalButton">Fechar</button>
            </div>
        </div>

        <div>
            <div class="header-content">
                <?php if ($_SESSION['idTipoLogin'] == 2): ?>
                    <a href="../public/index.php?controller=produto&action=createProdutos" class="btn btn-primary"><i class="fa-solid fa-plus"></i></a>
                <?php endif; ?> 
                <form action="" method="GET" id="filter-form">
                    <input type="hidden" name="controller" value="produto">
                    <input type="hidden" name="action" value="listProdutos">
                    <input type="hidden" name="searchType" value="geral">


                    <div class="form-group">
                        <label for="search">Pesquisa:</label>
                        <input type="text" name="search" placeholder="Pesquisar produtos..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>


                    <div class="form-group">
                        <label for="tipoProduto">Tipo de Produto:</label>
                        <select name="tipoProduto">
                            <option value="">Todos</option>
                            <?php foreach ($tiposProduto as $tipo): ?>
                                <option value="<?php echo $tipo['idTipoProduto']; ?>" 
                                    <?php echo (isset($_GET['tipoProduto']) && $_GET['tipoProduto'] == $tipo['idTipoProduto']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['descricao']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($_SESSION['idTipoLogin'] == 2): ?>
                        <div class="form-group">
                            <label for="visibilidade">Visibilidade:</label>
                            <select name="visibilidade">
                                <option value="">Todos</option>
                                <?php foreach ($visibilidadeProduto as $vis): ?>
                                    <option value="<?php echo $vis['idVisibilidadeProduto']; ?>" 
                                        <?php echo (isset($_GET['visibilidade']) && $_GET['visibilidade'] == $vis['idVisibilidadeProduto']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vis['descricao']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>


                    <div id="filter-buttons">
                        <label for="reset-filter">ㅤ</label>
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        <label for="reset-filter">ㅤ</label>
                        <button onclick="location.href='../public/index.php?controller=produto&action=listProdutos'" type="button" class="btn btn-primary">Limpar filtros</button>
                    </div>
                </form>
            </div>

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
                            window.location.href = "../public/index.php?controller=produto&action=listProdutos"; 
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
                        const successAnimation = document.querySelector('.success-animation');
                        successAnimation.classList.add('hidden'); 

                        setTimeout(function() {
                            window.location.href = "../public/index.php?controller=produto&action=listProdutos"; 
                        }, 1000); 
                    }, 2000); 
                </script>
            <?php endif; ?>

            <div class="equipment-grid">
                <?php if (!empty($produtos)): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="equipment-card" 
                            data-product-id="<?php echo $produto['idProduto']; ?>"
                            data-visibility="<?php echo $produto['idVisibilidadeProduto']; ?>"
                            data-images='<?php echo htmlspecialchars(json_encode($produto['imagens'])); ?>'
                            data-full-details='<?php echo htmlspecialchars(json_encode($produto)); ?>'>
                            <div class="image-container">
                                <?php if (!empty($produto['imagens'])): ?>
                                    <img class="equipment-image" src="data:image/jpeg;base64,<?php echo $produto['imagens'][0]['imagemProduto']; ?>" alt="Produto">
                                <?php else: ?>
                                    <img class="equipment-image" src="../public/assets/img/no-image.jpg" alt="Sem imagem">
                                <?php endif; ?>
                                <div class="image-title"></div>
                                <!-- <div class="image-counter"></div> -->
                            </div>
                            <div class="equipment-header">
                                <div class="equipment-name">
                                    <?php echo htmlspecialchars($produto['nomeProduto']); ?>
                                </div>
                                <?php if ($_SESSION['idTipoLogin'] == 2): ?>
                                    <a href="../public/index.php?controller=produto&action=editProduto&id=<?php echo $produto['idProduto']; ?>" 
                                    class="edit-button">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                <?php endif; ?> 
                                <?php if ($_SESSION['idTipoLogin'] == 1): ?>
                                    <button class="cart-button" onclick="addToCart(<?php echo $produto['idProduto']; ?>)">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                <?php endif; ?> 
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Não há nenhum Produto.</p>
                <?php endif; ?>
            </div>

            <div class="pagination">
                <a href="?controller=produto&action=listProdutos&pagina=1&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tipoProduto=<?php echo $_GET['tipoProduto'] ?? ''; ?>&visibilidade=<?php echo $_GET['visibilidade'] ?? ''; ?>" class="pagBtn"><i class="fa-solid fa-angles-left"></i></a>
                <a href="?controller=produto&action=listProdutos&pagina=<?php echo max(1, $paginaAtual - 1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tipoProduto=<?php echo $_GET['tipoProduto'] ?? ''; ?>&visibilidade=<?php echo $_GET['visibilidade'] ?? ''; ?>" class="pagBtn"><i class="fa-solid fa-angle-left"></i></a>

                <?php
                $maxDisplayedPages = 4;
                $startPage = max(1, $paginaAtual - 2);
                $endPage = min($totalPaginas, $startPage + $maxDisplayedPages - 1);

                if ($endPage - $startPage < $maxDisplayedPages - 1) {
                    $startPage = max(1, $endPage - $maxDisplayedPages + 1);
                }

                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?controller=produto&action=listProdutos&pagina=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tipoProduto=<?php echo $_GET['tipoProduto'] ?? ''; ?>&visibilidade=<?php echo $_GET['visibilidade'] ?? ''; ?>" class="pagBtn <?php echo $i === $paginaAtual ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($endPage < $totalPaginas): ?>
                    <span class="ellipsis">...</span>
                    <a href="?controller=produto&action=listProdutos&pagina=<?php echo $totalPaginas; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tipoProduto=<?php echo $_GET['tipoProduto'] ?? ''; ?>&visibilidade=<?php echo $_GET['visibilidade'] ?? ''; ?>" class="pagBtn">
                        <?php echo $totalPaginas; ?>
                    </a>
                <?php endif; ?>

                <a href="?controller=produto&action=listProdutos&pagina=<?php echo min($totalPaginas, $paginaAtual + 1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tipoProduto=<?php echo $_GET['tipoProduto'] ?? ''; ?>&visibilidade=<?php echo $_GET['visibilidade'] ?? ''; ?>" class="pagBtn"><i class="fa-solid fa-angle-right"></i></a>
                <a href="?controller=produto&action=listProdutos&pagina=<?php echo $totalPaginas; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tipoProduto=<?php echo $_GET['tipoProduto'] ?? ''; ?>&visibilidade=<?php echo $_GET['visibilidade'] ?? ''; ?>" class="pagBtn"><i class="fa-solid fa-angles-right"></i></a>
            </div>

            <!-- Modal for product details -->
            <div id="productDetailsModal" class="details-modal">
                <div class="details-modal-content">
                    <span class="details-modal-close">&times;</span>
                    <div class="modal-image-container">
                        <img class="details-modal-image" src="" alt="Produto">
                        <div class="modal-nav">
                            <button onclick="changeModalImage(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                            <span class="image-counter"></span>
                            <button onclick="changeModalImage(1)"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="details-modal-title"></div>
                    <div class="details-modal-code"></div>
                    <div class="details-modal-description"></div>
                    <div class="details-modal-price"></div>
                    <div class="details-modal-min-price"></div>
                    <div class="details-modal-rental"></div>
                    <div class="details-modal-date"></div>
                </div>
            </div>
        </div>
        <?php if ($_SESSION['idTipoLogin'] == 1): ?>
            <div class="view-cart-container">
                <button id="viewCartBtn" class="view-cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Ver Carrinho</span>
                    <span class="cart-counter" id="cartCounter">0</span>
                </button>
            </div>

            <!-- Modal do Carrinho -->
            <div id="cartModal" class="cart-modal">
                <div class="cart-modal-content">
                    <span class="cart-modal-close">&times;</span>
                    <h2>Seu Carrinho</h2>
                    <div id="cartItems" class="cart-items">
                        <!-- Itens do carrinho serão inseridos aqui via JavaScript -->
                    </div>
                    <div class="cart-footer">
                        <div class="cart-total">
                            <span>Total:</span>
                            <span id="cartTotal">R$ 0,00</span>
                        </div>
                        <button id="clearCartBtn" class="btn btn-danger">Limpar Carrinho</button>
                        <button id="checkoutBtn" class="btn btn-primary">Finalizar Compra</button>
                    </div>
                </div>
            </div>
        <?php endif; ?> 
        
    </div>
    <script src="../public/assets/js/ListarProduto.js"></script>
</body>
</html>

