<?php
$title = "Ajouter un article";
require_once 'partials/_header.php';

if (!logged_in()) redirect_to('login.php');

$categories = get_all_data('category', 'id', 100);
foreach ($categories as $index => $category) {
    $categories[$index] = $category->title;
}

$errors = [];
if (isset($_POST['post'])) {
    $submitButton = array_pop($_POST);
    $_POST = sanitize($_POST);
    $_FILES = sanitize($_FILES);
    $title = $_POST['title'];
    $category = $_POST['category'] ?? [];
    $content = $_POST['content'];


    if (!not_empty($title)) {
        $errors['title'] = 'Champ obligatoire';
    } else if (!length_validation($title, 3, 250)) {
        $errors['title'] = 'Compris entre 3 et 250 caractères';
    }
    if (empty($category)) {
        $errors['category'] = 'Champ obligatoire';

    }
    foreach ($category as $uniqueCategory) {
        if (!in_array($uniqueCategory, $categories)) {
            $errors['category'] = 'Au moins l\'une des catégorie est erronée';
        }
    }
    if (!not_empty($content)) {
        $errors['content'] = 'Champ obligatoire';
    }
    if (not_empty($_FILES['image']['name'])) {
        if ($_FILES['image']['error'] === 0) {
            extract($_FILES);
            $tmpImg = $image['tmp_name'] ?? null;
            $nameImg = $image['name'] ?? null;
            $typeImg = $image['type'] ?? null;
            if (!in_array($typeImg, ['image/jpeg', 'image/png', 'image/jpg'])) {
                $errors['image'] = 'Image invalide';
            }
        } else {
            $errors['image'] = 'image invalide';
        }
    } else {
        $errors['image'] = "L'image est obligatoire";
    }
    if (!file_exists(BASE_FILE_ROOT .'/post')) {
        mkdir( BASE_FILE_ROOT .'/post', 0777, true);
    }
    $profileImageFolder = BASE_FILE_ROOT .'/post';
    $pathImg = $profileImageFolder . '/' . ($nameImg ?? null);

    if (empty($errors)) {
        $db->beginTransaction();
        $q = $db->prepare("INSERT INTO post (title, content, image, user_id) VALUES (:title, :content, :image, :user_id)");
        $q->execute([
            'title'          =>      $title,
            'content'        =>      $content,
            'image'          =>      $pathImg,
            'user_id'        =>      $_SESSION['id']
        ]);
        $post_id = $db->lastInsertId();
        foreach ($category as $index => $item) {
            if (in_array($item, $categories)) {
//            array_keys($categories, $item, true);
                $key = array_search($item, $categories) + 1;
            }
            $q = $db->prepare("INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)");
            $q->execute([
                'post_id'           =>      $post_id,
                'category_id'       =>      $key
            ]);
        }
        $success = $db->commit();

        if ($success) {
            if(!move_uploaded_file($tmpImg, $pathImg)) $_SESSION['success'] = 'Echec lors du chargement de l\'image';

            $_SESSION['info'] = 'Article posté avec succès';
            redirect_to('post_list.php');
        } else {
            $_SESSION['warning'] = 'Echec lors de la mise à jour';
        }
    }
}

?>

<?php require_once 'views/_post.php'; ?>

<?php require_once 'partials/_footer.php';
