<?php
require_once dirname(__DIR__) . '/models/Admin.php';
require_once dirname(__DIR__) . '/models/Usuario.php';
require_once dirname(__DIR__) . '/functions/SessionTimeout.php';


class AdminController {
    private $db;
    private $admin;
    private $usuario;

    public function __construct() {
        checkSessionTimeout();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->admin = new Admin($this->db);
        $this->usuario = new Usuario($this->db);
    }

    private function validateAdminAccess() {
        if (!isset($_SESSION['loggedin'])) {
            header('Location: ../public/index.php?controller=auth&action=login');
            exit;
        }
        
        if (!isset($_SESSION['idTipoLogin']) || $_SESSION['idTipoLogin'] != 2) {
            header('Location: ../public/index.php?controller=produto&action=listProdutos&error=unauthorized');
            exit;
        }
    }

    public function listUsuarios() {
        $this->validateAdminAccess();

        $usuarioDetails = $this->usuario->getById($_SESSION['id']);
        $adminDetails = $this->admin->getById($_SESSION['id']);

        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'nome';
        $status = isset($_GET['status']) ? $_GET['status'] : 'todos';

        if (!empty($searchTerm) || $status !== 'todos') {
            $usuarios = $this->admin->searchUsuarios($searchType, $searchTerm, $status);
        } else {
            $usuarios = $this->admin->getAllUsuarios();
        }

        require_once dirname(__DIR__) . '/views/usuarios/list.php';
    }

    


    public function createUsuario() {
        $this->validateAdminAccess();
    
        $usuarioDetails = $this->usuario->getById($_SESSION['id']);
        $adminDetails = $this->admin->getById($_SESSION['id']);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nome' => $_POST['nome'],
                'documento' => $_POST['documento'],
                'login' => $_POST['login'],
                'senha' => $_POST['senha'],
                'tipoLogin' => $_POST['tipoLogin'],
                // Dados de endereço
                'cep' => $_POST['cep'],
                'uf' => $_POST['uf'],
                'municipio' => $_POST['municipio'],
                'rua' => $_POST['rua'],
                'numero' => $_POST['numero'],
                'complemento' => $_POST['complemento'] ?? ''
            ];
            
            if ($this->admin->createUsuario($data)) {
                header('Location: ../public/index.php?controller=admin&action=listUsuarios&success=1');
                exit;
            } else {
                header('Location: ../public/index.php?controller=admin&action=createUsuario&error=1');
                exit;
            }
        } else {
            require_once dirname(__DIR__) . '/views/usuarios/create.php';
        }
    }
    
    public function editUsuario($id = null) {
        $this->validateAdminAccess();
    
        $usuarioDetails = $this->usuario->getById($_SESSION['id']);
        $adminDetails = $this->admin->getById($_SESSION['id']);
    
        if ($id === null) {
            $id = isset($_GET['id']) ? $_GET['id'] : null;
        }
        
        if ($id === null) {
            header('Location: ../public/index.php?controller=admin&action=listUsuarios&error=ID do usuário não fornecido');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nome' => $_POST['nome'],
                'documento' => $_POST['documento'],
                'login' => $_POST['login'],
                'senha' => $_POST['senha'] ?? '',
                'tipoLogin' => $_POST['tipoLogin'],
                'status' => $_POST['status'],
                // Dados de endereço
                'cep' => $_POST['cep'],
                'uf' => $_POST['uf'],
                'municipio' => $_POST['municipio'],
                'rua' => $_POST['rua'],
                'numero' => $_POST['numero'],
                'complemento' => $_POST['complemento'] ?? ''
            ];
            
            if ($this->admin->updateUsuario($id, $data)) {
                header('Location: ../public/index.php?controller=admin&action=listUsuarios&success=1');
                exit;
            } else {
                header("Location: ../public/index.php?controller=admin&action=editUsuario&id=$id&error=1");
                exit;
            }
        } else {
            $usuario = $this->admin->getUsuarioById($id);
            if ($usuario) {
                require_once dirname(__DIR__) . '/views/usuarios/edit.php';
            } else {
                header('Location: ../public/index.php?controller=admin&action=listUsuarios&error=Usuário não encontrado');
                exit;
            }
        }
    }




    public function historicoLogin() {
        $this->validateAdminAccess();

        $dataFinal = date('Y-m-d');
        $dataInicial = date('Y-m-d');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataInicial = $_POST['data_inicial'] ?? $dataInicial;
            $dataFinal = $_POST['data_final'] ?? $dataFinal;
            $tipoOperacao = $_POST['tipo_operacao'] ?? '';
            $statusOperacao = $_POST['status_operacao'] ?? '';
        } else {
            $tipoOperacao = '';
            $statusOperacao = '';
        }

        $diff = strtotime($dataFinal) - strtotime($dataInicial);
        if ($diff < 0) {
            $dataFinal = $dataInicial;
        }

        $historico = $this->getHistoricoLogin($dataInicial, $dataFinal, $tipoOperacao, $statusOperacao);
        
        $filterValues = [
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
            'tipoOperacao' => $tipoOperacao,
            'statusOperacao' => $statusOperacao
        ];
        
        require_once dirname(__DIR__) . '/views/historico/login.php';
    }

    private function getHistoricoLogin($dataInicial, $dataFinal, $tipoOperacao = '', $statusOperacao = '') {
        $query = "SELECT 
                    h.dataOperacao,
                    h.tipoOperacao,
                    h.enderecoIP,
                    h.userAgent,
                    h.statusOperacao,
                    h.detalhes,
                    l.login AS login_usuario,
                    u.nome AS nome_completo
                FROM historicoLogin h
                LEFT JOIN login l ON h.idLogin = l.idLogin
                LEFT JOIN usuario u ON l.idUsuario = u.idUsuario
                WHERE DATE(h.dataOperacao) >= :dataInicial 
                AND DATE(h.dataOperacao) <= :dataFinal";

        if ($tipoOperacao) {
            $query .= " AND h.tipoOperacao = :tipoOperacao";
        }

        if ($statusOperacao) {
            $query .= " AND h.statusOperacao = :statusOperacao";
        }
        
        $query .= " ORDER BY h.dataOperacao DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":dataInicial", $dataInicial);
        $stmt->bindParam(":dataFinal", $dataFinal);
        
        if ($tipoOperacao) {
            $stmt->bindParam(":tipoOperacao", $tipoOperacao);
        }

        if ($statusOperacao) {
            $stmt->bindParam(":statusOperacao", $statusOperacao);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }





    public function historicoAlteracoesUsuario() {
        $this->validateAdminAccess();
    
        $adminDetails = $this->admin->getById($_SESSION['id']);
    
        $dataFinal = date('Y-m-d');
        $dataInicial = date('Y-m-d', strtotime('-31 days'));
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataInicial = $_POST['data_inicial'] ?? $dataInicial;
            $dataFinal = $_POST['data_final'] ?? $dataFinal;
            $tipoOperacao = $_POST['tipo_operacao'] ?? '';
        } else {
            $tipoOperacao = '';
        }
    
        $diff = strtotime($dataFinal) - strtotime($dataInicial);
        if ($diff < 0 || $diff > 31 * 24 * 60 * 60) {
            $dataFinal = date('Y-m-d');
            $dataInicial = date('Y-m-d', strtotime('-31 days'));
        }
        
        $historico = $this->admin->getHistoricoAlteracoesUsuario($dataInicial, $dataFinal, $tipoOperacao);
        
        $filterValues = [
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal,
            'tipoOperacao' => $tipoOperacao
        ];
        
        require_once dirname(__DIR__) . '/views/historico/alteracoes.php';
    }
}
?>