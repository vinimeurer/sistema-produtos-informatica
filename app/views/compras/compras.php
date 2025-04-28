
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Minhas Compras</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/list-produto.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        .compras-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .compra-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .compra-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .compra-date {
            font-weight: bold;
            color: #555;
        }
        
        .compra-total {
            font-size: 1.1em;
            font-weight: bold;
            color: #0f2566;
        }
        
        .compra-items {
            color: #666;
        }
        
        .compra-actions {
            margin-top: 10px;
            text-align: right;
        }
        
        .view-details {
            background-color: #0f2566;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .view-details:hover {
            background-color: #0a1b4a;
        }
        
        .no-orders {
            text-align: center;
            padding: 40px 0;
            color: #666;
        }
    </style>
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