
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Minhas Compras</title>
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
            <a href="../public/index.php?controller=profile&action=index"><i class="fas fa-user-circle"></i>Meu Perfil</a>
            <a href="../public/index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt"></i>Sair</a>
        </div>
    </nav>
    
    <div class="content">
        <h2>Minhas Compras</h2>
        
        <div class="compras-container">
            <?php if (empty($compras)): ?>
                <div class="no-orders">
                    <p>Você ainda não realizou nenhuma compra.</p>
                    <a href="../public/index.php?controller=produto&action=listProdutos" class="btn btn-primary">Ver produtos</a>
                </div>
            <?php else: ?>
                <?php foreach ($compras as $compra): ?>
                    <div class="compra-card">
                        <div class="compra-header">
                            <div class="compra-date">
                                Compra #<?php echo $compra['idCompra']; ?> - 
                                <?php echo date('d/m/Y H:i', strtotime($compra['dataCompra'])); ?>
                            </div>
                            <div class="compra-total">
                                R$ <?php echo number_format($compra['valorTotal'], 2, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="compra-items">
                            <?php echo $compra['totalItens']; ?> item(ns)
                        </div>
                        <div class="compra-actions">
                            <a href="../public/index.php?controller=compra&action=detalhesCompra&id=<?php echo $compra['idCompra']; ?>" class="view-details">
                                Ver detalhes
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>