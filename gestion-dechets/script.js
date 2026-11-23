(function () {
  // ----- Stockage local -----
  const STORAGE_KEY = 'dechets_agricoles_v1';
  const CUSTOM_TYPES_KEY = 'dechets_agricoles_custom_types_v1';

  // Données par défaut (exemples pour initialiser si vide)
  const defaultWaste = [
    { id: '1', type: 'Déchets verts', quantite: 250, date: '2025-03-10', statut: 'Tri' },
    { id: '2', type: 'Résidus de culture', quantite: 180, date: '2025-03-12', statut: 'Recyclage' },
    { id: '3', type: 'Fumier / lisier', quantite: 520, date: '2025-03-08', statut: 'Valorisation' },
    { id: '4', type: 'Déchets plastiques', quantite: 45, date: '2025-03-14', statut: 'En attente' },
    { id: '5', type: 'Emballages', quantite: 32, date: '2025-03-15', statut: 'Tri' }
  ];

  let wasteList = [];
  let customTypes = [];

  function loadFromStorage() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) {
      try {
        wasteList = JSON.parse(stored);
      } catch (e) {
        wasteList = [...defaultWaste];
      }
    } else {
      wasteList = [...defaultWaste];
      saveToStorage();
    }
  }

  function saveToStorage() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(wasteList));
  }

  // ----- Éléments du DOM -----
  const tableBody = document.getElementById('tableBody');
  const emptyState = document.getElementById('emptyState');
  const form = document.getElementById('wasteForm');
  const typeSelect = document.getElementById('type');
  const customTypeField = document.getElementById('customTypeField');
  const customTypeInput = document.getElementById('customTypeInput');
  const quantiteInput = document.getElementById('quantite');
  const dateInput = document.getElementById('date');
  const statutSelect = document.getElementById('statut');

  // ----- Utilitaires -----
  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, character => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[character]);
  }

  function addCustomTypeOption(type) {
    const alreadyExists = Array.from(typeSelect.options).some(option =>
      option.value.toLowerCase() === type.toLowerCase()
    );
    if (alreadyExists) return;

    const option = document.createElement('option');
    option.value = type;
    option.textContent = type;
    typeSelect.insertBefore(option, typeSelect.querySelector('option[value="Autre"]'));
  }

  function loadCustomTypes() {
    const stored = localStorage.getItem(CUSTOM_TYPES_KEY);
    if (!stored) return;

    try {
      const parsed = JSON.parse(stored);
      if (!Array.isArray(parsed) || parsed.some(type => typeof type !== 'string')) {
        throw new TypeError('Expected an array of custom waste type names.');
      }

      const seen = new Set();
      customTypes = parsed
        .map(type => type.trim())
        .filter(type => {
          const normalized = type.toLowerCase();
          if (!normalized || seen.has(normalized)) return false;
          seen.add(normalized);
          return true;
        });
    } catch (error) {
      console.error('Impossible de charger les types de déchets personnalisés.', error);
      customTypes = [];
    }

    customTypes.forEach(addCustomTypeOption);
  }

  function rememberCustomType(type) {
    const existingOption = Array.from(typeSelect.options).find(option =>
      option.value !== 'Autre' && option.value.toLowerCase() === type.toLowerCase()
    );
    if (existingOption) return existingOption.value;

    customTypes.push(type);
    localStorage.setItem(CUSTOM_TYPES_KEY, JSON.stringify(customTypes));
    addCustomTypeOption(type);
    return type;
  }

  function updateCustomTypeField() {
    const isOther = typeSelect.value === 'Autre';
    customTypeInput.required = isOther;
    customTypeInput.disabled = !isOther;
    customTypeField.setAttribute('aria-hidden', String(!isOther));

    if (isOther) {
      const wasHidden = customTypeField.hidden;
      customTypeField.hidden = false;
      if (wasHidden) {
        requestAnimationFrame(() => {
          if (typeSelect.value === 'Autre') {
            customTypeField.classList.add('is-visible');
          } else {
            customTypeField.hidden = true;
          }
        });
      } else {
        customTypeField.classList.add('is-visible');
      }
      return;
    }

    customTypeInput.value = '';
    const wasVisible = customTypeField.classList.contains('is-visible');
    customTypeField.classList.remove('is-visible');
    if (!wasVisible || matchMedia('(prefers-reduced-motion: reduce)').matches) {
      customTypeField.hidden = true;
    }
  }

  function getBadgeClass(statut) {
    switch (statut) {
      case 'Tri': return 'badge-tri';
      case 'Recyclage': return 'badge-recyclage';
      case 'Valorisation': return 'badge-valorisation';
      default: return 'badge-attente';
    }
  }

  function getBadgeIcon(statut) {
    switch (statut) {
      case 'Tri': return 'fa-filter';
      case 'Recyclage': return 'fa-recycle';
      case 'Valorisation': return 'fa-fire';
      default: return 'fa-clock';
    }
  }

  function formatNumber(n) {
    return n.toFixed(1).replace('.0', '');
  }

  function updateStats() {
    const total = wasteList.length;
    const totalKg = wasteList.reduce((sum, w) => sum + (Number(w.quantite) || 0), 0);
    const tri = wasteList.filter(w => w.statut === 'Tri').length;
    const recyclage = wasteList.filter(w => w.statut === 'Recyclage').length;
    const valorisation = wasteList.filter(w => w.statut === 'Valorisation').length;

    document.getElementById('totalCount').textContent = total;
    document.getElementById('totalWeight').textContent = formatNumber(totalKg);
    document.getElementById('triCount').textContent = tri;
    document.getElementById('recyclageCount').textContent = recyclage;
    document.getElementById('valorisationCount').textContent = valorisation;
  }

  function renderTable() {
    if (wasteList.length === 0) {
      tableBody.innerHTML = '';
      emptyState.style.display = 'block';
    } else {
      emptyState.style.display = 'none';
      let html = '';
      wasteList.forEach(item => {
        const badgeClass = getBadgeClass(item.statut);
        const badgeIcon = getBadgeIcon(item.statut);
        html += `
          <tr>
            <td><i class="fas fa-leaf" style="color:#4c8b4c; margin-right:8px;"></i>${escapeHtml(item.type)}</td>
            <td><strong>${formatNumber(item.quantite)}</strong> kg</td>
            <td>${item.date}</td>
            <td>
              <span class="badge ${badgeClass}">
                <i class="fas ${badgeIcon}"></i> ${item.statut}
              </span>
            </td>
            <td>
              <div class="actions">
                <button class="btn-icon edit" data-id="${item.id}" title="Changer le statut">
                  <i class="fas fa-pen"></i>
                </button>
                <button class="btn-icon delete" data-id="${item.id}" title="Supprimer">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      tableBody.innerHTML = html;
    }
    updateStats();
  }

  // ----- Actions -----
  function cycleStatut(id) {
    const item = wasteList.find(w => w.id === id);
    if (!item) return;
    const statuts = ['En attente', 'Tri', 'Recyclage', 'Valorisation'];
    const currentIndex = statuts.indexOf(item.statut);
    const nextIndex = (currentIndex + 1) % statuts.length;
    item.statut = statuts[nextIndex];
    saveToStorage();
    renderTable();
  }

  function deleteWaste(id) {
    if (confirm('Confirmer la suppression de cet enregistrement ?')) {
      wasteList = wasteList.filter(w => w.id !== id);
      saveToStorage();
      renderTable();
    }
  }

  // ----- Délégation d'événements -----
  tableBody.addEventListener('click', (e) => {
    const editBtn = e.target.closest('.btn-icon.edit');
    const deleteBtn = e.target.closest('.btn-icon.delete');

    if (editBtn) {
      cycleStatut(editBtn.getAttribute('data-id'));
    } else if (deleteBtn) {
      deleteWaste(deleteBtn.getAttribute('data-id'));
    }
  });

  // ----- Formulaire -----
  function setDefaultDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    dateInput.value = `${year}-${month}-${day}`;
  }

  customTypeField.addEventListener('transitionend', (event) => {
    if (
      event.target === customTypeField &&
      event.propertyName === 'grid-template-rows' &&
      !customTypeField.classList.contains('is-visible')
    ) {
      customTypeField.hidden = true;
    }
  });

  typeSelect.addEventListener('change', updateCustomTypeField);

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const selectedType = typeSelect.value;
    let type = selectedType;
    const quantite = parseFloat(quantiteInput.value);
    const date = dateInput.value;
    const statut = statutSelect.value;

    if (!type) {
      alert('Veuillez sélectionner un type de déchet.');
      return;
    }
    if (selectedType === 'Autre') {
      type = customTypeInput.value.trim();
      if (!type || type.toLowerCase() === 'autre') {
        alert('Veuillez saisir un nom précis pour ce déchet.');
        return;
      }
    }
    if (isNaN(quantite) || quantite <= 0) {
      alert('Veuillez saisir une quantité valide (supérieure à 0).');
      return;
    }
    if (!date) {
      alert('Veuillez indiquer une date.');
      return;
    }

    if (selectedType === 'Autre') {
      type = rememberCustomType(type);
    }

    const newWaste = {
      id: Date.now().toString() + Math.random().toString(36).substring(2, 6),
      type: type,
      quantite: quantite,
      date: date,
      statut: statut
    };

    wasteList.push(newWaste);
    saveToStorage();
    renderTable();

    // Réinitialisation
    typeSelect.value = '';
    customTypeInput.value = '';
    updateCustomTypeField();
    quantiteInput.value = '';
    statutSelect.value = '';
    setDefaultDate();
    typeSelect.focus();
  });

  // ----- Initialisation -----
  loadFromStorage();
  loadCustomTypes();
  updateCustomTypeField();
  renderTable();
  if (!dateInput.value) setDefaultDate();
})();