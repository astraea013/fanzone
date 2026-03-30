<?php
require_once BASE_PATH . '/app/models/CommentModel.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new CommentModel();
    }

    // Add comment (AJAX)
    public function add() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userId  = $_SESSION['user_id'];
        $postId  = intval($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if (empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
            return;
        }

        if ($postId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post']);
            return;
        }

        $comment = $this->commentModel->addComment($userId, $postId, $content);

        if ($comment) {
            echo json_encode(['success' => true, 'comment' => $comment]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
    }

    // Get comments for a post (AJAX)
    // FIX: Added missing getComments() method — was causing silent failure
    public function getComments() {
        header('Content-Type: application/json');

        $postId = intval($_GET['post_id'] ?? 0);

        if ($postId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID', 'comments' => []]);
            exit;
        }

        $comments = $this->commentModel->getCommentsByPostId($postId);

        // Return consistent structure that posts.js expects
        echo json_encode([
            'success'  => true,
            'post_id'  => $postId,
            'comments' => $comments
        ]);
        exit;
    }

    // Edit comment (AJAX)
    public function edit() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userId    = $_SESSION['user_id'];
        $commentId = intval($_POST['comment_id'] ?? 0);
        $content   = trim($_POST['content'] ?? '');

        if (empty($content) || $commentId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        if (!$this->commentModel->isOwner($commentId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $result = $this->commentModel->updateComment($commentId, $content);
        echo json_encode(['success' => $result]);
    }

    // Delete comment (AJAX)
    public function delete() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userId    = $_SESSION['user_id'];
        $commentId = intval($_POST['comment_id'] ?? 0);

        if ($commentId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid comment']);
            return;
        }

        if (!$this->commentModel->isOwner($commentId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $result = $this->commentModel->deleteComment($commentId);
        echo json_encode(['success' => $result]);
    }
}