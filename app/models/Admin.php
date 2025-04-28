<?php
class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getById($id) {
        $query = "SELECT u.*, l.*
            FROM usuario u
            INNER JOIN login l ON u.idUsuario = l.idUsuario
            WHERE l.idTipoLogin = 2 AND l.idLogin = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsuarios() {
        $query = "SELECT u.idUsuario, u.nome, u.documento, u.dataCriacao, u.idSituacaoUsuario, l.idLogin,
                         l.login, l.idTipoLogin
                  FROM login l
                  INNER JOIN usuario u ON l.idUsuario = u.idUsuario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchUsuarios($searchType, $searchTerm, $status) {
        $query = "SELECT u.idUsuario, u.nome, u.documento, u.dataCriacao, u.idSituacaoUsuario, l.idLogin,
                         l.login, l.idTipoLogin
                  FROM usuario u
                  INNER JOIN login l ON u.idUsuario = l.idUsuario
                  WHERE 1=1";

        if (!empty($searchTerm)) {
            if ($searchType === 'nome') {
                $query .= " AND u.nome LIKE :searchTerm";
            } elseif ($searchType === 'documento') {
                $query .= " AND u.documento LIKE :searchTerm";
            }
        }

        if ($status !== 'todos') {
            $query .= " AND u.idSituacaoUsuario = :status";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($searchTerm)) {
            $searchTermWithWildcards = "%{$searchTerm}%";
            $stmt->bindParam(":searchTerm", $searchTermWithWildcards);
        }

        if ($status !== 'todos') {
            $stmt->bindParam(":status", $status);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUsuario($data) {
        $this->conn->beginTransaction();
    
        try {
            $queryUsuario = "INSERT INTO usuario (nome, documento, idSituacaoUsuario) 
                            VALUES (:nome, :documento, :idSituacaoUsuario)";
            
            $stmtUsuario = $this->conn->prepare($queryUsuario);
            
            $idSituacaoUsuario = 1;
            $stmtUsuario->bindParam(":nome", $data['nome']);
            $stmtUsuario->bindParam(":documento", $data['documento']);
            $stmtUsuario->bindParam(":idSituacaoUsuario", $idSituacaoUsuario);
    
            if ($stmtUsuario->execute()) {
                $usuarioId = $this->conn->lastInsertId();
                
                // Inserir endereço
                $queryEndereco = "INSERT INTO endereco (idUsuario, cep, uf, municipio, rua, numero, complemento)
                                 VALUES (:idUsuario, :cep, :uf, :municipio, :rua, :numero, :complemento)";
                
                $stmtEndereco = $this->conn->prepare($queryEndereco);
                $stmtEndereco->bindParam(":idUsuario", $usuarioId);
                $stmtEndereco->bindParam(":cep", $data['cep']);
                $stmtEndereco->bindParam(":uf", $data['uf']);
                $stmtEndereco->bindParam(":municipio", $data['municipio']);
                $stmtEndereco->bindParam(":rua", $data['rua']);
                $stmtEndereco->bindParam(":numero", $data['numero']);
                $stmtEndereco->bindParam(":complemento", $data['complemento']);
                
                $stmtEndereco->execute();
                
                // Inserir login
                $hash = password_hash($data['senha'], PASSWORD_BCRYPT);
                $queryLogin = "INSERT INTO login 
                              (idUsuario, login, senha, idTipoLogin, idSituacaoUsuario) 
                              VALUES 
                              (:idUsuario, :login, :senha, :idTipoLogin, :idSituacaoUsuario)";
        
                $stmtLogin = $this->conn->prepare($queryLogin);
    
                $idSituacaoUsuario = 1;
                $stmtLogin->bindParam(":idUsuario", $usuarioId);
                $stmtLogin->bindParam(":login", $data['login']);
                $stmtLogin->bindParam(":senha", $hash);
                $stmtLogin->bindParam(":idTipoLogin", $data['tipoLogin']);
                $stmtLogin->bindParam(":idSituacaoUsuario", $idSituacaoUsuario);
        
                if ($stmtLogin->execute()) {
                    $loginId = $this->conn->lastInsertId();
                    $idLoginAlterador = $_SESSION['id'];
    
                    $this->registrarAlteracao('usuario', 'INSERT', $loginId, 'nome', null, $data['nome'], $idLoginAlterador);
                    $this->registrarAlteracao('usuario', 'INSERT', $loginId, 'documento', null, $data['documento'], $idLoginAlterador);
                    $this->registrarAlteracao('login', 'INSERT', $loginId, 'login', null, $data['login'], $idLoginAlterador);
    
                    // Registrar alterações de endereço
                    $this->registrarAlteracao('endereco', 'INSERT', $loginId, 'cep', null, $data['cep'], $idLoginAlterador);
                    $this->registrarAlteracao('endereco', 'INSERT', $loginId, 'endereco', null, 
                        $data['rua'] . ', ' . $data['numero'] . ' - ' . $data['municipio'] . '/' . $data['uf'], 
                        $idLoginAlterador);
    
                    $tipoLoginTexto = $data['tipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador';
                    $this->registrarAlteracao('login', 'INSERT', $loginId, 'tipo_login', null, $tipoLoginTexto, $idLoginAlterador);
                    
                    $this->conn->commit();
                    return $usuarioId;
                }
            }
        
            $this->conn->rollBack();
            return false;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getUsuarioById($id) {
        $query = "SELECT u.*, l.*, e.*
                  FROM usuario u
                  INNER JOIN login l ON u.idUsuario = l.idUsuario
                  LEFT JOIN endereco e ON u.idUsuario = e.idUsuario
                  WHERE u.idUsuario = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUsuario($id, $data) {
        $this->conn->beginTransaction();
    
        try {
            $usuarioAntigo = $this->getUsuarioById($id);
            $idLoginAlterador = $_SESSION['id'];
    
            $queryUpdateUsuario = "UPDATE usuario 
                                 SET nome = :nome,
                                     documento = :documento,
                                     idSituacaoUsuario = :status
                                 WHERE idUsuario = :id";
    
            $stmtUpdateUsuario = $this->conn->prepare($queryUpdateUsuario);
            $stmtUpdateUsuario->bindParam(":nome", $data['nome']);
            $stmtUpdateUsuario->bindParam(":documento", $data['documento']);
            $stmtUpdateUsuario->bindParam(":status", $data['status']);
            $stmtUpdateUsuario->bindParam(":id", $id);
    
            if ($stmtUpdateUsuario->execute()) {
                if ($usuarioAntigo['nome'] != $data['nome']) {
                    $this->registrarAlteracao('usuario', 'UPDATE', $id, 'nome', $usuarioAntigo['nome'], $data['nome'], $idLoginAlterador);
                }
                if ($usuarioAntigo['documento'] != $data['documento']) {
                    $this->registrarAlteracao('usuario', 'UPDATE', $id, 'documento', $usuarioAntigo['documento'], $data['documento'], $idLoginAlterador);
                }
                if ($usuarioAntigo['idSituacaoUsuario'] != $data['status']) {
                    $statusAntigo = $usuarioAntigo['idSituacaoUsuario'] == 1 ? 'Ativo' : 'Inativo';
                    $statusNovo = $data['status'] == 1 ? 'Ativo' : 'Inativo';
                    $this->registrarAlteracao('usuario', 'UPDATE', $id, 'status', $statusAntigo, $statusNovo, $idLoginAlterador);
                }
    
                // Atualizar endereço
                // Verificar se já existe um endereço para este usuário
                $queryCheckEndereco = "SELECT COUNT(*) FROM endereco WHERE idUsuario = :idUsuario";
                $stmtCheckEndereco = $this->conn->prepare($queryCheckEndereco);
                $stmtCheckEndereco->bindParam(":idUsuario", $id);
                $stmtCheckEndereco->execute();
                $enderecoExists = $stmtCheckEndereco->fetchColumn() > 0;
    
                if ($enderecoExists) {
                    $queryUpdateEndereco = "UPDATE endereco 
                                         SET cep = :cep,
                                             uf = :uf,
                                             municipio = :municipio,
                                             rua = :rua,
                                             numero = :numero,
                                             complemento = :complemento
                                         WHERE idUsuario = :idUsuario";
                    
                    $stmtUpdateEndereco = $this->conn->prepare($queryUpdateEndereco);
                } else {
                    $queryUpdateEndereco = "INSERT INTO endereco 
                                         (idUsuario, cep, uf, municipio, rua, numero, complemento)
                                         VALUES 
                                         (:idUsuario, :cep, :uf, :municipio, :rua, :numero, :complemento)";
                    
                    $stmtUpdateEndereco = $this->conn->prepare($queryUpdateEndereco);
                }
    
                $stmtUpdateEndereco->bindParam(":idUsuario", $id);
                $stmtUpdateEndereco->bindParam(":cep", $data['cep']);
                $stmtUpdateEndereco->bindParam(":uf", $data['uf']);
                $stmtUpdateEndereco->bindParam(":municipio", $data['municipio']);
                $stmtUpdateEndereco->bindParam(":rua", $data['rua']);
                $stmtUpdateEndereco->bindParam(":numero", $data['numero']);
                $stmtUpdateEndereco->bindParam(":complemento", $data['complemento']);
                
                $stmtUpdateEndereco->execute();
                
                // Registrar alterações de endereço se houver
                if ($enderecoExists) {
                    if ($usuarioAntigo['cep'] != $data['cep']) {
                        $this->registrarAlteracao('endereco', 'UPDATE', $id, 'cep', $usuarioAntigo['cep'], $data['cep'], $idLoginAlterador);
                    }
                    
                    $enderecoAntigo = $usuarioAntigo['rua'] . ', ' . $usuarioAntigo['numero'] . ' - ' . $usuarioAntigo['municipio'] . '/' . $usuarioAntigo['uf'];
                    $enderecoNovo = $data['rua'] . ', ' . $data['numero'] . ' - ' . $data['municipio'] . '/' . $data['uf'];
                    
                    if ($enderecoAntigo != $enderecoNovo) {
                        $this->registrarAlteracao('endereco', 'UPDATE', $id, 'endereco', $enderecoAntigo, $enderecoNovo, $idLoginAlterador);
                    }
                } else {
                    $this->registrarAlteracao('endereco', 'INSERT', $id, 'cep', null, $data['cep'], $idLoginAlterador);
                    $this->registrarAlteracao('endereco', 'INSERT', $id, 'endereco', null, 
                        $data['rua'] . ', ' . $data['numero'] . ' - ' . $data['municipio'] . '/' . $data['uf'], 
                        $idLoginAlterador);
                }
    
                // Atualizar login (código existente)
                $queryUpdateLogin = "UPDATE login 
                                   SET login = :login" .
                                   (isset($data['senha']) && trim($data['senha']) !== '' ? ", senha = :senha" : "") . 
                                   ", idTipoLogin = :tipoLogin,
                                     idSituacaoUsuario = :status
                                   WHERE idUsuario = :idUsuario";
                
                $stmtUpdateLogin = $this->conn->prepare($queryUpdateLogin);
                $stmtUpdateLogin->bindParam(":login", $data['login']);
                $stmtUpdateLogin->bindParam(":tipoLogin", $data['tipoLogin']);
                $stmtUpdateLogin->bindParam(":status", $data['status']);
                $stmtUpdateLogin->bindParam(":idUsuario", $id);
            
                if (isset($data['senha']) && trim($data['senha']) !== '') {
                    $hash = password_hash($data['senha'], PASSWORD_BCRYPT);
                    $stmtUpdateLogin->bindParam(":senha", $hash);
                }
            
                if ($stmtUpdateLogin->execute()) {
                    if ($usuarioAntigo['login'] != $data['login']) {
                        $this->registrarAlteracao('login', 'UPDATE', $id, 'login', $usuarioAntigo['login'], $data['login'], $idLoginAlterador);
                    }
                    if (isset($data['senha']) && trim($data['senha']) !== '') {
                        $this->registrarAlteracao('login', 'UPDATE', $id, 'senha', '********', '********', $idLoginAlterador);
                    }
                    if ($usuarioAntigo['idTipoLogin'] != $data['tipoLogin']) {
                        $tipoAntigo = $usuarioAntigo['idTipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador';
                        $tipoNovo = $data['tipoLogin'] == 1 ? 'Usuário Comum' : 'Administrador';
                        $this->registrarAlteracao('login', 'UPDATE', $id, 'tipo_login', $tipoAntigo, $tipoNovo, $idLoginAlterador);
                    }
    
                    $this->conn->commit();
                    return true;
                }
            }
        
            $this->conn->rollBack();
            return false;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    private function registrarAlteracao($tabela, $operacao, $idRegistro, $campo, $valorAntigo, $valorNovo, $idLogin) {
        $query = "INSERT INTO historicoAlteracoesUsuario (tabela, operacao, idRegistro, campo, valorAntigo, valorNovo, idLogin) 
                  VALUES (:tabela, :operacao, :idRegistro, :campo, :valorAntigo, :valorNovo, :idLogin)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":tabela", $tabela);
        $stmt->bindParam(":operacao", $operacao);
        $stmt->bindParam(":idRegistro", $idRegistro);
        $stmt->bindParam(":campo", $campo);
        $stmt->bindParam(":valorAntigo", $valorAntigo);
        $stmt->bindParam(":valorNovo", $valorNovo);
        $stmt->bindParam(":idLogin", $idLogin);

        return $stmt->execute();
    }

    public function getHistoricoAlteracoesUsuario($dataInicial, $dataFinal, $tipoOperacao = '') {
        $dataFinalAjustada = date('Y-m-d', strtotime($dataFinal . ' +1 day'));
    
        $query = "SELECT 
                historicoAlteracoesUsuario.dataAlteracao,
                historicoAlteracoesUsuario.operacao,
                historicoAlteracoesUsuario.campo,
                historicoAlteracoesUsuario.valorAntigo,
                historicoAlteracoesUsuario.valorNovo,
                loginAlterado.login AS login_alterado,
                usuarioAlterado.nome AS nome_completo_alterado,
                loginAlterando.login AS login_alterou,
                usuarioAlterando.nome AS nome_completo_alterou
            FROM historicoAlteracoesUsuario
            INNER JOIN login AS loginAlterado ON historicoAlteracoesUsuario.idRegistro = loginAlterado.idLogin
            LEFT JOIN usuario AS usuarioAlterado ON loginAlterado.idUsuario = usuarioAlterado.idUsuario
            INNER JOIN login AS loginAlterando ON historicoAlteracoesUsuario.idLogin = loginAlterando.idLogin
            LEFT JOIN usuario AS usuarioAlterando ON loginAlterando.idUsuario = usuarioAlterando.idUsuario
            WHERE dataAlteracao >= :dataInicial 
            AND dataAlteracao <= :dataFinal";
    
        if ($tipoOperacao) {
            $query .= " AND historicoAlteracoesUsuario.operacao = :tipoOperacao";
        }
    
        $query .= " ORDER BY historicoAlteracoesUsuario.dataAlteracao DESC";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":dataInicial", $dataInicial);
        $stmt->bindParam(":dataFinal", $dataFinalAjustada);
        
        if ($tipoOperacao) {
            $stmt->bindParam(":tipoOperacao", $tipoOperacao);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>