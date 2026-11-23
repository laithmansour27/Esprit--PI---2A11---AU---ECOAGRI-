<!DOCTYPE html><html lang="fr"><head><?php require __DIR__.'/partials/head.php'; ?></head><body class="dashboard-body"> 
    <?php
$modules = $modules ?? [];
$user = $user ?? [];
$currentPage = $currentPage ?? '';
?>
<a class="skip" href="#main">Aller au contenu</a><?php require __DIR__.'/partials/sidebar.php'; ?><div class="dashboard-shell"><?php require __DIR__.'/partials/header.php'; ?><main id="main" class="dashboard-main"><?php foreach ($flashes as $flash): ?><p class="flash" role="status"><?= e($flash['message']) ?></p><?php endforeach; ?><?= $content ?></main><?php require __DIR__.'/partials/footer.php'; ?></div><?php require __DIR__.'/partials/scripts.php'; ?></body></html>
