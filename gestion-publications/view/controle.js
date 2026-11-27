function validerPublication() {
    var titre = document.getElementById('titre').value.trim();
    var region = document.getElementById('region').value.trim();
    var date = document.getElementById('date_publication').value;
    var contenu = document.getElementById('contenu').value.trim();
    var err = document.getElementById('erreur');
    err.innerText = '';

    if (titre.length < 3) {
        err.innerText = 'Le titre doit contenir au moins 3 caractères.';
        return false;
    }
    if (region.length < 2) {
        err.innerText = 'La région est obligatoire (2 caractères minimum).';
        return false;
    }
    if (date === '') {
        err.innerText = 'La date de publication est obligatoire.';
        return false;
    }
    if (contenu.length < 10) {
        err.innerText = 'Les notes doivent contenir au moins 10 caractères.';
        return false;
    }
    return true;
}

function validerReponse() {
    var contenu = document.getElementById('contenu').value.trim();
    var err = document.getElementById('erreur');
    err.innerText = '';

    if (contenu.length < 3) {
        err.innerText = 'La réponse doit contenir au moins 3 caractères.';
        return false;
    }
    return true;
}