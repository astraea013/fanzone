<?php
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/PostModel.php';

class ProfileController {
    private $userModel;
    private $postModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
    }

    // View profile
    public function view() {
        AuthController::requireLogin();
        $userId = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
        $user   = $this->userModel->getUserById($userId);

        if (!$user) {
            header('Location: index.php?action=newsfeed');
            exit;
        }

        $posts        = $this->postModel->getPostsByUserId($userId);
        $stats        = $this->userModel->getUserStats($userId);
        $fandoms      = $this->userModel->getUserFandoms($userId);
        $isOwnProfile = ($userId == $_SESSION['user_id']);

        require_once BASE_PATH . '/app/views/profile/view.php';
    }

    // Edit profile
    public function edit() {
        AuthController::requireLogin();
        $userId  = $_SESSION['user_id'];
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $bio      = trim($_POST['bio']       ?? '');
            $fandoms  = $_POST['fandoms']         ?? [];

            $profileImage = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $profileImage = $this->uploadProfileImage($_FILES['profile_image']);
            }

            $result = $this->userModel->updateProfile($userId, $fullName, $bio, $fandoms, $profileImage);

            if ($result['success']) {
                $_SESSION['full_name'] = $fullName;
                if ($profileImage) {
                    $_SESSION['profile_image'] = $profileImage;
                }
                $success = 'Profile updated successfully!';
            } else {
                $error = $result['message'];
            }
        }

        $user           = $this->userModel->getUserById($userId);
        $currentFandoms = $this->userModel->getUserFandoms($userId);

        require_once BASE_PATH . '/app/views/profile/edit.php';
    }

    // Upload profile image helper
    private function uploadProfileImage($file) {
        $uploadDir = BASE_PATH . '/public/assets/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) return null;

        $filename   = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'assets/uploads/profiles/' . $filename;
        }

  

        return null;
    }
}