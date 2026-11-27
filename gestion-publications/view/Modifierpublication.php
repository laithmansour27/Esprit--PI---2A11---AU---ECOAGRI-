<?php
include '../controller/publicationC.php';

$pc = new PublicationC();
$p = $pc->getPublication($_GET['id'] ?? 0);
if (!$p) { die('Publication introuvable'); }

$pageTitle = 'Modifier un élément';
include 'header.php';
?>
<div class="top">
    <div>
        <div class="crumb">GESTION / CONSEILS</div>
        <h1>Modifier un élément</h1>
    </div>
    <a class="btn-light" href="listepublication.php">← Retour</a>
</div>

<div class="card">
    <h2>Informations principales</h2>
    <form action="Updatepublication.php" method="POST" onsubmit="return validerPublication()">
        <input type="hidden" name="id" value="<?= $p['id_publication']; ?>">
        <div class="form-grid">
            <div>
                <label for="titre">Titre de publication</label>
                <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($p['titre']); ?>">
            </div>
            <div>
                <label for="type">Catégorie</label>
                <select id="type" name="type">
                    <option value="conseil" <?= $p['type'] === 'conseil' ? 'selected' : ''; ?>>Conseil</option>
                    <option value="alerte" <?= $p['type'] === 'alerte' ? 'selected' : ''; ?>>Alerte</option>
                </select>
            </div>
            <div>
                <label for="region">Région</label>
                <input type="text" id="region" name="region" value="<?= htmlspecialchars($p['region']); ?>">
            </div>
            <div>
                <label for="date_publication">Date de publication</label>
                <input type="date" id="date_publication" name="date_publication" value="<?= $p['date_publication']; ?>">
            </div>
            <div>
                <label for="niveau">Niveau</label>
                <select id="niveau" name="niveau">
                    <?php foreach (['info', 'moyen', 'urgent'] as $n) { ?>
                        <option value="<?= $n; ?>" <?= $p['niveau'] === $n ? 'selected' : ''; ?>><?= ucfirst($n); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label for="etat">État</label>
                <select id="etat" name="etat">
                    <option value="publie" <?= $p['etat'] === 'publie' ? 'selected' : ''; ?>>Publié</option>
                    <option value="brouillon" <?= $p['etat'] === 'brouillon' ? 'selected' : ''; ?>>Brouillon</option>
                </select>
            </div>
            <div class="full">
                <label for="contenu">Notes complémentaires</label>
                <textarea id="contenu" name="contenu"><?= htmlspecialchars($p['contenu']); ?></textarea>
            </div>
        </div>
        <div class="actions">
            <a class="btn-light" href="listepublication.php">Annuler</a>
            <input class="btn" type="submit" value="Mettre à jour">
        </div>
        <p id="erreur"><?= htmlspecialchars($_GET['erreur'] ?? ''); ?></p>
    </form>
</div>
<?php include 'footer.php'; ?>