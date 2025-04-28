<?php
// Define o caminho base do projeto
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Configura o caminho para salvar as sessões
session_save_path(dirname(__DIR__) . '/tmp');

// Inicia a sessão
session_start();

// Inclui os arquivos necessários
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Determina o controlador e a ação
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Constrói o nome da classe do controlador
$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile = dirname(__DIR__) . '/app/controllers/' . $controllerClass . '.php';

// Verifica se o arquivo do controlador existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Cria uma instância do controlador
    // Passando a conexão do banco de dados, se necessário
    $controller = new $controllerClass();
    
    // Verifica se o método da ação existe
    if (method_exists($controller, $action)) {
        // Chama o método da ação
        $controller->$action();
    } else {
        // Ação não encontrada
        die('Action not found');
    }
} else {
    // Controlador não encontrado
    die('Controller not found');
}
?>