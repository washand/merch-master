<?php
// Levertijden ophalen uit database — met fallback als DB niet beschikbaar is
function get_levertijden_site(): array {
    static $cached = null;
    if ($cached !== null) return $cached;
    
    try {
        require_once __DIR__ . '/../bestellen/includes/db.php';
        $cached = get_levertijden();
    } catch (Exception $e) {
        $cached = [
            'dtf'      => '5-8',
            'zeefdruk' => '6-10',
            'borduren' => '7-12',
        ];
    }
    return $cached;
}
?>
