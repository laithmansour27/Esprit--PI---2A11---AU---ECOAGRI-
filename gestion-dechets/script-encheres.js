(function () {
  // ----- Clés de stockage -----
  const STORAGE_KEY = 'encheres_dechets_v1';
  const DURATION_MS = 60 * 60 * 1000; // 1 heure

  // ----- État -----
  let auctions = [];
  let tickInterval = null;

  // ----- Helpers -----
  function loadFromStorage() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) {
      try {
        auctions = JSON.parse(stored);
      } catch (e) {
        auctions = [];
      }
    } else {
      auctions = [];
    }
    // Marquer comme terminées les enchères expirées
    auctions.forEach(a => {
      if (a.endTime <= Date.now() && a.status !== 'ended') {
        a.status = 'ended';
      }
    });
    saveToStorage();
  }

  function saveToStorage() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(auctions));
  }

  function formatTime(ms) {
    if (ms <= 0) return '00:00:00';
    const totalSeconds = Math.floor(ms / 1000);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    return [hours, minutes, seconds]
      .map(v => String(v).padStart(2, '0'))
      .join(':');
  }

  function formatPrice(n) {
    return Number(n).toFixed(0);
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, character => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[character]);
  }

  function showToast(msg, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast' + (isError ? ' error' : '');
    toast.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${escapeHtml(msg)}`;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.transition = 'opacity 0.4s, transform 0.4s';
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(120%)';
      setTimeout(() => toast.remove(), 400);
    }, 2500);
  }

  // ----- Rendu -----
  const auctionListEl = document.getElementById('auctionList');
  const emptyEl = document.getElementById('emptyAuctions');
  const activeCountEl = document.getElementById('activeCount');

  function render() {
    const sorted = [...auctions].sort((a, b) => {
      if (a.status !== b.status) return a.status === 'active' ? -1 : 1;
      return a.endTime - b.endTime;
    });

    const activeCount = auctions.filter(a => a.status === 'active').length;
    activeCountEl.textContent = `${activeCount} active(s)`;

    if (sorted.length === 0) {
      auctionListEl.innerHTML = '';
      emptyEl.style.display = 'block';
      return;
    }
    emptyEl.style.display = 'none';

    let html = '';
    sorted.forEach(a => {
      const remaining = a.endTime - Date.now();
      const isEnded = a.status === 'ended' || remaining <= 0;
      const isUrgent = !isEnded && remaining < 5 * 60 * 1000;
      const hasBid = a.bids.length > 0;
      const lastBid = hasBid ? a.bids[a.bids.length - 1] : null;
      const currentPrice = hasBid ? lastBid.amount : a.startPrice;
      const bidHistory = hasBid
        ? `<section class="bid-history" aria-label="Historique des offres">
             <h3>Offres, du plus bas au plus haut</h3>
             <ol class="bid-history-list">
               ${[...a.bids]
                 .sort((first, second) => first.amount - second.amount)
                 .map(bid => `
                   <li class="bid-history-item">
                     <span>${escapeHtml(bid.bidder)}</span>
                     <strong>${formatPrice(bid.amount)} DT</strong>
                   </li>
                 `).join('')}
             </ol>
           </section>`
        : '';

      html += `
        <div class="auction-card ${isEnded ? 'ended' : ''} ${isEnded && hasBid ? 'winner' : ''}">
          <div class="card-header">
            <div class="card-title">
              <i class="fas fa-leaf"></i>${a.type}
            </div>
            <span class="card-qty">${a.quantity} kg</span>
          </div>

          <div class="timer ${isEnded ? 'ended' : (isUrgent ? 'urgent' : '')}">
            <i class="fas ${isEnded ? 'fa-flag-checkered' : 'fa-clock'}"></i>
            ${isEnded ? 'Enchère terminée' : `Temps restant : ${formatTime(remaining)}`}
          </div>

          <div class="current-price">
            <span class="label">${hasBid ? 'Meilleure offre' : 'Prix de départ'}</span>
            <span class="value">${formatPrice(currentPrice)} <small>DT</small></span>
          </div>

          ${hasBid
            ? `<div class="current-bidder"><i class="fas fa-user"></i> ${escapeHtml(lastBid.bidder)} <span style="color:#a0b8a0;">(${a.bids.length} offre${a.bids.length > 1 ? 's' : ''})</span></div>`
            : `<div class="current-bidder" style="color:#a0b8a0;"><i class="fas fa-hourglass-half"></i> Aucune offre pour le moment</div>`
          }

          ${bidHistory}

          ${isEnded && hasBid
            ? `<div class="winner-banner"><i class="fas fa-trophy"></i> Gagnant : <strong>${escapeHtml(lastBid.bidder)}</strong> — ${formatPrice(lastBid.amount)} DT</div>`
            : isEnded && !hasBid
              ? `<div class="no-winner-banner"><i class="fas fa-info-circle"></i> Aucune offre, enchère non attribuée</div>`
              : `<form class="bid-form" data-id="${a.id}">
                   <input type="number" min="${currentPrice + 1}" step="1" placeholder="Offre min ${currentPrice + 1} DT" required>
                   <input type="text" placeholder="Votre nom" required style="flex: 0 0 120px;">
                   <button type="submit"><i class="fas fa-gavel"></i> Enchérir</button>
                 </form>`
          }

          <div class="card-actions">
            <button class="btn-delete-auction" data-id="${a.id}" title="Supprimer l'enchère">
              <i class="fas fa-trash-alt"></i> Supprimer
            </button>
          </div>
        </div>
      `;
    });

    auctionListEl.innerHTML = html;

    // Attacher les écouteurs aux formulaires d'enchère
    auctionListEl.querySelectorAll('.bid-form').forEach(form => {
      form.addEventListener('submit', handleBidSubmit);
    });

    // Attacher les écouteurs de suppression
    auctionListEl.querySelectorAll('.btn-delete-auction').forEach(btn => {
      btn.addEventListener('click', () => deleteAuction(btn.getAttribute('data-id')));
    });
  }

  // ----- Actions -----
  function handleBidSubmit(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const id = form.getAttribute('data-id');
    const auction = auctions.find(a => a.id === id);
    if (!auction) return;

    if (auction.status !== 'active' || auction.endTime <= Date.now()) {
      showToast("Cette enchère est terminée.", true);
      return;
    }

    const [amountInput, bidderInput] = form.querySelectorAll('input');
    const amount = parseFloat(amountInput.value);
    const bidder = bidderInput.value.trim();

    if (!bidder) {
      showToast("Veuillez saisir votre nom.", true);
      return;
    }

    const lastAmount = auction.bids.length > 0
      ? auction.bids[auction.bids.length - 1].amount
      : auction.startPrice;

    if (isNaN(amount) || amount <= lastAmount) {
      showToast(`Votre offre doit être supérieure à ${formatPrice(lastAmount)} DT.`, true);
      return;
    }

    auction.bids.push({
      amount: amount,
      bidder: bidder,
      time: Date.now()
    });

    saveToStorage();
    showToast(`Offre de ${formatPrice(amount)} DT par ${bidder} enregistrée !`);
    render();
  }

  function deleteAuction(id) {
    if (!confirm('Supprimer définitivement cette enchère ?')) return;
    auctions = auctions.filter(a => a.id !== id);
    saveToStorage();
    render();
  }

  // ----- Formulaire de création -----
  const auctionForm = document.getElementById('auctionForm');
  const aType = document.getElementById('aType');
  const aQuantite = document.getElementById('aQuantite');
  const aPrixDepart = document.getElementById('aPrixDepart');
  const aVendeur = document.getElementById('aVendeur');

  auctionForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const type = aType.value;
    const quantity = parseFloat(aQuantite.value);
    const startPrice = parseFloat(aPrixDepart.value);
    const seller = aVendeur.value.trim();

    if (!type) {
      showToast("Sélectionnez un type de déchet.", true);
      return;
    }
    if (isNaN(quantity) || quantity <= 0) {
      showToast("Quantité invalide.", true);
      return;
    }
    if (isNaN(startPrice) || startPrice <= 0) {
      showToast("Prix de départ invalide.", true);
      return;
    }
    if (!seller) {
      showToast("Indiquez votre nom.", true);
      return;
    }

    const now = Date.now();
    const newAuction = {
      id: 'A' + now + Math.random().toString(36).substring(2, 6),
      type: type,
      quantity: quantity,
      startPrice: startPrice,
      seller: seller,
      startTime: now,
      endTime: now + DURATION_MS,
      status: 'active',
      bids: []
    };

    auctions.push(newAuction);
    saveToStorage();
    render();
    showToast("Enchère lancée ! Elle se termine dans 1 heure.");

    // Reset
    auctionForm.reset();
    aType.value = '';
    aQuantite.value = '';
    aPrixDepart.value = '';
    aVendeur.value = '';
  });

  // ----- Tick : mise à jour du timer et clôture -----
  function tick() {
    let needsRender = false;
    const now = Date.now();

    auctions.forEach(a => {
      if (a.status === 'active' && a.endTime <= now) {
        a.status = 'ended';
        needsRender = true;
      }
    });

    if (needsRender) {
      saveToStorage();
      render();
    } else {
      const cards = auctionListEl.querySelectorAll('.auction-card');
      const sorted = [...auctions].sort((a, b) => {
        if (a.status !== b.status) return a.status === 'active' ? -1 : 1;
        return a.endTime - b.endTime;
      });
      cards.forEach((card, idx) => {
        const auction = sorted[idx];
        if (!auction || auction.status !== 'active') return;
        const remaining = auction.endTime - Date.now();
        const timerEl = card.querySelector('.timer');
        if (timerEl && remaining > 0) {
          timerEl.innerHTML = `<i class="fas fa-clock"></i> Temps restant : ${formatTime(remaining)}`;
          if (remaining < 5 * 60 * 1000) {
            timerEl.classList.add('urgent');
          } else {
            timerEl.classList.remove('urgent');
          }
        } else if (remaining <= 0) {
          render();
        }
      });
    }
  }

  // ----- Initialisation -----
  loadFromStorage();
  render();
  tickInterval = setInterval(tick, 1000);
  window.addEventListener('beforeunload', () => clearInterval(tickInterval));
})();