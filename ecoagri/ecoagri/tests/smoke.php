<?php
// Run with: php -n tests/smoke.php
// Each request runs in isolation; no application or database records are created.
if (isset($argv[1])) {
    $case = $argv[1];
    $_SERVER['SCRIPT_NAME'] = '/ecoagri/public/index.php';
    $_SERVER['REQUEST_URI'] = '/ecoagri/public/index.php';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['HTTPS'] = '';
    session_save_path(sys_get_temp_dir());
    require dirname(__DIR__).'/app/bootstrap.php';
    if (str_starts_with($case, 'role:')) {
        [, $role, $key] = explode(':', $case);
        $_SESSION['user'] = ['id_user'=>999999, 'nom'=>'Test <script>', 'nom_role'=>$role];
        $_GET['route'] = $key==='dashboard' ? '/dashboard' : '/module';
        $_GET['key'] = $key;
    } else { $_GET['route'] = $case; }
    ob_start();
    (new Router(require dirname(__DIR__).'/app/routes.php'))->dispatch();
    $html = ob_get_clean();
    if (str_contains($html, 'Warning:') || str_contains($html, 'Fatal error:')) { throw new RuntimeException('PHP error in '.$case); }
    if (substr_count($html, '<!DOCTYPE html>')!==1) { throw new RuntimeException('Invalid layout in '.$case); }
    if (str_contains($html, 'Test <script>')) { throw new RuntimeException('Unescaped user name'); }
    $forbidden = in_array($case, ['role:agriculteur:utilisateurs','role:conseiller:marketplace','role:administrateur:dechets'], true);
    if ($forbidden && http_response_code()!==403) { throw new RuntimeException('Missing role restriction'); }
    if ($case==='/missing' && http_response_code()!==404) { throw new RuntimeException('Missing 404'); }
    if ($case==='/inscription' && !str_contains($html, 'name="csrf"')) { throw new RuntimeException('Missing CSRF field'); }
    unset($_SESSION['csrf']);
    if (Session::checkCsrf('') || Session::checkCsrf(null)) { throw new RuntimeException('Empty CSRF accepted'); }
    $token = Session::csrfToken();
    if (!Session::checkCsrf($token) || Session::checkCsrf('incorrect')) { throw new RuntimeException('CSRF validation failed'); }
    session_destroy();
    echo "PASS $case\n";
    exit;
}
$cases = ['/','/projet','/services','/connexion','/inscription','/missing',
 'role:agriculteur:dashboard','role:conseiller:dashboard','role:administrateur:dashboard',
 'role:agriculteur:parcelles','role:agriculteur:irrigation','role:agriculteur:marketplace',
 'role:agriculteur:dechets','role:agriculteur:conseils','role:administrateur:utilisateurs',
 'role:agriculteur:utilisateurs','role:conseiller:marketplace','role:administrateur:dechets'];
foreach ($cases as $case) {
    passthru(escapeshellarg(PHP_BINARY).' -n '.escapeshellarg(__FILE__).' '.escapeshellarg($case), $status);
    if ($status!==0) { exit($status); }
}
