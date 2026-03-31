<?php
session_start();
date_default_timezone_set('Asia/Manila');
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/helpers/helpers.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/PostController.php';
require_once BASE_PATH . '/app/controllers/ProfileController.php';
require_once BASE_PATH . '/app/controllers/CommentController.php';
require_once BASE_PATH . '/app/controllers/SearchController.php';

$action = $_GET['action'] ?? 'newsfeed';

// Initialize controllers
$auth = new AuthController();
$post = new PostController();
$profile = new ProfileController();
$comment = new CommentController();
$search = new SearchController(); 

switch ($action) {
    // Auth Routes
    case 'login':
        $auth->login();
        break;
        
    case 'register':
        $auth->register();
        break;
        
    case 'logout':
        $auth->logout();
        break;

    // Newsfeed & Posts
    case 'newsfeed':
        AuthController::requireLogin();
        $post->newsfeed();
        break;
        
    case 'create_post':
        AuthController::requireLogin();
        $post->create();
        break;
        
    case 'edit_post':
        AuthController::requireLogin();
        $post->edit();
        break;
        
    case 'delete_post':
        AuthController::requireLogin();
        $post->delete();
        break;

    case 'add_comment':
        AuthController::requireLogin();
        $comment->add();
        break;
        
    case 'edit_comment':
        AuthController::requireLogin();
        $comment->edit();
        break;
        
    case 'delete_comment':
        AuthController::requireLogin();
        $comment->delete();
        break;
        
    
    case 'get_comments':
        AuthController::requireLogin();
        $comment->getComments();
        break;

    // Likes
    case 'like_post':
        AuthController::requireLogin();
        $post->like();
        break;

    // Profile
    case 'profile':
        AuthController::requireLogin();
        $profile->view();
        break;
        
    case 'edit_profile':
        AuthController::requireLogin();
        $profile->edit();
        break;

    // Search
       // Search
    case 'search':
        AuthController::requireLogin();
        $search->search();
        break;

}