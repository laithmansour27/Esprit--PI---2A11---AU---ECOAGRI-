
<div class="eyebrow">ADMINISTRATION</div>

<h1>Gestion des utilisateurs</h1>

<p class="intro">
    Consultez et gérez les comptes ECOAGRI.
</p>

<?php if ($error): ?>
    <p class="flash"><?= e($error) ?></p>
<?php endif; ?>

<section class="panel">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap">

        <h2 style="font-size:22px">
            Utilisateurs (<?= count($users) ?>)
        </h2>

        <a class="button" href="<?= e(url('/utilisateurs/ajouter')) ?>">
            + Ajouter un utilisateur
        </a>

    </div>

    <?php if (!$users): ?>

        <p class="muted">Aucun utilisateur enregistré.</p>

    <?php else: ?>

        <div class="table-scroll">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($users as $item): ?>

                        <tr>
                            <td><?= (int)$item['id_user'] ?></td>

                            <td><?= e($item['nom']) ?></td>

                            <td><?= e($item['email']) ?></td>

                            <td><?= e($item['telephone'] ?: '—') ?></td>

                            <td><?= e($item['nom_role']) ?></td>

                            <td>

                                <a class="button small"
                                   href="<?= e(url('/utilisateurs/modifier')) ?>&id=<?= (int)$item['id_user'] ?>">
                                    Modifier
                                </a>

                                <?php if ((int)$item['id_user'] !== (int)$user['id_user']): ?>

                                    <form method="post"
                                          action="<?= e(url('/utilisateurs/supprimer')) ?>"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?')"
                                          style="margin-top:6px">

                                        <input type="hidden"
                                               name="csrf"
                                               value="<?= e(Session::csrfToken()) ?>">

                                        <input type="hidden"
                                               name="id_user"
                                               value="<?= (int)$item['id_user'] ?>">

                                        <button class="button small" type="submit">
                                            Supprimer
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

        </div>

    <?php endif; ?>

</section>
