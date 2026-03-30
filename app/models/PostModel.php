<?php
require_once BASE_PATH . '/config/database.php';

class PostModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get single post by ID
    public function getPostById($postId) {
        $sql  = "SELECT * FROM posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (getPostById): " . $this->db->error);
        }
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get posts for newsfeed
    public function getNewsfeedPosts($userId, $limit = 20) {
        $sql = "SELECT
                    p.*,
                    u.username,
                    u.full_name,
                    u.profile_image AS user_avatar,
                    (SELECT COUNT(*) FROM likes    WHERE post_id = p.id) AS like_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
                    (SELECT COUNT(*) FROM likes    WHERE post_id = p.id AND user_id = ?) AS user_liked
                FROM posts p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (getNewsfeedPosts): " . $this->db->error);
        }
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get posts by user
    public function getPostsByUserId($userId, $limit = 20) {
        $currentUserId = $_SESSION['user_id'];
        $sql = "SELECT
                    p.*,
                    u.username,
                    u.full_name,
                    u.profile_image AS user_avatar,
                    (SELECT COUNT(*) FROM likes    WHERE post_id = p.id) AS like_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
                    (SELECT COUNT(*) FROM likes    WHERE post_id = p.id AND user_id = ?) AS user_liked
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (getPostsByUserId): " . $this->db->error);
        }
        $stmt->bind_param("iii", $currentUserId, $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Create post - REMOVED: image parameter
    public function createPost($userId, $content, $fandomTag) {
        $sql  = "INSERT INTO posts (user_id, content, fandom_tag, created_at)
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (createPost): " . $this->db->error);
        }
        $stmt->bind_param("iss", $userId, $content, $fandomTag);
        return $stmt->execute();
    }

   
    public function updatePost($postId, $content) {
        $sql  = "UPDATE posts SET content = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (updatePost): " . $this->db->error);
        }
        $stmt->bind_param("si", $content, $postId);
        return $stmt->execute();
    }

    // Delete post
    public function deletePost($postId) {
        $sql  = "DELETE FROM posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (deletePost): " . $this->db->error);
        }
        $stmt->bind_param("i", $postId);
        return $stmt->execute();
    }

    // Check ownership
    public function isOwner($postId, $userId) {
        $sql  = "SELECT id FROM posts WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (isOwner): " . $this->db->error);
        }
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }


    // Get user fandoms
    public function getUserFandoms($userId) {
        $sql  = "SELECT fandoms FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Query error (getUserFandoms): " . $this->db->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row || empty($row['fandoms'])) return [];
        return explode(',', $row['fandoms']);
    }

    
}