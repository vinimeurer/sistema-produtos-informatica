<?php
class User {
    private $conn;

    public $id;
    public $idUsuario;
    public $idTipoLogin;
    public $login;
    public $senha;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate() {
        try {
            $queryUsuario = "SELECT l.idLogin, l.idTipoLogin, l.login, l.senha, l.dataCriacao, 
                             u.nome, l.idSituacaoUsuario, u.idUsuario
                             FROM login l
                             INNER JOIN usuario u ON l.idUsuario = u.idUsuario
                             WHERE l.login = :login 
                             AND l.idSituacaoUsuario = 1 AND u.idSituacaoUsuario = 1";
    
            $stmt = $this->conn->prepare($queryUsuario);
            $stmt->bindParam(":login", $this->login);
            $stmt->execute();
    
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    public function getById($id) {
        $query = "SELECT l.idLogin, l.idusuario, l.idTipoLogin, l.login, l.dataCriacao, l.idSituacaoUsuario 
                  FROM login l 
                  WHERE l.idLogin = :id AND l.idSituacaoUsuario = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}