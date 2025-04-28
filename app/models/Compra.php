<?php
class Compra {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrarCompra($idUsuario, $itens) {
        try {
            $this->conn->beginTransaction();
            
            // Calcular valor total da compra
            $valorTotal = 0;
            foreach ($itens as $item) {
                $valorTotal += $item['price'] * $item['quantity'];
            }
            
            // Inserir cabeçalho da compra
            $query = "INSERT INTO compra (idUsuario, valorTotal) VALUES (:idUsuario, :valorTotal)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':valorTotal', $valorTotal, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao registrar a compra");
            }
            
            $idCompra = $this->conn->lastInsertId();
            
            // Inserir itens da compra
            $queryItem = "INSERT INTO item_compra (idCompra, idProduto, quantidade, valorUnitario, valorTotal) 
                         VALUES (:idCompra, :idProduto, :quantidade, :valorUnitario, :valorTotal)";
            $stmtItem = $this->conn->prepare($queryItem);
            
            foreach ($itens as $item) {
                $idProduto = $item['id'];
                $quantidade = $item['quantity'];
                $valorUnitario = $item['price'];
                $valorItemTotal = $valorUnitario * $quantidade;
                
                $stmtItem->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
                $stmtItem->bindParam(':idProduto', $idProduto, PDO::PARAM_INT);
                $stmtItem->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
                $stmtItem->bindParam(':valorUnitario', $valorUnitario, PDO::PARAM_STR);
                $stmtItem->bindParam(':valorTotal', $valorItemTotal, PDO::PARAM_STR);
                
                if (!$stmtItem->execute()) {
                    throw new Exception("Erro ao registrar item da compra");
                }
            }
            
            $this->conn->commit();
            return [
                'success' => true,
                'idCompra' => $idCompra
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Erro ao registrar compra: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function listarComprasUsuario($idUsuario) {
        try {
            $query = "SELECT c.*, COUNT(ic.idItemCompra) as totalItens 
                     FROM compra c 
                     LEFT JOIN item_compra ic ON c.idCompra = ic.idCompra 
                     WHERE c.idUsuario = :idUsuario 
                     GROUP BY c.idCompra 
                     ORDER BY c.dataCompra DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao listar compras: " . $e->getMessage());
            return [];
        }
    }
    
    public function getCompraDetalhes($idCompra) {
        try {
            // Dados da compra
            $queryCompra = "SELECT c.*, u.nome as nomeUsuario 
                           FROM compra c 
                           JOIN usuario u ON c.idUsuario = u.idUsuario 
                           WHERE c.idCompra = :idCompra";
            
            $stmtCompra = $this->conn->prepare($queryCompra);
            $stmtCompra->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
            $stmtCompra->execute();
            
            $compra = $stmtCompra->fetch(PDO::FETCH_ASSOC);
            
            if (!$compra) {
                return false;
            }
            
            // Itens da compra
            $queryItens = "SELECT ic.*, p.nomeProduto, p.codigoProduto 
                          FROM item_compra ic 
                          JOIN produto p ON ic.idProduto = p.idProduto 
                          WHERE ic.idCompra = :idCompra";
            
            $stmtItens = $this->conn->prepare($queryItens);
            $stmtItens->bindParam(':idCompra', $idCompra, PDO::PARAM_INT);
            $stmtItens->execute();
            
            $compra['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
            
            return $compra;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar detalhes da compra: " . $e->getMessage());
            return false;
        }
    }





    // Adicione estas funções à classe Compra existente:

    public function listarTodasCompras($filtros = []) {
        try {
            $query = "SELECT c.*, u.nome as nomeUsuario, COUNT(ic.idItemCompra) as totalItens 
                    FROM compra c 
                    JOIN usuario u ON c.idUsuario = u.idUsuario 
                    LEFT JOIN item_compra ic ON c.idCompra = ic.idCompra 
                    WHERE 1=1";
            
            $params = [];
            
            // Filtro por data inicial
            if (!empty($filtros['dataInicial'])) {
                $query .= " AND c.dataCompra >= :dataInicial";
                $params[':dataInicial'] = $filtros['dataInicial'] . ' 00:00:00';
            }
            
            // Filtro por data final
            if (!empty($filtros['dataFinal'])) {
                $query .= " AND c.dataCompra <= :dataFinal";
                $params[':dataFinal'] = $filtros['dataFinal'] . ' 23:59:59';
            }
            
            // Filtro por usuário
            if (!empty($filtros['idUsuario'])) {
                $query .= " AND c.idUsuario = :idUsuario";
                $params[':idUsuario'] = $filtros['idUsuario'];
            }
            
            // Filtro por valor mínimo
            if (isset($filtros['valorMinimo']) && $filtros['valorMinimo'] !== '') {
                $query .= " AND c.valorTotal >= :valorMinimo";
                $params[':valorMinimo'] = str_replace(',', '.', $filtros['valorMinimo']);
            }
            
            // Filtro por valor máximo
            if (isset($filtros['valorMaximo']) && $filtros['valorMaximo'] !== '') {
                $query .= " AND c.valorTotal <= :valorMaximo";
                $params[':valorMaximo'] = str_replace(',', '.', $filtros['valorMaximo']);
            }
            
            $query .= " GROUP BY c.idCompra ORDER BY c.dataCompra DESC";
            
            // Adiciona paginação se necessário
            if (isset($filtros['page']) && isset($filtros['itemsPerPage'])) {
                $offset = ($filtros['page'] - 1) * $filtros['itemsPerPage'];
                $limit = $filtros['itemsPerPage'];
                $query .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $limit;
                $params[':offset'] = $offset;
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind de parâmetros
            foreach($params as $key => $value) {
                if(in_array($key, [':limit', ':offset'])) {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular estatísticas para o relatório
            $estatisticas = $this->calcularEstatisticasCompras($filtros);
            
            return [
                'compras' => $compras,
                'estatisticas' => $estatisticas
            ];
            
        } catch (PDOException $e) {
            error_log("Erro ao listar compras: " . $e->getMessage());
            return [
                'compras' => [],
                'estatisticas' => [
                    'totalCompras' => 0,
                    'valorTotal' => 0,
                    'mediaValor' => 0
                ]
            ];
        }
    }

    private function calcularEstatisticasCompras($filtros = []) {
        try {
            $query = "SELECT 
                        COUNT(c.idCompra) as totalCompras,
                        SUM(c.valorTotal) as valorTotal,
                        AVG(c.valorTotal) as mediaValor
                    FROM compra c 
                    WHERE 1=1";
            
            $params = [];
            
            // Filtro por data inicial
            if (!empty($filtros['dataInicial'])) {
                $query .= " AND c.dataCompra >= :dataInicial";
                $params[':dataInicial'] = $filtros['dataInicial'] . ' 00:00:00';
            }
            
            // Filtro por data final
            if (!empty($filtros['dataFinal'])) {
                $query .= " AND c.dataCompra <= :dataFinal";
                $params[':dataFinal'] = $filtros['dataFinal'] . ' 23:59:59';
            }
            
            // Filtro por usuário
            if (!empty($filtros['idUsuario'])) {
                $query .= " AND c.idUsuario = :idUsuario";
                $params[':idUsuario'] = $filtros['idUsuario'];
            }
            
            // Filtro por valor mínimo
            if (isset($filtros['valorMinimo']) && $filtros['valorMinimo'] !== '') {
                $query .= " AND c.valorTotal >= :valorMinimo";
                $params[':valorMinimo'] = str_replace(',', '.', $filtros['valorMinimo']);
            }
            
            // Filtro por valor máximo
            if (isset($filtros['valorMaximo']) && $filtros['valorMaximo'] !== '') {
                $query .= " AND c.valorTotal <= :valorMaximo";
                $params[':valorMaximo'] = str_replace(',', '.', $filtros['valorMaximo']);
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind de parâmetros
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao calcular estatísticas: " . $e->getMessage());
            return [
                'totalCompras' => 0,
                'valorTotal' => 0,
                'mediaValor' => 0
            ];
        }
    }

    public function getUsuariosComCompras() {
        try {
            $query = "SELECT DISTINCT u.idUsuario, u.nome
                    FROM usuario u
                    JOIN compra c ON u.idUsuario = c.idUsuario
                    ORDER BY u.nome ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários com compras: " . $e->getMessage());
            return [];
        }
    }
}