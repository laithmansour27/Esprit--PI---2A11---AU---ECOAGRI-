<?php
include '../controller/publicationC.php';

$search = trim($_GET['search'] ?? '');
$tri = $_GET['tri'] ?? 'recent';
$filtre = $_GET['filtre'] ?? 'tous';

$pc = new PublicationC();
$liste = $pc->listePublication($search, $tri, $filtre)->fetchAll();

// Pagination: 5 éléments par page
$parPage = 5;
$total = count($liste);
$nbPages = max(1, (int) ceil($total / $parPage));
$page = (int) ($_GET['page'] ?? 1);
$page = max(1, min($page, $nbPages));
$affiche = array_slice($liste, ($page - 1) * $parPage, $parPage);

$stats = $pc->statistiques();
$pageTitle = 'Conseils & alertes';
include 'header.php';
?>

<div class="top">
    <div>
        <div class="crumb">GESTION / CONSEILS</div>
        <h1>Conseils &amp; alertes</h1>
        <div class="sub">Bonnes pratiques et notifications</div>
    </div>
    <a class="btn no-print" href="Addpublication.php">+ Ajouter un élément</a>
</div>

<div class="stats">
    <div class="stat">
        <div class="stat-num"><?= $stats['total']; ?></div>
        <div class="stat-label">Total</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $stats['conseils']; ?></div>
        <div class="stat-label">Conseils</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $stats['alertes']; ?></div>
        <div class="stat-label">Alertes</div>
    </div>
    <div class="stat urgent">
        <div class="stat-num"><?= $stats['urgentes']; ?></div>
        <div class="stat-label">Urgentes</div>
    </div>
    <div class="stat">
        <div class="stat-num"><?= $stats['brouillons']; ?></div>
        <div class="stat-label">Brouillons</div>
    </div>
</div>

<div class="tabs no-print">
    <?php
    $onglets = [
        'tous'      => 'Tous',
        'conseil'   => 'Conseils',
        'alerte'    => 'Alertes',
        'urgent'    => 'Urgent',
        'brouillon' => 'Brouillons',
    ];
    foreach ($onglets as $cle => $label) {
        $lien = '?' . http_build_query(['filtre' => $cle, 'search' => $search, 'tri' => $tri]);
        $classe = ($filtre === $cle) ? 'tab active' : 'tab';
        echo '<a class="' . $classe . '" href="' . $lien . '">' . $label . '</a>';
    }
    ?>
</div>

<div class="card">
    <div class="toolbar">
        <h2>Liste des éléments <span class="count"><?= $total; ?></span></h2>
        <form class="filters no-print" method="GET">
            <input type="hidden" name="filtre" value="<?= htmlspecialchars($filtre); ?>">
            <input type="text" name="search" placeholder="Rechercher..."
                   value="<?= htmlspecialchars($search); ?>">
            <select name="tri" onchange="this.form.submit()">
                <option value="recent" <?= $tri === 'recent' ? 'selected' : ''; ?>>Plus récent</option>
                <option value="date"   <?= $tri === 'date'   ? 'selected' : ''; ?>>Date de publication</option>
                <option value="titre"  <?= $tri === 'titre'  ? 'selected' : ''; ?>>Titre</option>
                <option value="type"   <?= $tri === 'type'   ? 'selected' : ''; ?>>Catégorie</option>
                <option value="region" <?= $tri === 'region' ? 'selected' : ''; ?>>Région</option>
                <option value="niveau" <?= $tri === 'niveau' ? 'selected' : ''; ?>>Niveau</option>
                <option value="etat"   <?= $tri === 'etat'   ? 'selected' : ''; ?>>État</option>
            </select>
            <button type="button" class="btn-light" onclick="window.print()">⇩ Export PDF</button>
        </form>
    </div>

    <table>
        <tr>
            <th>#</th><th>PUBLICATION</th><th>CATÉGORIE</th><th>RÉGION</th>
            <th>NIVEAU</th><th>DATE</th><th>ÉTAT</th>
            <th class="no-print">ACTIONS</th>
        </tr>
        <?php foreach ($affiche as $p) { ?>
        <tr>
            <td><?= $p['id_publication']; ?></td>
            <td><?= htmlspecialchars($p['titre']); ?></td>
            <td><?= htmlspecialchars($p['type']); ?></td>
            <td><?= htmlspecialchars($p['region']); ?></td>
            <td><span class="badge <?= $p['niveau']; ?>"><?= htmlspecialchars($p['niveau']); ?></span></td>
            <td><?= date('d/m/Y', strtotime($p['date_publication'])); ?></td>
            <td>
                <span class="badge <?= $p['etat']; ?>">
                    <?= $p['etat'] === 'publie' ? 'Publié' : 'Brouillon'; ?>
                </span>
            </td>
            <td class="no-print">
                <a class="act edit" href="listereponse.php?id=<?= $p['id_publication']; ?>">💬</a>
                <a class="act edit" href="Modifierpublication.php?id=<?= $p['id_publication']; ?>">✎</a>
                <a class="act del" href="deletpublication.php?id=<?= $p['id_publication']; ?>"
                   onclick="return confirm('Voulez-vous vraiment supprimer ?')">🗑</a>
            </td>
        </tr>
        <?php } ?>
        <?php if ($total === 0) { ?>
        <tr><td colspan="8">Aucun résultat.</td></tr>
        <?php } ?>
    </table>

    <?php if ($nbPages > 1) { ?>
    <div class="pagination no-print">
        <?php if ($page > 1) { ?>
            <a href="?<?= http_build_query(['filtre' => $filtre, 'search' => $search, 'tri' => $tri, 'page' => $page - 1]); ?>">←</a>
        <?php } ?>

        <?php for ($i = 1; $i <= $nbPages; $i++) { ?>
            <a class="<?= $i === $page ? 'active' : ''; ?>"
               href="?<?= http_build_query(['filtre' => $filtre, 'search' => $search, 'tri' => $tri, 'page' => $i]); ?>"><?= $i; ?></a>
        <?php } ?>

        <?php if ($page < $nbPages) { ?>
            <a href="?<?= http_build_query(['filtre' => $filtre, 'search' => $search, 'tri' => $tri, 'page' => $page + 1]); ?>">→</a>
        <?php } ?>
    </div>
    <?php } ?>
</div>
<?php include 'footer.php'; ?>