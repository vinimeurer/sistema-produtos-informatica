<?php
function checkSessionTimeout() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Tempo de inatividade em segundos (por exemplo, 30 minutos = 1800 segundos)
    $timeout_duration = 900; 
    
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
        
        require_once dirname(__DIR__) . '/config/database.php';
        require_once dirname(__DIR__) . '/controllers/AuthController.php';
        
        $database = new Database();
        $db = $database->getConnection();
        $authController = new AuthController();
        
        if (isset($_SESSION['id'])) {
            $authController->registrarOperacao($_SESSION['id'], 'LOGOUT', 'SUCESSO', 'Tempo Expirado');
        } else {
            $authController->registrarOperacao(null, 'LOGOUT', 'SUCESSO', 'Tempo Expirado');
        }

        session_unset();     
        session_destroy(); 
        
        header('Location: ../public/index.php?controller=auth&action=login&timeout=1');
        exit();
    }
    
    $_SESSION['LAST_ACTIVITY'] = time();
}