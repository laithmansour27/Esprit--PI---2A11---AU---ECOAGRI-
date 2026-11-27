<?php
$pageTitle = 'Ajouter un élément';
include 'header.php';
?>
<div class="top">
    <div>
        <div class="crumb">GESTION / CONSEILS</div>
        <h1>Ajouter un élément</h1>
    </div>
    <a class="btn-light" href="listepublication.php">← Retour</a>
</div>

<div class="card">
    <h2>Informations principales</h2>
    <form action="Ajoutpublication.php" method="POST" onsubmit="return validerPublication()">
        <div class="form-grid">
            <div>
                <label for="titre">Titre de publication</label>
                <input type="text" id="titre" name="titre" placeholder="Ex. Économiser l'eau">
            </div>
            <div>
                <label for="type">Catégorie</label>
                <select id="type" name="type">
                    <option value="conseil">Conseil</option>
                    <option value="alerte">Alerte</option>
                </select>
            </div>
            <div>
                <label for="region">Région</label>
                <input type="text" id="region" name="region" placeholder="Ex. Nord">
            </div>
            <div>
                <label for="date_publication">Date de publication</label>
                <input type="date" id="date_publication" name="date_publication" value="<?= date('Y-m-d'); ?>">
            </div>
            <div>
                <label for="niveau">Niveau</label>
                <select id="niveau" name="niveau">
                    <option value="info">Info</option>
                    <option value="moyen">Moyen</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label for="etat">État</label>
                <select id="etat" name="etat">
                    <option value="publie">Publié</option>
                    <option value="brouillon">Brouillon</option>
                </select>
            </div>
            <div class="full">
                <label for="contenu">Notes complémentaires</label>
                <textarea id="contenu" name="contenu" placeholder="Quelques détails sur cet élément..."></textarea>
            </div>
        </div>
        <div class="actions">
            <a class="btn-light" href="listepublication.php">Annuler</a>
            <input class="btn" type="submit" value="Enregistrer">
        </div>
        <p id="erreur"><?= htmlspecialchars($_GET['erreur'] ?? ''); ?></p>
    </form>
</div>
<?php include 'footer.php'; ?>