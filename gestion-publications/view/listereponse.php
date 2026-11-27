<?php
include '../controller/publicationC.php';
include '../controller/reponseC.php';

$idp = (int) ($_GET['id'] ?? 0);

$pc = new PublicationC();
$p = $pc->getPublication($idp);
if (!$p) { die('Publication introuvable'); }

$rc = new ReponseC();
$reponses = $rc->listeReponse($idp);

$pageTitle = 'Réponses';
include 'header.php';
?>
<div class="top">
    <div>
        <div class="crumb">GESTION / CONSEILS / RÉPONSES</div>
        <h1><?= htmlspecialchars($p['titre']); ?></h1>
        <div class="sub">
            <?= htmlspecialchars($p['type']); ?> · <?= htmlspecialchars($p['region']); ?> ·
            <span class="badge <?= $p['niveau']; ?>"><?= htmlspecialchars($p['niveau']); ?></span>
        </div>
    </div>
    <a class="btn-light" href="listepublication.php">← Retour</a>
</div>

<div class="card" style="margin-bottom:24px">
    <p><?= nl2br(htmlspecialchars($p['contenu'])); ?></p>
</div>

<div class="card" style="margin-bottom:24px">
    <h2>Ajouter une réponse</h2>
    <form action="Ajoutreponse.php" method="POST" onsubmit="return validerReponse()">
        <input type="hidden" name="id_publication" value="<?= $p['id_publication']; ?>">
        <textarea id="contenu" name="contenu" placeholder="Votre réponse..."></textarea>
        <div class="actions">
            <input class="btn" type="submit" value="Répondre">
        </div>
        <p id="erreur"></p>
    </form>
</div>

<div class="card">
    <h2>Réponses <span class="count"><?= count($reponses); ?></span></h2>
    <table>
        <tr>
            <th>#</th><th>RÉPONSE</th><th>DATE</th><th>ACTIONS</th>
        </tr>
        <?php foreach ($reponses as $r) { ?>
        <tr>
            <td><?= $r['id_reponse']; ?></td>
            <td><?= nl2br(htmlspecialchars($r['contenu'])); ?></td>
            <td><?= $r['date_reponse']; ?></td>
            <td>
                <a class="act del"
                   href="deletreponse.php?id=<?= $r['id_reponse']; ?>&idp=<?= $p['id_publication']; ?>"
                   onclick="return confirm('Supprimer cette réponse ?')">🗑</a>
            </td>
        </tr>
        <?php } ?>
        <?php if (count($reponses) === 0) { ?>
        <tr><td colspan="4">Aucune réponse pour le moment.</td></tr>
        <?php } ?>
    </table>
</div>
<?php include 'footer.php'; ?>