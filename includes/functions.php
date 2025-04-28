<?php
function redirectTo($controller, $action) {
    header("Location: ../public/index.php?controller=$controller&action=$action");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirectTo('auth', 'login');
    }
}

function isRevenda() {
    return $_SESSION['idTipoLogin'] == 1;
}

function isTecnico() {
    return $_SESSION['idTipoLogin'] == 2;
}

// Adicione outras funções úteis conforme necessário