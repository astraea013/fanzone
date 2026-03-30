<?php
require_once BASE_PATH . '/app/models/PostModel.php';
require_once BASE_PATH . '/app/models/LikeModel.php';
require_once BASE_PATH . '/app/models/CommentModel.php';

class PostController {
    private $postModel;
    private $likeModel;
    private $commentModel;

    public function __construct() {
        $this->postModel    = new PostModel();
        $this->likeModel    = new LikeModel();
        $this->commentModel = new CommentModel();
    }

    // Display newsfeed
    public function newsfeed() {
        AuthController::requireLogin();
        $userId      = $_SESSION['user_id'];
        $posts       = $this->postModel->getNewsfeedPosts($userId);
        $userFandoms = $this->postModel->getUserFandoms($userId);
        require_once BASE_PATH . '/app/views/posts/newsfeed.php';

    }

    // Show create post form / handle submission
    public function create() {
        AuthController::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleCreate();
        }
        require_once BASE_PATH . '/app/views/posts/create.php';
    }

    // Handle create post submission 
    private function handleCreate() {
        $userId    = $_SESSION['user_id'];
        $content   = trim($_POST['content']    ?? '');
        $fandomTag = $_POST['fandom_tag']       ?? 'Anime';

        if (empty($content)) {
            $error = 'Please write something before posting.';
            require_once BASE_PATH . '/app/views/posts/create.php';
            return;
        }

        
        $this->postModel->createPost($userId, $content, $fandomTag, null);
        
        header('Location: index.php?action=newsfeed');
        exit;
    }

    // Show edit post form / handle submission
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

    // Handle edit post submission 
    private function handleEdit($postId) {
        $content = trim($_POST['content'] ?? '');

        if (empty($content)) {
            $error = 'Post content cannot be empty.';
            $post  = $this->postModel->getPostById($postId);
            require_once BASE_PATH . '/app/views/posts/edit.php';
            return;
        }

        
        // Just update the content
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

    // Like/unlike post (AJAX)
    public function like() {
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $postId = intval($_POST['post_id'] ?? 0);
        $result = $this->likeModel->toggleLike($userId, $postId);
        echo json_encode($result);
        exit;
    }
// Add this method to PostController class:

// Add comment to a post (AJAX)
public function addComment() {
    header('Content-Type: application/json');
    
    $userId = $_SESSION['user_id'] ?? 0;
    $postId = intval($_POST['post_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }
    
    if ($postId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
        exit;
    }
    
    if (empty($content)) {
        echo json_encode(['success' => false, 'error' => 'Comment cannot be empty']);
        exit;
    }
    
    $commentId = $this->commentModel->addComment($postId, $userId, $content);
    $commentCount = $this->commentModel->getCommentCount($postId);
    
    echo json_encode([
        'success' => true,
        'comment_id' => $commentId,
        'comment_count' => $commentCount
    ]);
    exit;
}
    // Get comments for a post (AJAX)
    public function getComments() {
        header('Content-Type: application/json');
        
        $postId = intval($_GET['post_id'] ?? 0);
        
        if ($postId <= 0) {
            echo json_encode(['error' => 'Invalid post ID']);
            exit;
        }
        
        $comments = $this->commentModel->getCommentsByPostId($postId);
        
        echo json_encode([
            'success'  => true,
            'post_id'  => $postId,
            'comments' => $comments
        ]);
        exit;
    }

    // Search posts
    public function search() {
        AuthController::requireLogin();
        $query   = trim($_GET['q'] ?? '');
        $results = [];
        if (!empty($query)) {
            $results = $this->postModel->searchPosts($query);
        }
        require_once BASE_PATH . '/app/views/search/results.php';
    }

   
}