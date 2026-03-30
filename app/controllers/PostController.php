<?php
require_once BASE_PATH . '/app/models/PostModel.php';
require_once BASE_PATH . '/app/models/LikeModel.php';
require_once BASE_PATH . '/app/models/CommentModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class PostController {
    private $postModel;
    private $likeModel;
    private $commentModel;
    private $userModel; 

    public function __construct() {
        $this->postModel    = new PostModel();
        $this->likeModel    = new LikeModel();
        $this->commentModel = new CommentModel();
        $this->userModel    = new UserModel(); 
    }

    // Display newsfeed
    public function newsfeed() {
        AuthController::requireLogin();
        $userId      = $_SESSION['user_id'];
        $posts       = $this->postModel->getNewsfeedPosts($userId);
        $userFandoms = $this->postModel->getUserFandoms($userId);
        require_once BASE_PATH . '/app/views/posts/newsfeed.php';
    }

    // Create post
    public function create() {
        AuthController::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleCreate();
        }
        require_once BASE_PATH . '/app/views/posts/create.php';
    }

    private function handleCreate() {
        $userId    = $_SESSION['user_id'];
        $content   = trim($_POST['content'] ?? '');
        $fandomTag = $_POST['fandom_tag'] ?? 'Anime';

        if (empty($content)) {
            $error = 'Please write something before posting.';
            require_once BASE_PATH . '/app/views/posts/create.php';
            return;
        }

        $this->postModel->createPost($userId, $content, $fandomTag, null);
        header('Location: index.php?action=newsfeed');
        exit;
    }

    // Edit post
    public function edit() {
        AuthController::requireLogin();
        $userId = $_SESSION['user_id'];
        $postId = intval($_GET['id'] ?? 0);
        $post   = $this->postModel->getPostById($postId);

        if (!$post || $post['user_id'] != $userId) {
            header('Location: index.php?action=newsfeed');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEdit($postId);
        }

        $error = '';
        require_once BASE_PATH . '/app/views/posts/edit.php';
    }

    private function handleEdit($postId) {
        $content = trim($_POST['content'] ?? '');
        if (empty($content)) {
            $error = 'Post content cannot be empty.';
            $post  = $this->postModel->getPostById($postId);
            require_once BASE_PATH . '/app/views/posts/edit.php';
            return;
        }

        $this->postModel->updatePost($postId, $content);
        header('Location: index.php?action=newsfeed');
        exit;
    }

    // Delete post
    public function delete() {
        AuthController::requireLogin();
        $userId = $_SESSION['user_id'];
        $postId = intval($_POST['post_id'] ?? 0);

        if ($this->postModel->isOwner($postId, $userId)) {
            $this->commentModel->deleteCommentsByPostId($postId);
            $this->postModel->deletePost($postId);
        }

        header('Location: index.php?action=newsfeed');
        exit;
    }

    // Like post
    public function like() {
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $postId = intval($_POST['post_id'] ?? 0);
        $result = $this->likeModel->toggleLike($userId, $postId);
        echo json_encode($result);
        exit;
    }

    //  SEARCH
    
}