(function () {
  const toggleButton = document.getElementById('assistantToggle');
  const closeButton = document.getElementById('assistantClose');
  const panel = document.getElementById('assistantPanel');
  const messages = document.getElementById('assistantMessages');
  const actions = document.getElementById('assistantActions');
  const form = document.getElementById('assistantForm');
  const input = document.getElementById('assistantInput');
  const isWastePage = Boolean(document.getElementById('wasteForm'));

  function addMessage(text, sender) {
    const message = document.createElement('p');
    message.className = `assistant-message is-${sender}`;
    message.textContent = text;
    messages.appendChild(message);
    messages.scrollTop = messages.scrollHeight;
  }

  function setOpen(isOpen) {
    panel.hidden = !isOpen;
    toggleButton.setAttribute('aria-expanded', String(isOpen));
    if (isOpen) input.focus();
    else toggleButton.focus();
  }

  function getAnswer(question) {
    const normalized = question
      .toLocaleLowerCase('fr')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');

    if (/offre|encherir|enchere|mise|ench[eè]re/.test(normalized)) {
      if (/offre|encherir|participer/.test(normalized) && !/creer|lancer|vendre|mettre/.test(normalized)) {
        return {
          text: 'Pour faire une offre, choisissez une enchère en cours. Saisissez votre nom et un montant supérieur à la meilleure offre (ou au prix de départ), puis cliquez sur « Enchérir ». Les enchères durent 1 heure.',
            target: 'auctionList',
            link: { href: 'encheres.html#auctionList', label: 'Voir les enchères en cours' }
        };
      }
      if (/creer|lancer|vendre|mettre/.test(normalized)) {
        return {
          text: 'Pour créer une enchère, indiquez le type de déchet, la quantité en kg, le prix de départ en DT et votre nom. Cliquez sur « Lancer l’enchère » : elle sera ouverte pendant 1 heure.',
            target: 'auctionForm',
            link: { href: 'encheres.html#auctionForm', label: 'Ouvrir le formulaire d’enchère' }
        };
      }
      return {
        text: 'Vous pouvez créer une enchère en remplissant son formulaire, ou participer à une enchère en cours en proposant un montant supérieur à l’offre actuelle.',
        target: 'auctionForm',
        link: { href: 'encheres.html#auctionForm', label: 'Ouvrir les enchères' }
      };
    }

    if (/dechet|ajout|nouveau|enregistr|type/.test(normalized)) {
      if (isWastePage) {
        return {
          text: 'Pour ajouter un déchet, choisissez son type. Si le type n’est pas dans la liste, sélectionnez « Autre » et saisissez son nom. Indiquez ensuite la quantité en kg, la date et le statut, puis cliquez sur « Enregistrer ». Le nouveau type sera conservé dans la liste.',
          target: 'wasteForm'
        };
      }
      return {
        text: 'Pour enregistrer un nouveau déchet agricole, choisissez son type (ou « Autre » pour saisir un nouveau nom), indiquez la quantité, la date et le statut, puis cliquez sur « Enregistrer ».',
        link: { href: 'index.html#wasteForm', label: 'Ouvrir Gestion des déchets' }
      };
    }

    return {
      text: 'Je peux vous aider à ajouter un déchet agricole, créer une enchère ou faire une offre. Choisissez un sujet ci-dessus ou posez une question sur l’une de ces étapes.'
    };
  }

  function answerQuestion(question) {
    const answer = getAnswer(question);
    addMessage(question, 'user');
    addMessage(answer.text, 'assistant');

    const target = answer.target && document.getElementById(answer.target);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const firstInput = target.querySelector('input, select, button');
      if (firstInput) firstInput.focus({ preventScroll: true });
    } else if (answer.link) {
      const link = document.createElement('a');
      link.className = 'assistant-link';
      link.href = answer.link.href;
      link.textContent = answer.link.label;
      messages.appendChild(link);
      messages.scrollTop = messages.scrollHeight;
    }
  }

  toggleButton.addEventListener('click', () => setOpen(panel.hidden));
  closeButton.addEventListener('click', () => setOpen(false));

  actions.addEventListener('click', event => {
    const button = event.target.closest('button[data-prompt]');
    if (button) answerQuestion(button.getAttribute('data-prompt'));
  });

  form.addEventListener('submit', event => {
    event.preventDefault();
    const question = input.value.trim();
    if (!question) return;
    input.value = '';
    answerQuestion(question);
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !panel.hidden) setOpen(false);
  });
})();
