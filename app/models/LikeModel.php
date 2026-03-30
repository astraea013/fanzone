<?php
require_once BASE_PATH . '/config/database.php';

class LikeModel {
    private $db;

    public function __construct() {
  
        $this->db = Database::getInstance()->getConnection();
    }

    // Toggle like (like if not liked, unlike if already liked)
    public function toggleLike($userId, $postId) {
        $checkSql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt     = $this->db->prepare($checkSql);
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Unlike
            $sql  = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $userId, $postId);
            $stmt->execute();
            return ['liked' => false, 'message' => 'Unliked'];
        } else {
            // Like
            $sql  = "INSERT INTO likes (user_id, post_id, created_at) VALUES (?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $userId, $postId);
            $stmt->execute();
            return ['liked' => true, 'message' => 'Liked'];
        }
    }

    // Check if user already liked a post
    public function hasLiked($userId, $postId) {
        $sql  = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Get like count for a post
    public function getLikeCount($postId) {
        $sql  = "SELECT COUNT(*) as count FROM likes WHERE post_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $row  = $stmt->get_result()->fetch_assoc();
        return (int) $row['count'];
    }
}