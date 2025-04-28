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
    <title>Histórico de Login</title>
    <link rel="icon" type="image/x-icon" href="../public/assets/img/icon.ico">
    <link href="../public/assets/css/historico-alteracoes.css" rel="stylesheet" type="text/css">
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
        <h2>Histórico de Login
            <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
        </h2>

        <div id="infoModal" class="modal-legenda">
            <div class="modal-legenda-content">
                <span class="modal-legenda-close" id="closeModal">&times;</span>
                <h2>Histórico de Login</h2>
                <p>Através dessa tela é possível visualizar o histórico de login dos usuários.</p>
                <ul>
                    <li><strong>Listagem:</strong> Através da listagem é possível visualizar as seguintes informações:</li>
                    <ul>
                        <li><strong>Data:</strong> Data e hora da operação.</li>
                        <li><strong>Operação:</strong> Tipo da operação (Login ou Logout).</li>
                        <li><strong>Status:</strong> Status da operação (Sucesso ou Falha).</li>
                        <li><strong>Endereço IP:</strong> IP de origem da operação.</li>
                        <li><strong>Navegador:</strong> Informações do navegador utilizado.</li>
                        <li><strong>Detalhes:</strong> Informações adicionais sobre a operação.</li>
                        <li><strong>Usuário:</strong> Login do usuário que realizou a operação.</li>
                    </ul>
                    <br>
                    <li><strong>Filtros:</strong> É possível filtrar os resultados de pesquisa através das seguintes opções:</li>
                    <ul>
                        <li><strong>Período:</strong> informar a data inicial e data final do período que deseja visualizar.</li>
                        <li><strong>Tipo da Operação:</strong> Selecionar se é um login ou logout.</li>
                        <li><strong>Status da Operação:</strong> Selecionar se a operação teve sucesso ou falha.</li>
                    </ul>
                    <br>
                    <p>O período é limitado a 31 dias de busca.</p>
                    <button class="btn btn-secondary" id="closeModalButton">Fechar</button>
                </ul>
            </div>
        </div>

        <div>
            <form method="POST" action="" id="filter-form">
                <div class="form-group">
                    <label for="data_inicial">Data Inicial:</label>
                    <input type="date" id="data_inicial" name="data_inicial" value="<?php echo isset($filterValues['dataInicial']) ? htmlspecialchars($filterValues['dataInicial']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="data_final">Data Final:</label>
                    <input type="date" id="data_final" name="data_final" value="<?php echo isset($filterValues['dataFinal']) ? htmlspecialchars($filterValues['dataFinal']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo_operacao">Tipo de Operação:</label>
                    <select id="tipo_operacao" name="tipo_operacao">
                        <option value="">Todos</option>
                        <option value="LOGIN" <?php echo (isset($filterValues['tipoOperacao']) && $filterValues['tipoOperacao'] === 'LOGIN') ? 'selected' : ''; ?>>Login</option>
                        <option value="LOGOUT" <?php echo (isset($filterValues['tipoOperacao']) && $filterValues['tipoOperacao'] === 'LOGOUT') ? 'selected' : ''; ?>>Logout</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status_operacao">Status:</label>
                    <select id="status_operacao" name="status_operacao">
                        <option value="">Todos</option>
                        <option value="SUCESSO" <?php echo (isset($filterValues['statusOperacao']) && $filterValues['statusOperacao'] === 'SUCESSO') ? 'selected' : ''; ?>>Sucesso</option>
                        <option value="FALHA" <?php echo (isset($filterValues['statusOperacao']) && $filterValues['statusOperacao'] === 'FALHA') ? 'selected' : ''; ?>>Falha</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="submit">ㅤ</label>
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
            </form>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Operação</th>
                        <th>Status</th>
                        <th>Detalhes</th>
                        <th>Usuário</th>
                        <th>Endereço IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historico as $registro): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($registro['dataOperacao']); ?></td>
                            <td><?php echo $registro['tipoOperacao'] === 'LOGIN' ? 'Login' : 'Logout'; ?></td>
                            <td><?php echo $registro['statusOperacao'] === 'SUCESSO' ? 'Sucesso' : 'Falha'; ?></td>
                            
                            
                            <td><?php echo htmlspecialchars($registro['detalhes'] ?? "Manual"); ?></td>
                            <td class="tooltip">
                                <?php
                                    $login = $registro['login_usuario'];
                                    if (strlen($login) === 14) {
                                        echo htmlspecialchars(formatarCNPJ($login));
                                    } elseif (strlen($login) === 11) {
                                        echo htmlspecialchars(formatarCPF($login));
                                    } else {
                                        echo htmlspecialchars($login);
                                    }
                                ?>
                                <span class="tooltiptext"><?php echo htmlspecialchars($registro['nome_completo'] ?? $login); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($registro['enderecoIP']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../public/assets/js/HistoricoLogin.js"></script>
</body>
</html>