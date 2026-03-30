<?php
require_once BASE_PATH . '/app/models/UserModel.php';

class SearchController {
   
    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $users = [];
        
        if (!empty($query)) {
            $userModel = new UserModel();
            $users = $userModel->searchUsers($query);
        }
        
        // Load the search results view
        require_once BASE_PATH . '/app/views/search/results.php';
    }
}
?>