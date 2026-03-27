<?php
session_start();


define('BASE_PATH', dirname(__DIR__));


require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/PostController.php';
require_once BASE_PATH . '/app/controllers/ProfileController.php';
require_once BASE_PATH . '/app/controllers/CommentController.php';


$action = $_GET['action'] ?? 'newsfeed';

// Route map
$auth = new AuthController();
//$post = new PostController();
//$profile = new ProfileController();
//$comment = new CommentController();


switch ($action) {
    // Auth
    case 'login':       $auth->login(); break;
    case 'register':    $auth->register(); break;
    case 'logout':      $auth->logout(); break;

/*
    case 'newsfeed':    $post->newsfeed(); break;
    case 'create_post': $post->create(); break;
    case 'edit_post':   $post->edit(); break;
    case 'delete_post': $post->delete(); break;

    // Comments
    case 'add_comment':    $comment->add(); break;
    case 'edit_comment':   $comment->edit(); break;
    case 'delete_comment': $comment->delete(); break;

    // Likes
    case 'like_post':   $post->like(); break;

    // Profile
    case 'profile':     $profile->view(); break;
    case 'edit_profile':$profile->edit(); break;

    // Search
    case 'search':      $post->search(); break;
*/
    default:
        if (isset($_SESSION['user_id'])) {
            $post->newsfeed();
        } else {
            $auth->login();
        }
        break;
}