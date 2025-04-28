<?php
require_once dirname(__DIR__) . '/models/Usuario.php';
require_once dirname(__DIR__) . '/models/Admin.php';
require_once dirname(__DIR__) . '/functions/SessionTimeout.php';

class ProfileController {
    private $db;
    private $usuario;
    private $admin;


    public function __construct() {
        checkSessionTimeout();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
        $this->admin = new Admin($this->db);
    }

    public function index() {
        if (!isset($_SESSION['loggedin'])) {
            header('Location: ../public/index.php?controller=auth&action=login');
            exit;
        }

        if ($_SESSION['idTipoLogin'] == 1) {
            $userDetails = $this->usuario->getById($_SESSION['id']);
        } 
        
        if ($_SESSION['idTipoLogin'] == 2) {
            $userId = $_SESSION['id'];
            $userDetails = $this->usuario->getById($_SESSION['id']);
        }

        include dirname(__DIR__) . '/views/profile/index.php';
    }
}