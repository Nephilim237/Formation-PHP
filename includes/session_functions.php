<?php
date_default_timezone_set('Africa/Douala');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');


if (session_status() === PHP_SESSION_NONE) session_start();

function display_session_alert(string $type = 'success')
{
//    if ($type === 'success' || $type === null) $alertType = 'success'; else $alertType = 'warning';

    if (isset($_SESSION[$type])) {
        $alert = "<div class=\"alert alert-$type text-center fs-5 col-md-10 mx-auto\">
            $_SESSION[$type]
        </div>";
        unset($_SESSION[$type]);
    } else {
        $alert = '';
    }

    return $alert;
}

/**
 * Permet d'afficher les information de l'utilisateur connecté
 *
 * @param string|null $param Représente le nom de l'élément stocké dans la session. Ca peur être le nom, l'email ou meme l'id et bien d'autres informations concernant l'utilisateur connecté.
 *
 * @return string|null Renvoie la valeur contenue dans l'élément demandé. Par exemple ds_info('name') renverra le nom de l'utilisateur connecté tandis que ds_info('email') renverra son email.
 */
function ds_info(?string $param = null): ?string {

    return $_SESSION[$param] ?? null;
}

function logged_in() {
    return isset($_SESSION['id'], $_SESSION['email'], $_SESSION['role']) ?: false;
}


function modo() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'modo' ?: false;
}

function admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ?: false;
}

function super() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super' ?: false;
}
