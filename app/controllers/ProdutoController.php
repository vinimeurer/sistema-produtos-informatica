<?php
require_once dirname(__DIR__) . '/models/Produto.php';
require_once dirname(__DIR__) . '/models/Admin.php';
require_once dirname(__DIR__) . '/functions/SessionTimeout.php';

class ProdutoController {
    private $db;
    private $produto;
    private $admin;

    public function __construct() {
        checkSessionTimeout();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->produto = new Produto($this->db);
        $this->admin = new Admin($this->db);
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

    public function listProdutos() {
        if (!isset($_SESSION['loggedin'])) {
            header('Location: ../public/index.php?controller=auth&action=login');
            exit;
        }
    
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $tipoProduto = isset($_GET['tipoProduto']) ? $_GET['tipoProduto'] : null;
        $page = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        
        // For common users, set visibilidade to a default value that shows visible products
        $visibilidade = $_SESSION['idTipoLogin'] == 1 ? 1 : (isset($_GET['visibilidade']) ? $_GET['visibilidade'] : null);
    
        $result = $this->produto->searchProdutos($searchTerm, 'geral', $tipoProduto, $visibilidade, $page, 10);
        
        $produtos = $result['produtos'];
        $totalItems = $result['totalItems'];
        $itemsPerPage = $result['itemsPerPage'];
        $paginaAtual = $result['currentPage'];
        
        $totalPaginas = ceil($totalItems / $itemsPerPage);
        
        $tiposProduto = $this->produto->getTiposProduto();
        $visibilidadeProduto = $this->produto->getVisibilidadeProduto();
        
        require_once dirname(__DIR__) . '/views/produtos/list.php';
    }

    public function createProdutos() {
        $this->validateAdminAccess();

         // Buscar tipos de produto do banco
        $tiposProduto = $this->produto->getTiposProduto();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $imagens = [];
                
                // Debug
                error_log('FILES array: ' . print_r($_FILES, true));
                
                // Verifica se há imagens no upload
                if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['tmp_name'][0])) {
                    foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
                        // Verifica se é um arquivo válido
                        if (is_uploaded_file($tmp_name) && $_FILES['imagens']['error'][$key] === UPLOAD_ERR_OK) {
                            $imagemTemp = [
                                'name' => $_FILES['imagens']['name'][$key],
                                'type' => $_FILES['imagens']['type'][$key],
                                'tmp_name' => $tmp_name,
                                'error' => $_FILES['imagens']['error'][$key],
                                'size' => $_FILES['imagens']['size'][$key]
                            ];
                            
                            // Debug
                            error_log('Processando imagem: ' . $imagemTemp['name']);
                            
                            $imagens[] = $this->produto->encodeImageToBase64($imagemTemp);
                        }
                    }
                }
    
                $produtoData = [
                    'nome' => trim($_POST['nome'] ?? ''),
                    'codigo' => trim($_POST['codigo'] ?? ''),
                    'tipoProduto' => $_POST['tipoProduto'] ?? '',
                    'valor' => $_POST['valor'] ?? '',
                    'descricao' => trim($_POST['descricao'] ?? ''),
                    'imagens' => $imagens
                ];
    
                if ($this->produto->createProduto($produtoData)) {
                    echo "success=1";
                    exit;
                }
    
                throw new Exception("Erro ao criar o produto.");
    
            } catch (Exception $e) {
                error_log("Erro ao criar produto: " . $e->getMessage());
                echo "error=1";
                exit;
            }
        }
        
        require_once dirname(__DIR__) . '/views/produtos/create.php';
    }

    public function addTipoProduto() {
        $this->validateAdminAccess();
        
        // Garantir que a saída seja apenas JSON
        header('Content-Type: application/json');
        
        // Limpar qualquer saída anterior
        ob_clean();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $descricao = trim($_POST['descricao'] ?? '');
                
                if (empty($descricao)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'O nome da categoria é obrigatório.'
                    ]);
                    exit;
                }
                
                $result = $this->produto->addTipoProduto($descricao);
                echo json_encode($result);
                exit;
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao processar solicitação: ' . $e->getMessage()
                ]);
                exit;
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Método não permitido.'
        ]);
        exit;
    }

    public function editProduto() {
        $this->validateAdminAccess();
    
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ../public/index.php?controller=produto&action=listProdutos&error=1');
            exit;
        }
    
        $produto = $this->produto->getProdutoById($id);
        
        if (!$produto) {
            header('Location: ../public/index.php?controller=produto&action=listProdutos&error=1');
            exit;
        }
    
        // Buscar tipos de produto do banco
        $tiposProduto = $this->produto->getTiposProduto();
    
        require_once dirname(__DIR__) . '/views/produtos/edit.php';
    }

    public function updateProduto() {
        $this->validateAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $produtoId = $_POST['idProduto'] ?? null;
                
                // Prepare image processing
                $imagens = [];
                if (isset($_FILES['imagens'])) {
                    foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
                        if (is_uploaded_file($tmp_name) && $_FILES['imagens']['error'][$key] === UPLOAD_ERR_OK) {
                            $imagemTemp = [
                                'name' => $_FILES['imagens']['name'][$key],
                                'type' => $_FILES['imagens']['type'][$key],
                                'tmp_name' => $tmp_name,
                                'error' => $_FILES['imagens']['error'][$key],
                                'size' => $_FILES['imagens']['size'][$key]
                            ];
                            
                            $imagens[] = $this->produto->encodeImageToBase64($imagemTemp);
                        }
                    }
                }

                // Process removed images
                $imagensRemovidas = isset($_POST['imagensRemovidas']) ? 
                    explode(',', $_POST['imagensRemovidas']) : [];

                    $produtoData = [
                        'idProduto' => $produtoId,
                        'nome' => trim($_POST['nome'] ?? ''),
                        'codigo' => trim($_POST['codigo'] ?? ''),
                        'tipoProduto' => $_POST['tipoProduto'] ?? '',
                        'valor' => $_POST['valor'] ?? '',
                        'descricao' => trim($_POST['descricao'] ?? ''),
                        'visibilidadeProduto' => $_POST['visibilidadeProduto'] ?? 1, // Adicionado este campo
                        'imagens' => $imagens,
                        'imagensRemovidas' => $imagensRemovidas
                    ];

                if ($this->produto->updateProduto($produtoData)) {
                    header('Location: ../public/index.php?controller=produto&action=listProdutos&success=1');
                    exit;
                }

                throw new Exception("Erro ao atualizar o produto.");

            } catch (Exception $e) {
                error_log("Erro ao atualizar produto: " . $e->getMessage());
                header('Location: ../public/index.php?controller=produto&action=listProdutos&error=1');
                exit;
            }
        }
        
        header('Location: ../public/index.php?controller=produto&action=listProdutos');
        exit;
    }
}