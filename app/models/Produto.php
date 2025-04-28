<?php
class Produto {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function searchProdutos($searchTerm, $searchType = 'geral', $tipoProduto = null, $visibilidade = null, $page = 1, $itemsPerPage = 10) {
        try {
            $query = "SELECT p.* FROM produto p WHERE 1=1";
            
            if (!empty($searchTerm)) {
                $query .= " AND (p.nomeProduto LIKE :searchTerm 
                                OR p.descricaoProduto LIKE :searchTerm 
                                OR p.codigoProduto LIKE :searchTerm)";
            }
            
            if (!empty($tipoProduto)) {
                $query .= " AND p.idTipoProduto = :tipoProduto";
            }
            
            if (!empty($visibilidade)) {
                $query .= " AND p.idVisibilidadeProduto = :visibilidade";
            }

            $query .= " ORDER BY p.idVisibilidadeProduto ASC, p.nomeProduto ASC";
            
            $offset = ($page - 1) * $itemsPerPage;
            $query .= " LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($searchTerm)) {
                $searchTermWithWildcards = "%{$searchTerm}%";
                $stmt->bindParam(":searchTerm", $searchTermWithWildcards);
            }
            
            if (!empty($tipoProduto)) {
                $stmt->bindParam(":tipoProduto", $tipoProduto);
            }
            
            if (!empty($visibilidade)) {
                $stmt->bindParam(":visibilidade", $visibilidade);
            }
            
            $stmt->bindValue(":limit", $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count for pagination
            $countQuery = str_replace("p.*", "COUNT(*) as total", 
                substr($query, 0, strpos($query, "LIMIT")));
            $stmtCount = $this->conn->prepare($countQuery);
            
            if (!empty($searchTerm)) {
                $stmtCount->bindParam(":searchTerm", $searchTermWithWildcards);
            }
            if (!empty($tipoProduto)) {
                $stmtCount->bindParam(":tipoProduto", $tipoProduto);
            }
            if (!empty($visibilidade)) {
                $stmtCount->bindParam(":visibilidade", $visibilidade);
            }
            
            $stmtCount->execute();
            $totalCount = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Fetch images separately
            foreach ($produtos as &$produto) {
                $imageQuery = "SELECT imagemProduto FROM imagemProduto WHERE idProduto = :idProduto";
                $stmtImages = $this->conn->prepare($imageQuery);
                $stmtImages->bindParam(":idProduto", $produto['idProduto']);
                $stmtImages->execute();
                $produto['imagens'] = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return [
                'produtos' => $produtos,
                'totalItems' => $totalCount,
                'itemsPerPage' => $itemsPerPage,
                'currentPage' => $page
            ];
        } catch (PDOException $e) {
            error_log("Error searching products: " . $e->getMessage());
            return [
                'produtos' => [],
                'totalItems' => 0,
                'itemsPerPage' => $itemsPerPage,
                'currentPage' => $page
            ];
        }
    }
    
    public function getTiposProduto() {
        try {
            $query = "SELECT idTipoProduto, descricao FROM tipoProduto";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching product types: " . $e->getMessage());
            return [];
        }
    }
    
    public function getVisibilidadeProduto() {
        try {
            $query = "SELECT idVisibilidadeProduto, descricao FROM visibilidadeProduto";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching product visibility: " . $e->getMessage());
            return [];
        }
    }

    public function createProduto($data) {
        try {
            $this->conn->beginTransaction();
    
            $query = "INSERT INTO produto 
                      (nomeProduto, codigoProduto, idTipoProduto, valorProduto, 
                       descricaoProduto, idVisibilidadeProduto) 
                      VALUES 
                      (:nome, :codigo, :tipoProduto, :valor, 
                       :descricao, 1)";
            
            $stmt = $this->conn->prepare($query);
            
            // Converte valores monetários para formato do banco
            $valor = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor']);
            
            $stmt->bindParam(":nome", $data['nome']);
            $stmt->bindParam(":codigo", $data['codigo']);
            $stmt->bindParam(":tipoProduto", $data['tipoProduto']);
            $stmt->bindParam(":valor", $valor);
            $stmt->bindParam(":descricao", $data['descricao']);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao inserir produto");
            }
            
            $produtoId = $this->conn->lastInsertId();
            
            // Se não houver imagens, usar a imagem padrão
            if (empty($data['imagens'])) {
                // Corrigido o caminho da imagem padrão
                $defaultImagePath = dirname(dirname(__DIR__)) . '/public/assets/img/no-image.png';
                error_log("Trying to load default image from: " . $defaultImagePath);
                
                if (file_exists($defaultImagePath)) {
                    $defaultImageData = file_get_contents($defaultImagePath);
                    $defaultImageBase64 = base64_encode($defaultImageData);
                    $data['imagens'] = [$defaultImageBase64];
                } else {
                    error_log("Default image not found at: " . $defaultImagePath);
                    throw new Exception("Imagem padrão não encontrada em: " . $defaultImagePath);
                }
            }
            
            // Insere as imagens
            $queryImagem = "INSERT INTO imagemProduto (idProduto, imagemProduto) VALUES (:idProduto, :imagem)";
            $stmtImagem = $this->conn->prepare($queryImagem);
            
            foreach ($data['imagens'] as $imagem) {
                $stmtImagem->bindParam(":idProduto", $produtoId);
                $stmtImagem->bindParam(":imagem", $imagem);
                
                if (!$stmtImagem->execute()) {
                    throw new Exception("Erro ao inserir imagem");
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error creating product: " . $e->getMessage());
            throw $e;
        }
    }

    public function encodeImageToBase64($file) {
        if (is_uploaded_file($file['tmp_name'])) {
            $imageData = file_get_contents($file['tmp_name']);
            return base64_encode($imageData);
        }
        throw new Exception("Erro no upload da imagem.");
    }

    public function addTipoProduto($descricao) {
        try {
            // Verificar se a categoria já existe
            $checkQuery = "SELECT idTipoProduto FROM tipoProduto WHERE descricao = :descricao";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':descricao', $descricao);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'success' => false,
                    'message' => 'Esta categoria já existe.',
                    'id' => $row['idTipoProduto']
                ];
            }
    
            // Inserir nova categoria
            $query = "INSERT INTO tipoProduto (descricao) VALUES (:descricao)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':descricao', $descricao);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Categoria adicionada com sucesso!',
                    'id' => $this->conn->lastInsertId()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Erro ao adicionar categoria.'
            ];
            
        } catch (PDOException $e) {
            error_log("Erro ao adicionar tipo de produto: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao adicionar categoria: ' . $e->getMessage()
            ];
        }
    }

    public function getProdutoById($id) {
        try {
            // Fetch product details
            $query = "SELECT p.* FROM produto p WHERE p.idProduto = :id";
                          
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($produto) {
                // Fetch images separately
                $queryImages = "SELECT imagemProduto FROM imagemProduto WHERE idProduto = :id";
                $stmtImages = $this->conn->prepare($queryImages);
                $stmtImages->bindParam(":id", $id, PDO::PARAM_INT);
                $stmtImages->execute();
                
                $produto['imagens'] = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $produto;
        } catch (PDOException $e) {
            error_log("Error fetching product: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduto($data) {
        try {
            $this->conn->beginTransaction();
    
            // Update product details
            $query = "UPDATE produto 
                      SET nomeProduto = :nome,
                          codigoProduto = :codigo,
                          idTipoProduto = :tipoProduto,
                          valorProduto = :valor,
                          descricaoProduto = :descricao,
                          idVisibilidadeProduto = :visibilidadeProduto
                      WHERE idProduto = :idProduto";
            
            $stmt = $this->conn->prepare($query);
            
            // Convert monetary values
            $valor = str_replace(['R$', '.', ','], ['', '', '.'], $data['valor']);
            
            $stmt->bindParam(":nome", $data['nome']);
            $stmt->bindParam(":codigo", $data['codigo']);
            $stmt->bindParam(":tipoProduto", $data['tipoProduto']);
            $stmt->bindParam(":valor", $valor);
            $stmt->bindParam(":descricao", $data['descricao']);
            $stmt->bindParam(":visibilidadeProduto", $data['visibilidadeProduto'], PDO::PARAM_INT);
            $stmt->bindParam(":idProduto", $data['idProduto'], PDO::PARAM_INT);
            
            $stmt->execute();
    
            // Modificação para remover múltiplas imagens
            if (!empty($data['imagensRemovidas'])) {
                $placeholders = implode(',', array_fill(0, count($data['imagensRemovidas']), '?'));
                $queryRemoveImagens = "DELETE FROM imagemProduto 
                                       WHERE idProduto = ? 
                                       AND imagemProduto IN ($placeholders)";
                
                $stmtRemoveImagens = $this->conn->prepare($queryRemoveImagens);
                
                // Create parameter array
                $params = array_merge(
                    [$data['idProduto']],
                    $data['imagensRemovidas']
                );
                
                $stmtRemoveImagens->execute($params);
            }
    
            // Insert new images (mantém o código existente)
            if (!empty($data['imagens'])) {
                $queryImagem = "INSERT INTO imagemProduto (idProduto, imagemProduto) VALUES (:idProduto, :imagem)";
                $stmtImagem = $this->conn->prepare($queryImagem);
                
                foreach ($data['imagens'] as $imagem) {
                    $stmtImagem->bindParam(":idProduto", $data['idProduto'], PDO::PARAM_INT);
                    $stmtImagem->bindParam(":imagem", $imagem);
                    $stmtImagem->execute();
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }
}