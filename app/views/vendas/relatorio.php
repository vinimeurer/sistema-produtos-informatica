<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Relatório de Vendas</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/list-produto.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        .filter-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .filter-group {
            margin-bottom: 0;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #495057;
        }
        
        .filter-group input, .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .filter-buttons {
            grid-column: span 2;
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            flex: 1;
            min-width: 200px;
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0f2566;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .vendas-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .vendas-table th, .vendas-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .vendas-table th {
            background-color: #f8f9fa;
            color: #495057;
            position: sticky;
            top: 0;
        }
        
        .vendas-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .no-results {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }
        
        .btn-details {
            background-color: #0f2566;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-details:hover {
            background-color: #0a1b4a;
        }
        
        .pagination {
            margin-top: 20px;
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
        <h2>Relatório de Vendas</h2>
        
        <div class="filter-container">
            <form action="" method="GET" class="filter-form">
                <input type="hidden" name="controller" value="compra">
                <input type="hidden" name="action" value="relatorioVendas">
                
                <div class="filter-group">
                    <label for="dataInicial">Data Inicial:</label>
                    <input type="date" id="dataInicial" name="dataInicial" value="<?php echo htmlspecialchars($filtros['dataInicial']); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="dataFinal">Data Final:</label>
                    <input type="date" id="dataFinal" name="dataFinal" value="<?php echo htmlspecialchars($filtros['dataFinal']); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="idUsuario">Usuário:</label>
                    <select id="idUsuario" name="idUsuario">
                        <option value="">Todos os usuários</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario['idUsuario']; ?>" <?php echo $filtros['idUsuario'] == $usuario['idUsuario'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($usuario['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="valorMinimo">Valor Mínimo:</label>
                    <input type="text" id="valorMinimo" name="valorMinimo" placeholder="0,00" value="<?php echo htmlspecialchars($filtros['valorMinimo']); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="valorMaximo">Valor Máximo:</label>
                    <input type="text" id="valorMaximo" name="valorMaximo" placeholder="0,00" value="<?php echo htmlspecialchars($filtros['valorMaximo']); ?>">
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="../public/index.php?controller=compra&action=relatorioVendas" class="btn btn-secondary">Limpar Filtros</a>
                </div>
            </form>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Total de Vendas</div>
                <div class="stat-value"><?php echo number_format($estatisticas['totalCompras'], 0, ',', '.'); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Valor Total (R$)</div>
                <div class="stat-value"><?php echo number_format($estatisticas['valorTotal'], 2, ',', '.'); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Média por Venda (R$)</div>
                <div class="stat-value"><?php echo number_format($estatisticas['mediaValor'], 2, ',', '.'); ?></div>
            </div>
        </div>
        
        <?php if (empty($compras)): ?>
            <div class="no-results">
                <p>Nenhuma venda encontrada com os filtros selecionados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="vendas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Cliente</th>
                            <th>Valor Total</th>
                            <th>Itens</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td>#<?php echo $compra['idCompra']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($compra['dataCompra'])); ?></td>
                                <td><?php echo htmlspecialchars($compra['nomeUsuario']); ?></td>
                                <td>R$ <?php echo number_format($compra['valorTotal'], 2, ',', '.'); ?></td>
                                <td><?php echo $compra['totalItens']; ?></td>
                                <td>
                                    <a href="../public/index.php?controller=compra&action=detalhesVendaAdmin&id=<?php echo $compra['idCompra']; ?>" class="btn-details">
                                        Ver detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <a href="?controller=compra&action=relatorioVendas&pagina=1&dataInicial=<?php echo urlencode($filtros['dataInicial']); ?>&dataFinal=<?php echo urlencode($filtros['dataFinal']); ?>&idUsuario=<?php echo urlencode($filtros['idUsuario']); ?>&valorMinimo=<?php echo urlencode($filtros['valorMinimo']); ?>&valorMaximo=<?php echo urlencode($filtros['valorMaximo']); ?>" class="pagBtn"><i class="fa-solid fa-angles-left"></i></a>
                
                <a href="?controller=compra&action=relatorioVendas&pagina=<?php echo max(1, $paginaAtual - 1); ?>&dataInicial=<?php echo urlencode($filtros['dataInicial']); ?>&dataFinal=<?php echo urlencode($filtros['dataFinal']); ?>&idUsuario=<?php echo urlencode($filtros['idUsuario']); ?>&valorMinimo=<?php echo urlencode($filtros['valorMinimo']); ?>&valorMaximo=<?php echo urlencode($filtros['valorMaximo']); ?>" class="pagBtn"><i class="fa-solid fa-angle-left"></i></a>
                
                <?php
                $maxDisplayedPages = 4;
                $startPage = max(1, $paginaAtual - 2);
                $endPage = min($totalPaginas, $startPage + $maxDisplayedPages - 1);
                
                if ($endPage - $startPage < $maxDisplayedPages - 1) {
                    $startPage = max(1, $endPage - $maxDisplayedPages + 1);
                }
                
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?controller=compra&action=relatorioVendas&pagina=<?php echo $i; ?>&dataInicial=<?php echo urlencode($filtros['dataInicial']); ?>&dataFinal=<?php echo urlencode($filtros['dataFinal']); ?>&idUsuario=<?php echo urlencode($filtros['idUsuario']); ?>&valorMinimo=<?php echo urlencode($filtros['valorMinimo']); ?>&valorMaximo=<?php echo urlencode($filtros['valorMaximo']); ?>" class="pagBtn <?php echo $i === $paginaAtual ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPaginas): ?>
                    <span class="ellipsis">...</span>
                    <a href="?controller=compra&action=relatorioVendas&pagina=<?php echo $totalPaginas; ?>&dataInicial=<?php echo urlencode($filtros['dataInicial']); ?>&dataFinal=<?php echo urlencode($filtros['dataFinal']); ?>&idUsuario=<?php echo urlencode($filtros['idUsuario']); ?>&valorMinimo=<?php echo urlencode($filtros['valorMinimo']); ?>&valorMaximo=<?php echo urlencode($filtros['valorMaximo']); ?>" class="pagBtn">
                        <?php echo $totalPaginas; ?>
                    </a>
                <?php endif; ?>
                
                <a href="?controller=compra&action=relatorioVendas&pagina=<?php echo min($totalPaginas, $paginaAtual + 1); ?>&dataInicial=<?php echo urlencode($filtros['dataInicial']); ?>&dataFinal=<?php echo urlencode($filtros['dataFinal']); ?>&idUsuario=<?php echo urlencode($filtros['idUsuario']); ?>&valorMinimo=<?php echo urlencode($filtros['valorMinimo']); ?>&valorMaximo=<?php echo urlencode($filtros['valorMaximo']); ?>" class="pagBtn"><i class="fa-solid fa-angle-right"></i></a>
                
                <a href="?controller=compra&action=relatorioVendas&pagina=<?php echo $totalPaginas; ?>&dataInicial=<?php echo urlencode($filtros['dataInicial']); ?>&dataFinal=<?php echo urlencode($filtros['dataFinal']); ?>&idUsuario=<?php echo urlencode($filtros['idUsuario']); ?>&valorMinimo=<?php echo urlencode($filtros['valorMinimo']); ?>&valorMaximo=<?php echo urlencode($filtros['valorMaximo']); ?>" class="pagBtn"><i class="fa-solid fa-angles-right"></i></a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Formatação de valores monetários
        document.querySelectorAll('#valorMinimo, #valorMaximo').forEach(function(input) {
            input.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '') return;
                
                value = (parseInt(value) / 100).toFixed(2);
                e.target.value = value.replace('.', ',');
            });
            
            input.addEventListener('focus', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '') return;
                
                e.target.value = (parseInt(value) / 100).toFixed(2).replace('.', ',');
            });
        });
    </script>
</body>
</html>