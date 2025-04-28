<?php
require_once dirname(__DIR__) . '/models/Usuario.php';
require_once dirname(__DIR__) . '/models/Admin.php';
require_once dirname(__DIR__) . '/functions/SessionTimeout.php';

class HomeController {
    private $usuario;
    private $admin;

    public function __construct() {
        checkSessionTimeout();
        $database = new Database();
        $db = $database->getConnection();
        $this->usuario = new Usuario($db);
        $this->admin = new Admin($db);
    }

    public function index() {
        if (!isset($_SESSION['loggedin'])) {
            header('Location: ../public/index.php?controller=auth&action=login');
            exit;
        }

        // $adminDetails = $this->admin->getById($_SESSION['id']);

        if ($_SESSION['idTipoLogin'] == 1) {
            include dirname(__DIR__) . '/views/home/usuario.php';
            // if (isset($_GET['success'])) {
            //     include dirname(__DIR__) . '/views/produtos/list.php';
            // }
        } 
        
        
        if ($_SESSION['idTipoLogin'] == 2) {
            // $userId = $_SESSION['id'];
            // $userDetails = $this->usuario->getById($userId);

            
            include dirname(__DIR__) . '/views/home/admin.php';
        } 
    }
}