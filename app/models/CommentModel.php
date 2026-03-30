<?php
require_once BASE_PATH . '/config/database.php';

class CommentModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Add comment
    public function addComment($userId, $postId, $content) {
        $sql  = "INSERT INTO comments (post_id, user_id, content, created_at)
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $postId, $userId, $content);

        if (!$stmt->execute()) {
            return false;
        }

        $commentId = $stmt->insert_id;
        return $this->getCommentById($commentId);
    }

    // Get comment by ID
    public function getCommentById($commentId) {
        $sql  = "SELECT c.*, u.username, u.full_name, u.profile_image
                 FROM comments c
                 JOIN users u ON c.user_id = u.id
                 WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get all comments for a post
    public function getCommentsByPostId($postId) {
        $sql  = "SELECT c.*, u.username, u.full_name, u.profile_image
                 FROM comments c
                 JOIN users u ON c.user_id = u.id
                 WHERE c.post_id = ?
                 ORDER BY c.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update comment
    public function updateComment($commentId, $content) {
        $sql  = "UPDATE comments SET content = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $content, $commentId);
        return $stmt->execute();
    }

    // Delete comment
    public function deleteComment($commentId) {
        $sql  = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $commentId);
        return $stmt->execute();
    }

    // Delete all comments for a post
    public function deleteCommentsByPostId($postId) {
        $sql  = "DELETE FROM comments WHERE post_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $postId);
        return $stmt->execute();
    }

    // Check if user owns the comment
    public function isOwner($commentId, $userId) {
        $sql  = "SELECT id FROM comments WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Get comment count for a post
    public function getCommentCount($postId) {
        $sql  = "SELECT COUNT(*) as count FROM comments WHERE post_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $row  = $stmt->get_result()->fetch_assoc();
        return (int) $row['count'];
    }
}