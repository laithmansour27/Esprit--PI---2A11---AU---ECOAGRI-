
<?php
$isEdit = $target !== null;
$action = $isEdit
    ? url('/utilisateurs/modifier') . '&id=' . (int)$target['id_user']
    : url('/utilisateurs/ajouter');
?>

<div class="eyebrow">ADMINISTRATION</div>

<h1><?= $isEdit ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?></h1>

<p class="intro">
    <?= $isEdit
        ? 'Modifiez les informations du compte.'
        : 'Créez un nouveau compte ECOAGRI.' ?>
</p>

<section class="panel">

    <form method="post" action="<?= e($action) ?>" class="crud-form">

        <input type="hidden"
               name="csrf"
               value="<?= e(Session::csrfToken()) ?>">

        <label>
            Nom complet *
            <input
                type="text"
                name="nom"
                maxlength="100"
                required
                value="<?= e($target['nom'] ?? '') ?>"
            >
        </label>

        <label>
            Adresse e-mail *
            <input
                type="email"
                name="email"
                maxlength="150"
                required
                value="<?= e($target['email'] ?? '') ?>"
            >
        </label>

        <label>
            Téléphone
            <input
                type="text"
                name="telephone"
                maxlength="30"
                value="<?= e($target['telephone'] ?? '') ?>"
            >
        </label>

        <label>
            Mot de passe
            <input
                type="password"
                name="password"
                minlength="8"
                maxlength="72"
                <?= $isEdit ? '' : 'required' ?>
                autocomplete="new-password"
                placeholder="<?= $isEdit ? 'Laisser vide pour conserver' : '8 caractères minimum' ?>"
            >
        </label>

        <label>
            Rôle *
            <select name="id_role" required>

                <?php foreach ($roles as $role): ?>

                    <option
                        value="<?= (int)$role['id_role'] ?>"
                        <?= (int)($target['id_role'] ?? 1) === (int)$role['id_role'] ? 'selected' : '' ?>
                    >
                        <?= e(ucfirst($role['nom_role'])) ?>
                    </option>

                <?php endforeach; ?>

            </select>
        </label>

        <div style="display:flex;gap:10px;flex-wrap:wrap">

            <button class="button" type="submit">
                <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le compte' ?>
            </button>

            <a class="button small" href="<?= e(url('/utilisateurs')) ?>">
                Annuler
            </a>

        </div>

    </form>

</section>

<style>
.crud-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
}
.crud-form label {
    display: grid;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
}
.crud-form input,
.crud-form select {
    width: 100%;
    padding: 11px;
    border: 1px solid #dce2d5;
    border-radius: 6px;
    font: inherit;
}
</style>
