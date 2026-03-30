<?php
require_once BASE_PATH . '/config/database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Login user
    public function login($username, $password) {
        $sql  = "SELECT id, username, password, full_name, profile_image, fandoms
                 FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) die("Query error (login): " . $this->db->error);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        unset($user['password']);
        return ['success' => true, 'user' => $user];
    }

    // Register new user
    public function register($username, $password, $fullName, $fandoms = []) {
        // Check if username exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) die("Query error (register check): " . $this->db->error);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Username already taken'];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $fandomsString  = is_array($fandoms) ? implode(',', $fandoms) : $fandoms;

        $sql  = "INSERT INTO users (username, password, full_name, fandoms) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) die("Query error (register insert): " . $this->db->error);
        $stmt->bind_param("ssss", $username, $hashedPassword, $fullName, $fandomsString);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Registration failed'];
        }

        return ['success' => true, 'user_id' => $stmt->insert_id];
    }

    // Get user by ID
    public function getUserById($userId) {
        $sql  = "SELECT id, username, full_name, bio, profile_image, fandoms, created_at
                 FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) die("Query error (getUserById): " . $this->db->error);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get user stats
    public function getUserStats($userId) {
        $stats = ['posts' => 0];
        $stmt  = $this->db->prepare("SELECT COUNT(*) as count FROM posts WHERE user_id = ?");
        if (!$stmt) die("Query error (getUserStats): " . $this->db->error);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stats['posts'] = $stmt->get_result()->fetch_assoc()['count'];
        return $stats;
    }

    // Get user fandoms — reads from users.fandoms column (comma-separated)
    public function getUserFandoms($userId) {
        $stmt = $this->db->prepare("SELECT fandoms FROM users WHERE id = ?");
        if (!$stmt) die("Query error (getUserFandoms): " . $this->db->error);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row || empty($row['fandoms'])) return [];
        return explode(',', $row['fandoms']);
    }

    // Update profile
    public function updateProfile($userId, $fullName, $bio, $fandoms, $profileImage = null) {
        $fandomsString = is_array($fandoms) ? implode(',', $fandoms) : $fandoms;

        if ($profileImage) {
            $sql  = "UPDATE users SET full_name=?, bio=?, profile_image=?, fandoms=? WHERE id=?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) die("Query error (updateProfile): " . $this->db->error);
            $stmt->bind_param("ssssi", $fullName, $bio, $profileImage, $fandomsString, $userId);
        } else {
            $sql  = "UPDATE users SET full_name=?, bio=?, fandoms=? WHERE id=?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) die("Query error (updateProfile): " . $this->db->error);
            $stmt->bind_param("sssi", $fullName, $bio, $fandomsString, $userId);
        }

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
        return ['success' => true];
    }

    // Search users
       // Search users by username or full name
    public function searchUsers($query) {
        $searchTerm = "%{$query}%";
        
        $sql = "SELECT id, username, full_name, bio, profile_image, fandoms, created_at
                FROM users 
                WHERE username LIKE ? OR full_name LIKE ?
                ORDER BY 
                    CASE 
                        WHEN username = ? THEN 1
                        WHEN full_name = ? THEN 2
                        ELSE 3
                    END,
                    username ASC
                LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) die("Query error (searchUsers): " . $this->db->error);
        
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $query, $query);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }
}