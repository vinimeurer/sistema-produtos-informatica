<?php
class Usuario {
    private $conn;

    public $nome;
    public $documento;
    public $dataCriacao;
    public $idSituacaoCadastro;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getById($id) {
        $query = "SELECT login.*, usuario.* 
          FROM login 
          INNER JOIN usuario ON login.idUsuario = usuario.idUsuario
          WHERE login.idLogin = :id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}