<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detalhes da Venda #<?php echo $compra['idCompra']; ?></title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/relatorio-vendas.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>

    </style>
</head>
<body class="loggedin">
    <nav class="navtop no-print">
        <div class="nav-left">
            <img src="../public/assets/img/logo2.png" alt="">
        </div>
        <div class="nav-right">
            <a href="../public/index.php?controller=home&action=index"><i class="fas fa-house"></i>Página Inicial</a>
            <a href="../public/index.php?controller=produto&action=listProdutos"><i class="fas fa-shopping-bag"></i>Produtos</a>
            <a href="../public/index.php?controller=compra&action=relatorioVendas"><i class="fas fa-chart-line"></i>Relatório de Vendas</a>
            <a href="../public/index.php?controller=profile&action=index"><i class="fas fa-user-circle"></i>Meu Perfil</a>
            <a href="../public/index.php?controller=auth&action=logout"><i class="fas fa-sign-out-alt"></i>Sair</a>
        </div>
    </nav>
    
    <div class="content">
        <h2 class="no-print">Detalhes da Venda #<?php echo $compra['idCompra']; ?></h2>
        
        <div class="compra-detalhes">
            <div class="compra-header">
                <div class="compra-info">
                    <div class="info-group">
                        <div class="info-label">Número da Venda</div>
                        <div class="info-value">#<?php echo $compra['idCompra']; ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Data/Hora</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($compra['dataCompra'])); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Valor Total</div>
                        <div class="info-value">R$ <?php echo number_format($compra['valorTotal'], 2, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="cliente-info">
                <div class="cliente-title">Informações do Cliente</div>
                <div class="compra-info">
                    <div class="info-group">
                        <div class="info-label">Nome</div>
                        <div class="info-value"><?php echo htmlspecialchars($compra['nomeUsuario']); ?></div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">ID do Cliente</div>
                        <div class="info-value"><?php echo $compra['idUsuario']; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="compra-items">
                <h3>Itens da Venda</h3>
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
            
            <div class="action-buttons no-print">
                <a href="../public/index.php?controller=compra&action=relatorioVendas" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                
                <button onclick="window.print();" class="btn btn-print">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</body>
</html>