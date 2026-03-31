<?php
$TALEN = array('nl','en','de','no');

// Taal instellen via GET param
if (isset($_GET['lang']) && in_array($_GET['lang'], $TALEN)) {
    setcookie('mm_taal', $_GET['lang'], time()+60*60*24*365, '/');
    $_COOKIE['mm_taal'] = $_GET['lang'];
    $uri = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
    header('Location: ' . $uri);
    exit;
}

// Lees taal uit cookie, default NL
$TAAL = (isset($_COOKIE['mm_taal']) && in_array($_COOKIE['mm_taal'], $TALEN))
    ? $_COOKIE['mm_taal'] : 'nl';

// Laad vertalingen uit JSON bestand
$_json_file = __DIR__ . '/vertalingen.json';
$T = array();
if (file_exists($_json_file)) {
    $T = json_decode(file_get_contents($_json_file), true);
    if (!is_array($T)) $T = array();
}

// Vertaalfunctie — werkt op PHP 5.4+
function t($key) {
    global $T, $TAAL;
    if (isset($T[$key][$TAAL])) return $T[$key][$TAAL];
    if (isset($T[$key]['nl']))   return $T[$key]['nl'];
    return $key;
}

$VLAG     = array('nl'=>'&#127475;&#127473;','en'=>'&#127468;&#127463;','de'=>'&#127465;&#127466;','no'=>'&#127475;&#127476;');
$TAAL_LBL = array('nl'=>'NL','en'=>'EN','de'=>'DE','no'=>'NO');

// t_lt() — vertaling met levertijd placeholders (veilige fallback als DB niet beschikbaar)
function t_lt($key) {
    $txt = t($key);
    static $_lt = null;
    if ($_lt === null) {
        $_lt = array('dtf'=>'5-8','zeefdruk'=>'6-10','borduren'=>'7-12');
        try {
            $db = __DIR__ . '/../bestellen/includes/db.php';
            if (file_exists($db)) {
                require_once $db;
                if (function_exists('get_levertijden')) {
                    $_lt = get_levertijden();
                }
            }
        } catch (Exception $e) {}
    }
    $txt = str_replace('{lt_dtf}',   $_lt['dtf'],      $txt);
    $txt = str_replace('{lt_zeef}',  $_lt['zeefdruk'], $txt);
    $txt = str_replace('{lt_bord}',  $_lt['borduren'], $txt);
    return $txt;
}
?>
