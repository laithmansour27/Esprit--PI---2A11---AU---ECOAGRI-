<?php
class Auth
{
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function requireUser(): array
    {
        $user = self::user();
        if (!$user) { Session::flash('error', 'Connectez-vous pour accéder à votre espace.'); redirect('/connexion'); }
        return $user;
    }
    public static function csrf(): void
    {
        $token = $_POST['csrf'] ?? null;
        if (!Session::checkCsrf(is_string($token) ? $token : null)) {
            http_response_code(419);
            exit('Votre session a expiré. Rechargez la page puis réessayez.');
        }
    }
}
