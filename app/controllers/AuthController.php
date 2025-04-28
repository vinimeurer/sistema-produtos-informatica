<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Login.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }


    private function getClientIP() {

        if (!empty($_POST['localIP'])) {
            $localIP = $_POST['localIP'];
            if (filter_var($localIP, FILTER_VALIDATE_IP) && 
                (
                    strpos($localIP, '192.168.') === 0 || 
                    strpos($localIP, '10.') === 0 || 
                    preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $localIP)
                )) {
                return $localIP;
            }
        }
    
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
    
        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                if (strpos($_SERVER[$header], ',') !== false) {
                    $ips = explode(',', $_SERVER[$header]);
                    $ip = trim($ips[0]);
                } else {
                    $ip = $_SERVER[$header];
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    
        return '0.0.0.0';
    }

    public function registrarOperacao($idLogin, $tipoOperacao, $status, $detalhes = null) {
        try {
            $stmt = $this->db->prepare("INSERT INTO historicoLogin (idLogin, tipoOperacao, enderecoIP, userAgent, statusOperacao, detalhes) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $idLogin,
                $tipoOperacao,
                $this->getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                $status,
                $detalhes
            ]);
        } catch (Exception $e) {
            error_log("Erro ao registrar operação de login/logout: " . $e->getMessage());
            return false;
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username'], $_POST['password'])) {
                $this->user->login = $_POST['username'];

                try {
                    $stmt = $this->user->authenticate();

                    if ($stmt === null) {
                        $this->registrarOperacao(null, 'LOGIN', 'FALHA', 'Falha na consulta de autenticação');
                        throw new Exception("Authentication query failed");
                    }

                    if ($stmt->rowCount() > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (password_verify($_POST['password'], $row['senha'])) {
                            if ($row['idSituacaoUsuario'] == 1) {
                                $this->startSession($row);
                                $this->registrarOperacao($row['idLogin'], 'LOGIN', 'SUCESSO');
                                header('Location: ../public/index.php?controller=home&action=index&success=1');
                                exit;
                            } else {
                                $this->registrarOperacao($row['idLogin'], 'LOGIN', 'FALHA', 'Usuario inativo');
                                header('Location: ../public/index.php?controller=auth&action=login&error=1');
                            }
                        } else {
                            $this->registrarOperacao($row['idLogin'], 'LOGIN', 'FALHA', 'Senha incorreta');
                            header('Location: ../public/index.php?controller=auth&action=login&error=2');
                        }
                    } else {
                        $this->registrarOperacao(null, 'LOGIN', 'FALHA', 'Usuário não encontrado');
                        header('Location: ../public/index.php?controller=auth&action=login&error=3');
                    }
                } catch (Exception $e) {
                    error_log("Authentication error: " . $e->getMessage() . "\n" . 
                            "Stack trace: " . $e->getTraceAsString());
                    $this->registrarOperacao(null, 'LOGIN', 'FALHA', $e->getMessage());
                    header('Location: ../public/index.php?controller=auth&action=login&error=4');
                }
            } else {
                $this->registrarOperacao(null, 'LOGIN', 'FALHA', 'Dados de login incompletos');
                header('Location: ../public/index.php?controller=auth&action=login&error=5');
            }
        }

        include dirname(__DIR__) . '/views/auth/login.php';
    }

    private function startSession($userData) {
        session_save_path(dirname(__DIR__) . '/tmp');
        session_start();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['login'] = $userData['login'];
        $_SESSION['id'] = $userData['idLogin'];
        $_SESSION['idTipoLogin'] = $userData['idTipoLogin'];
        $_SESSION['nome'] = $userData['nome'];
        $_SESSION['idUsuario'] = $userData['idUsuario'];
    }

    public function logout() {
        session_start();
        if (isset($_SESSION['id'])) {
            $this->registrarOperacao($_SESSION['id'], 'LOGOUT', 'SUCESSO');
        }
        session_destroy();
        header('Location: ../public/index.php?controller=auth&action=login');
        exit;
    }
}