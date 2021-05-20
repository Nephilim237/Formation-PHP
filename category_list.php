<?php
require_once 'includes/db.php';
require_once 'includes/session_functions.php';
require_once 'includes/functions.php';

$title = 'Liste des catégories';

require_once 'partials/_header.php';

if (!logged_in()) redirect_to('login.php');

$perPage = 10; //Nombre d'éléments à afficher par page

$query = "SELECT c.id, title, c.created_at, user_id, name, firstname, email FROM category c JOIN user u ON c.user_id = u.id ";
$queryCount = "SELECT COUNT(c.id) as count FROM category c JOIN user u ON c.user_id = u.id";
$params = [];

//Gestion des paramètre de la recherche
if (!empty($_GET['q'])) {
    $query .= " WHERE title LIKE :q OR name LIKE :q OR firstname LIKE :q";
    $queryCount .= " WHERE title LIKE :q OR name LIKE :q OR firstname LIKE :q";
    $params['q'] = "%{$_GET['q']}%";
}

//Gestion des paramètre de la pagination
$page = (int)($_GET['p'] ?? 1);
$offset = ($page - 1) * $perPage;

$query .= " LIMIT $perPage OFFSET $offset";

$q = $db->prepare($query);
$q->execute($params);
$categories = $q->fetchAll(PDO::FETCH_OBJ);

$q = $db->prepare($queryCount);
$q->execute($params);
$totalElements = (int)$q->fetch()['count']; //Nombre Total des éléments provenant de la bdd
$totalPages = ceil($totalElements / $perPage); //Nombre total de page sur lesquelles tout les éléments seront afficher

?>

<?php require_once 'views/_category_list.php'?>

<?php require_once 'partials/_footer.php'; ?>
