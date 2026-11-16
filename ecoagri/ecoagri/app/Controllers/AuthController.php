<?php
class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::user()) { redirect('/dashboard'); }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::csrf();
            if (($_SESSION['login_blocked_until'] ?? 0) > time()) {
                $error = 'Trop de tentatives. Réessayez dans quelques minutes.';
            } else {
                try {
                    $user = User::byEmail(trim((string) ($_POST['email'] ?? '')));
                    if ($user && password_verify((string) ($_POST['password'] ?? ''), $user['mot_de_passe'])) {
                        session_regenerate_id(true);
                        unset($user['mot_de_passe'], $_SESSION['login_attempts'], $_SESSION['login_blocked_until']);
                        $_SESSION['user'] = $user;
                        redirect('/dashboard');
                    }
                    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                    if ($_SESSION['login_attempts'] >= 5) { $_SESSION['login_blocked_until'] = time() + 300; $_SESSION['login_attempts'] = 0; }
                    $error = 'Adresse e-mail ou mot de passe incorrect.';
                } catch (PDOException $exception) { $error = 'Connexion aux données indisponible. Vérifiez la configuration MySQL.'; }
            }
        }
        $this->render('auth/form', ['title'=>'Connexion', 'currentPage'=>'connexion', 'register'=>false, 'error'=>$error]);
    }
    public function register(): void
    {
        if (Auth::user()) { redirect('/dashboard'); }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::csrf();
            $name = trim((string) ($_POST['nom'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            if (strlen($name) < 2 || strlen($name)>100 || strlen($email)>150 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password)<8 || strlen($password)>72) {
                $error = 'Indiquez un nom valide, un e-mail et un mot de passe de 8 à 72 caractères.';
            } else {
                try {
                    User::register($name, $email, $password);
                    Session::flash('success', 'Compte créé. Vous pouvez maintenant vous connecter.');
                    redirect('/connexion');
                } catch (PDOException $exception) { $error = $exception->getCode()==='23000' ? 'Cette adresse e-mail est déjà utilisée.' : 'Inscription indisponible. Vérifiez la configuration MySQL.'; }
                catch (RuntimeException $exception) { $error = 'Importez le schéma de la base avant de créer un compte.'; }
            }
        }
        $this->render('auth/form', ['title'=>'Inscription', 'currentPage'=>'inscription', 'register'=>true, 'error'=>$error]);
    }
    public function logout(): void
    {
        Auth::csrf();
        unset($_SESSION['user']);
        session_regenerate_id(true);
        Session::flash('success', 'Vous êtes déconnecté.');
        redirect('/connexion');
    }
}
