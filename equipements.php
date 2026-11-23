<div class="eyebrow">VOTRE EXPLOITATION</div>
<h1>Équipements & maintenances</h1>
<p class="intro">Enregistrez votre matériel agricole et gardez un historique de ses opérations de maintenance.</p>
<section class="panel" style="margin-bottom:24px">
  <h2 style="font-size:22px">Ajouter un équipement</h2>
  <form method="post" action="<?= e(url('/equipements/creer')) ?>" class="crud-form">
    <input type="hidden" name="csrf" value="<?= e(Session::csrfToken()) ?>">
    <label>Nom *<input name="nom" maxlength="100" required placeholder="Ex. Tracteur John Deere"></label>
    <label>Type<input name="type" maxlength="60" placeholder="Tracteur, pompe, matériel d’irrigation…"></label>
    <label>Date d’achat<input type="date" name="date_achat"></label>
    <label>État<input name="etat" maxlength="40" placeholder="Bon état, en panne, en réparation…"></label>
    <button class="button" type="submit">Ajouter l’équipement</button>
  </form>
</section>
<section class="panel" style="margin-bottom:24px">
  <h2 style="font-size:22px">Mes équipements (<?= count($equipements) ?>)</h2>
  <?php if (!$equipements): ?><p class="muted">Aucun équipement enregistré. Ajoutez votre premier équipement ci-dessus.</p><?php else: ?>
  <div class="table-scroll"><table><thead><tr><th>Nom</th><th>Type</th><th>Date d’achat</th><th>État</th><th>Actions</th></tr></thead><tbody>
  <?php foreach ($equipements as $eq): ?><tr><td colspan="5">
    <form method="post" action="<?= e(url('/equipements/modifier')) ?>" class="crud-form inline-form">
      <input type="hidden" name="csrf" value="<?= e(Session::csrfToken()) ?>"><input type="hidden" name="id_equipement" value="<?= (int)$eq['id_equipement'] ?>">
      <label class="sr-only">Nom<input name="nom" maxlength="100" required value="<?= e($eq['nom']) ?>"></label>
      <label class="sr-only">Type<input name="type" maxlength="60" value="<?= e($eq['type']) ?>"></label>
      <label class="sr-only">Date d’achat<input type="date" name="date_achat" value="<?= e($eq['date_achat']) ?>"></label>
      <label class="sr-only">État<input name="etat" maxlength="40" value="<?= e($eq['etat']) ?>"></label>
      <button class="button small" type="submit">Enregistrer</button>
    </form>
    <form method="post" action="<?= e(url('/equipements/supprimer')) ?>" onsubmit="return confirm('Supprimer cet équipement et tout son historique de maintenance ?')" style="margin-top:8px">
      <input type="hidden" name="csrf" value="<?= e(Session::csrfToken()) ?>"><input type="hidden" name="id_equipement" value="<?= (int)$eq['id_equipement'] ?>"><button class="button small" type="submit">Supprimer</button>
    </form>
  </td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</section>
<section class="panel" style="margin-bottom:24px">
  <h2 style="font-size:22px">Enregistrer une maintenance</h2>
  <?php if (!$equipements): ?><p class="muted">Ajoutez d’abord un équipement.</p><?php else: ?>
  <form method="post" action="<?= e(url('/maintenances/creer')) ?>" class="crud-form">
    <input type="hidden" name="csrf" value="<?= e(Session::csrfToken()) ?>">
    <label>Équipement *<select name="id_equipement" required><?php foreach ($equipements as $eq): ?><option value="<?= (int)$eq['id_equipement'] ?>"><?= e($eq['nom']) ?></option><?php endforeach; ?></select></label>
    <label>Date *<input type="date" name="date_maintenance" required value="<?= e(date('Y-m-d')) ?>"></label>
    <label>Description<textarea name="description" maxlength="5000" rows="3" placeholder="Nature de l’intervention…"></textarea></label>
    <label>Coût (DT)<input type="number" name="cout" min="0" max="99999999" step="0.01"></label>
    <label>Technicien<input name="nom_technicien" maxlength="100"></label>
    <button class="button" type="submit">Enregistrer la maintenance</button>
  </form><?php endif; ?>
</section>
<section class="panel"><h2 style="font-size:22px">Historique des maintenances (<?= count($maintenances) ?>)</h2>
  <?php if (!$maintenances): ?><p class="muted">Aucune maintenance enregistrée.</p><?php else: ?><div class="table-scroll"><table><thead><tr><th>Équipement</th><th>Date</th><th>Description</th><th>Coût (DT)</th><th>Technicien</th><th>Action</th></tr></thead><tbody>
  <?php foreach ($maintenances as $m): ?><tr><td><?= e($m['equipement_nom']) ?></td><td><?= e($m['date_maintenance']) ?></td><td><?= e($m['description'] ?: '—') ?></td><td><?= e($m['cout'] ?? '—') ?></td><td><?= e($m['nom_technicien'] ?: '—') ?></td><td><form method="post" action="<?= e(url('/maintenances/supprimer')) ?>" onsubmit="return confirm('Supprimer cette maintenance ?')"><input type="hidden" name="csrf" value="<?= e(Session::csrfToken()) ?>"><input type="hidden" name="id_maintenance" value="<?= (int)$m['id_maintenance'] ?>"><button class="button small" type="submit">Supprimer</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</section>
<style>
.crud-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;align-items:end}.crud-form label{display:grid;gap:5px;font-size:12px;font-weight:600}.crud-form input,.crud-form select,.crud-form textarea{width:100%;padding:10px;border:1px solid #dce2d5;border-radius:6px;background:white;color:var(--dark);font:inherit;font-size:13px}.crud-form button{justify-self:start}.inline-form{grid-template-columns:repeat(4,minmax(110px,1fr)) auto}.inline-form label{font-weight:400}.sr-only{position:static}.data-panel{overflow:hidden}@media(max-width:700px){.inline-form{grid-template-columns:1fr 1fr}.inline-form button{grid-column:1/-1}}
</style>
