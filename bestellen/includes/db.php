<?php
require_once __DIR__ . '/config.php';

class DB {
    private static $pdo = null;

    public static function get(): PDO {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    public static function run($sql, $params = []) {
        $stmt = self::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch($sql, $params = []) {
        return self::run($sql, $params)->fetch() ?: null;
    }

    public static function fetchAll($sql, $params = []) {
        return self::run($sql, $params)->fetchAll();
    }

    // Aliassen voor leesbaarheid
    public static function row($sql, $params = []) {
        return self::fetch($sql, $params);
    }

    public static function rows($sql, $params = []) {
        return self::fetchAll($sql, $params);
    }

    public static function insert($sql, $params = []) {
        self::run($sql, $params);
        return (int) self::get()->lastInsertId();
    }
}

// ── Instellingen helpers ──────────────────────────────────────────────────────
function get_instelling(string $sleutel, string $default = ''): string {
    try {
        $r = DB::row('SELECT waarde FROM instellingen WHERE sleutel = ?', [$sleutel]);
        return $r ? $r['waarde'] : $default;
    } catch (Exception $e) { return $default; }
}

function set_instelling(string $sleutel, string $waarde): void {
    DB::run('INSERT INTO instellingen (sleutel, waarde) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE waarde = ?, updated_at = NOW()',
            [$sleutel, $waarde, $waarde]);
}

function get_levertijden(): array {
    return [
        'dtf'      => get_instelling('levertijd_dtf',      '5-8'),
        'zeefdruk' => get_instelling('levertijd_zeefdruk', '6-10'),
        'borduren' => get_instelling('levertijd_borduren', '7-12'),
    ];
}
