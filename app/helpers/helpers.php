<?php
/**
 * FANZONE — Global Helper Functions
 * Loaded via index.php — available to all views
 */

// ── TIME AGO
// FIX: Uses DateTime with explicit timezone to avoid "Just now" always showing
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        if (empty($datetime)) return 'Unknown';

        try {
            // Create DateTime from DB value (MySQL stores in server timezone)
            $then = new DateTime($datetime);
            $now  = new DateTime('now');

            // Get difference in seconds
            $diff = $now->getTimestamp() - $then->getTimestamp();

            // Handle future/negative (clock skew)
            if ($diff < 0) return 'Just now';
            if ($diff < 60)      return 'Just now';
            if ($diff < 3600)    return floor($diff / 60) . 'm ago';
            if ($diff < 86400)   return floor($diff / 3600) . 'h ago';
            if ($diff < 604800)  return floor($diff / 86400) . 'd ago';
            if ($diff < 2592000) return floor($diff / 604800) . 'w ago';

            return $then->format('M j, Y');
        } catch (Exception $e) {
            return date('M j, Y', strtotime($datetime));
        }
    }
}

// ── FANDOM BADGE CSS CLASS
if (!function_exists('fandomBadgeClass')) {
    function fandomBadgeClass($fandom) {
        $key = strtolower(str_replace([' ', '-'], '', $fandom ?? ''));
        $map = [
            'anime'   => 'badge-anime',
            'games'   => 'badge-games',
            'movies'  => 'badge-movies',
            'manga'   => 'badge-manga',
            'kdrama'  => 'badge-kdrama',
            'comics'  => 'badge-comics',
            'novels'  => 'badge-anime',
            'cosplay' => 'badge-manga',
        ];
        return $map[$key] ?? 'badge-anime';
    }
}

// ── USER INITIALS
if (!function_exists('userInitials')) {
    function userInitials($username) {
        return strtoupper(substr($username ?? 'U', 0, 2));
    }
}

// ── SAFE HTML OUTPUT
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}