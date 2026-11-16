document.querySelector('.menu-toggle')?.addEventListener('click', function () {
  const expanded = this.getAttribute('aria-expanded') === 'true';
  this.setAttribute('aria-expanded', String(!expanded));
  document.getElementById('main-nav').classList.toggle('open', !expanded);
});
document.querySelector('[data-table-search]')?.addEventListener('input', function () {
  const query = this.value.trim().toLocaleLowerCase('fr');
  let visible = 0;
  document.querySelectorAll('tbody tr').forEach(row => {
    row.hidden = !row.textContent.toLocaleLowerCase('fr').includes(query);
    if (!row.hidden) visible++;
  });
  const empty = document.querySelector('[data-no-results]');
  if (empty) empty.hidden = visible > 0;
});
