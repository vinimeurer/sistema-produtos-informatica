<?php
require_once dirname(__DIR__) . '/models/Compra.php';
require_once dirname(__DIR__) . '/functions/SessionTimeout.php';

class CompraController {
    private $db;
    private $compra;

    public function __construct() {
        checkSessionTimeout();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->compra = new Compra($this->db);
    }

    public function finalizarCompra() {
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['idUsuario'])) {
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
            exit;
        }

        // Verificar se é um usuário comum
        if ($_SESSION['idTipoLogin'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Apenas usuários comuns podem finalizar compras']);
            exit;
        }

        // Receber dados do POST (formato JSON)
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['itens']) || empty($data['itens'])) {
            echo json_encode(['success' => false, 'message' => 'Nenhum item no carrinho']);
            exit;
        }

        $idUsuario = $_SESSION['idUsuario'];
        $result = $this->compra->registrarCompra($idUsuario, $data['itens']);

        echo json_encode($result);
        exit;
    }

    public function minhasCompras() {
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['idUsuario'])) {
            header('Location: ../public/index.php?controller=auth&action=login');
            exit;
        }

        $idUsuario = $_SESSION['idUsuario'];
        $compras = $this->compra->listarComprasUsuario($idUsuario);

        require_once dirname(__DIR__) . '/views/compras/compras.php';
    }

    public function detalhesCompra() {
        if (!isset($_SESSION['loggedin']) || !isset($_SESSION['idUsuario'])) {
            header('Location: ../public/index.php?controller=auth&action=login');
            exit;
        }

        $idCompra = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($idCompra <= 0) {
            header('Location: ../public/index.php?controller=compra&action=minhasCompras&error=1');
            exit;
        }

        $compra = $this->compra->getCompraDetalhes($idCompra);
        
        if (!$compra || $compra['idUsuario'] != $_SESSION['idUsuario']) {
            header('Location: ../public/index.php?controller=compra&action=minhasCompras&error=1');
            exit;
        }

        require_once dirname(__DIR__) . '/views/compras/detalhes.php';
    }
}