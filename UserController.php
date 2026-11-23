
<?php

class UserController extends Controller
{
    private function admin(): array
    {
        $user = Auth::requireUser();

        if (($user['nom_role'] ?? '') !== 'administrateur') {
            http_response_code(403);
            exit('Accès réservé aux administrateurs.');
        }

        return $user;
    }

    public function index(): void
    {
        $user = $this->admin();

        try {
            $users = User::allForAdmin();
            $roles = User::allRoles();
            $error = null;
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $users = [];
            $roles = [];
            $error = 'Impossible de charger les utilisateurs.';
        }

        $modules = Module::available($user);

        $this->render(
            'users/index',
            compact('user', 'users', 'roles', 'modules', 'error')
                + [
                    'title' => 'Gestion des utilisateurs',
                    'currentPage' => 'utilisateurs'
                ],
            'back'
        );
    }

    public function create(): void
    {
        $user = $this->admin();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->form($user, null);
            return;
        }

        Auth::csrf();

        $name = trim((string)($_POST['nom'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $telephone = trim((string)($_POST['telephone'] ?? ''));
        $roleId = filter_var(
            $_POST['id_role'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            strlen($name) < 2 ||
            strlen($name) > 100 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 150 ||
            strlen($password) < 8 ||
            strlen($password) > 72 ||
            strlen($telephone) > 30 ||
            !$roleId
        ) {
            Session::flash('error', 'Vérifiez les informations saisies.');
            redirect('/utilisateurs/ajouter');
        }

        try {
            User::createByAdmin(
                $name,
                $email,
                $password,
                $telephone,
                (int)$roleId
            );

            Session::flash('success', 'Utilisateur ajouté avec succès.');
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            Session::flash('error', 'Impossible de créer cet utilisateur. Vérifiez notamment son adresse e-mail.');
        } catch (RuntimeException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        redirect('/utilisateurs');
    }

    public function edit(): void
    {
        $user = $this->admin();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            Session::flash('error', 'Identifiant invalide.');
            redirect('/utilisateurs');
        }

        $target = User::findById((int)$id);

        if (!$target) {
            Session::flash('error', 'Utilisateur introuvable.');
            redirect('/utilisateurs');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->form($user, $target);
            return;
        }

        Auth::csrf();

        $name = trim((string)($_POST['nom'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $telephone = trim((string)($_POST['telephone'] ?? ''));
        $roleId = filter_var(
            $_POST['id_role'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            strlen($name) < 2 ||
            strlen($name) > 100 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 150 ||
            strlen($password) > 72 ||
            ($password !== '' && strlen($password) < 8) ||
            strlen($telephone) > 30 ||
            !$roleId
        ) {
            Session::flash('error', 'Vérifiez les informations saisies.');
            header('Location: ' . url('/utilisateurs/modifier') . '&id=' . (int)$id);
            exit;
        }

        // Empêcher l'administrateur de retirer son propre rôle.
        if (
            (int)$id === (int)$user['id_user'] &&
            (int)$roleId !== (int)$user['id_role']
        ) {
            Session::flash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            redirect('/utilisateurs');
        }

        try {
            User::updateByAdmin(
                (int)$id,
                $name,
                $email,
                $telephone,
                (int)$roleId,
                $password
            );

            Session::flash('success', 'Utilisateur modifié avec succès.');
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            Session::flash('error', 'Modification impossible. Vérifiez notamment que cet e-mail n’est pas déjà utilisé.');
        } catch (RuntimeException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        redirect('/utilisateurs');
    }

    public function delete(): void
    {
        $user = $this->admin();
        Auth::csrf();

        $id = filter_var(
            $_POST['id_user'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            Session::flash('error', 'Identifiant invalide.');
            redirect('/utilisateurs');
        }

        if ((int)$id === (int)$user['id_user']) {
            Session::flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('/utilisateurs');
        }

        try {
            User::deleteByAdmin((int)$id);
            Session::flash('success', 'Utilisateur supprimé.');
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            Session::flash('error', 'Suppression impossible : cet utilisateur possède peut-être des données associées.');
        } catch (RuntimeException $exception) {
            Session::flash('error', $exception->getMessage());
        }

        redirect('/utilisateurs');
    }

    private function form(array $user, ?array $target): void
    {
        $roles = User::allRoles();
        $modules = Module::available($user);

        $this->render(
            'users/form',
            compact('user', 'target', 'roles', 'modules')
                + [
                    'title' => $target ? 'Modifier un utilisateur' : 'Ajouter un utilisateur',
                    'currentPage' => 'utilisateurs'
                ],
            'back'
        );
    }
}
