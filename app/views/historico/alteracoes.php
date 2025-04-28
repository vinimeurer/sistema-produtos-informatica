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
    <title>Histórico de Alterações</title>
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
        <h2>Histórico de Alterações
            <span class="info-icon" id="infoIcon"><i class="fas fa-info-circle"></i></span>
        </h2>



        <div id="infoModal" class="modal-legenda">
            <div class="modal-legenda-content">
                <span class="modal-legenda-close" id="closeModal">&times;</span>
                <h2>Histórico de Alterações</h2>
                <p>Através dessa tela é possível visulizar alterações feitas em cadastros.</p>
                <ul>
                    <li><strong>Listagem:</strong> Através da listagem é possível visualizar as seguintes informações:</li>
                    <ul>
                        <li><strong>Data:</strong> Data e hora que a alteração foi feita.</li>
                        <li><strong>Operação:</strong> operação que foi realizada. Pode ser:</li>
                        <ul>
                            <li>Inclusão de um novo cadastro.</li>
                            <li>Alteração de um cadastro existente.</li>
                        </ul>
                        <li><strong>Assunto:</strong> Campo de cadastro que foi incluído ou alterado.</li>
                        <li><strong>Valor Antigo:</strong> Valor original (antes da alteração) do campo.</li>
                        <li><strong>Valor Novo:</strong> Valor novo (após a alteração) do campo.</li>
                        <li><strong>Cadastro Alterado:</strong> Documento do cadastro que foi incluído ou alterado.</li>
                        <li><strong>Quem Alterou:</strong> Documento de quem realizou a inclusão ou alteração.</li>
                    </ul>
                    <p>Nas colunas de "Cadastro Alterado" e "Quem Alterou", é possível passar o mouse em cima do documento para visulaizar o noem do referido cadastro.</p>
                    <br>
                    <li><strong>Filtros:</strong> É possível filtrar os resultados de pesquisa através das seguintes opções:</li>
                    <ul>
                        <li><strong>Período:</strong> informar a data inicial e data final do período que deseja visualizar.</li>
                        <li><strong>Tipo da Operação:</strong> Selecionar se é uma inclusão ou alteração.</li>
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
                        <option value="INSERT" <?php echo (isset($filterValues['tipoOperacao']) && $filterValues['tipoOperacao'] === 'INSERT') ? 'selected' : ''; ?>>Cadastro</option>
                        <option value="UPDATE" <?php echo (isset($filterValues['tipoOperacao']) && $filterValues['tipoOperacao'] === 'UPDATE') ? 'selected' : ''; ?>>Atualização</option>
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
                        <th>Assunto</th>
                        <th>Valor Antigo</th>
                        <th>Valor Novo</th>
                        <th>Cadastro Alterado</th>
                        <th>Quem Alterou</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($historico as $alteracao): 
                        $campo = $alteracao['campo'] ?? '';
                                    
                        if (
                            (in_array($campo, ['idTipoLogin', 'idUsuario','login']))
                        ) {
                            continue;
                        }

                        if ($campo == 'documento') {
                            $campoExibicao = 'Documento/Login';

                        } elseif ($campo == 'idSituacaoCadastro') {
                            $campoExibicao = 'Status';

                        } elseif ($campo == 'tipo_login') {
                            $campoExibicao = 'Tipo do Login';

                        } else {
                            $campoExibicao = $campo;
                        }
                ?>

                        <tr>
                            <td><?php echo htmlspecialchars($alteracao['dataAlteracao'] ?? ''); ?></td>
                            <td>
                                <?php 
                                $operacao = htmlspecialchars($alteracao['operacao'] ?? '');
                                echo $operacao === 'INSERT' ? 'Cadastro' : ($operacao === 'UPDATE' ? 'Atualização' : $operacao);
                                ?>
                            </td>
                            
                            <td><?php echo htmlspecialchars($campoExibicao); ?></td>
                            <td>
                                <?php 
                                $valorAntigo = htmlspecialchars($alteracao['valorAntigo'] ?? 'N/A'); 
                                $valorAntigoExibido = strlen($valorAntigo) > 15 ? substr($valorAntigo, 0, 15) . '...' : $valorAntigo;
                                echo $valorAntigoExibido === '1' ? 'Ativo' : ($valorAntigoExibido === '2' ? 'Inativo' : $valorAntigoExibido);
                                ?>
                            </td>
                            <td>
                                <?php 
                                $valorNovo = htmlspecialchars($alteracao['valorNovo'] ?? 'N/A'); 
                                $valorNovoExibido = strlen($valorNovo) > 18 ? substr($valorNovo, 0, 18) . '...' : $valorNovo;
                                echo $valorNovoExibido === '1' ? 'Ativo' : ($valorNovoExibido === '2' ? 'Inativo' : $valorNovoExibido);
                                ?>
                            </td>

                            <td class="tooltip">
                                <?php
                                    $login = $alteracao['login_alterado'];
                                    
                                    if (strlen($login) === 14) {
                                        echo htmlspecialchars(formatarCNPJ($login));
                                    } elseif (strlen($login) === 11) {
                                        echo htmlspecialchars(formatarCPF($login));
                                    } else {
                                        echo htmlspecialchars($login);
                                    }
                                ?>
                            <span class="tooltiptext"><?php echo htmlspecialchars($alteracao['nome_completo_alterado'] ?? $login); ?></span>

                        
                            </td>
                            <td class="tooltip">
                                <?php
                                    $login = $alteracao['login_alterou'];
                                    
                                    if (strlen($login) === 14) {
                                        echo htmlspecialchars(formatarCNPJ($login));
                                    } elseif (strlen($login) === 11) {
                                        echo htmlspecialchars(formatarCPF($login));
                                    } else {
                                        echo htmlspecialchars($login);
                                    }
                                ?>

                            <span class="tooltiptext"><?php echo htmlspecialchars($alteracao['nome_completo_alterou'] ?? $login); ?></span>

                        
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src ="../public/assets/js/HistoricoAlteracoes.js"></script>
</body>
</html>