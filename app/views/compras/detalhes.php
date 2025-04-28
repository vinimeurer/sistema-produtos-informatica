
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detalhes da Compra</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/compras.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body class="loggedin">
    <nav class="navtop">
        <div class="nav-left">
            <img src="../public/assets/img/logo2.png" alt="">
        </div>
        <div class="nav-right">
            <a href="../public/index.php?controller=home&action=index"><i class="fas fa-house"></i>Página Inicial</a>
            <a href="../public/index.php?controller=produto&action=listProdutos"><i class="fas fa-shopping-bag"></i>Produtos</a>
            <a href="../public/index.php?controller=compra&action=minhasCompras"><i class="fas fa-shopping-cart"></i>Minhas Compras</a>
            <a href="../public/index.php?controller=profile&action=index"><i class="fas fa-user-circle"></i>Meu Perfil</a>
            <a href="../public/index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt"></i>Sair</a>
        </div>
    </nav>
    
    <div class="content">
        <h2>Detalhes da Compra #<?php echo $compra['idCompra']; ?></h2>
        
        <div class="compra-detalhes">
            <div class="compra-header">
                <div class="compra-info">
                    <div>
                        <div class="compra-title">Data da Compra</div>
                        <div><?php echo date('d/m/Y H:i', strtotime($compra['dataCompra'])); ?></div>
                    </div>
                    <div>
                        <div class="compra-title">Valor Total</div>
                        <div>R$ <?php echo number_format($compra['valorTotal'], 2, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="compra-items">
                <h3>Itens da Compra</h3>
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Valor Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compra['itens'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['codigoProduto']); ?></td>
                                <td><?php echo htmlspecialchars($item['nomeProduto']); ?></td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($item['valorUnitario'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($item['valorTotal'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Total:</td>
                            <td>R$ <?php echo number_format($compra['valorTotal'], 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="action-buttons">
                <a href="../public/index.php?controller=compra&action=minhasCompras" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</body>
</html>