
<aside class="sidebar">
    <a class="brand" href="<?= e(url('/')) ?>">
        <img
            src="<?= e(asset('images/logo.svg')) ?>"
            width="29"
            height="36"
            alt=""
        >
        ecoagri.
    </a>

    <div class="sidebar-label">MON ESPACE</div>

    <nav aria-label="Navigation de l’espace">

        <!-- Dashboard -->
        <a
            class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"
            href="<?= e(url('/dashboard')) ?>"
        >
            ▥ <span>Vue d’ensemble</span>
        </a>

        <!-- Modules -->
        <?php foreach ($modules as $slug => $definition): ?>
            <a
                class="<?= $currentPage === $slug ? 'active' : '' ?>"
                href="<?= e(
                    $slug === 'utilisateurs'
                        ? url('/utilisateurs')
                        : url('/module') . '&key=' . $slug
                ) ?>"
                <?= $currentPage === $slug ? 'aria-current="page"' : '' ?>
            >
                <span><?= e($definition['icon']) ?></span>
                <?= e($definition['name']) ?>
            </a>
        <?php endforeach; ?>

        <!-- Équipements et maintenances -->
        <?php if (($user['nom_role'] ?? '') === 'agriculteur'): ?>
            <a
                class="<?= $currentPage === 'equipements' ? 'active' : '' ?>"
                href="<?= e(url('/equipements')) ?>"
                <?= $currentPage === 'equipements' ? 'aria-current="page"' : '' ?>
            >
                <span>⚙</span>
                Équipements & maintenances
            </a>
        <?php endif; ?>

    </nav>

    <!-- Sidebar footer -->
    <div class="sidebar-bottom">

        <div class="sidebar-tip">
            ♧
            <strong>Chaque geste compte.</strong>
            <p>
                Un meilleur suivi pour des ressources
                mieux préservées.
            </p>
        </div>

        <a href="<?= e(url('/')) ?>">
            ← Retour au site
        </a>

        <form method="post" action="<?= e(url('/deconnexion')) ?>">
            <input
                type="hidden"
                name="csrf"
                value="<?= e(Session::csrfToken()) ?>"
            >

            <button class="logout" type="submit">
                Se déconnecter ↗
            </button>
        </form>

    </div>
</aside>
