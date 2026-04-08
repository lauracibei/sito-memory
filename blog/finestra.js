document.addEventListener('DOMContentLoaded', function() {

    const bottoniElimina = document.querySelectorAll('.js-elimina');

    for (let i = 0; i < bottoniElimina.length; i++) {
        bottoniElimina[i].addEventListener('click', function() {
            const idPost = this.getAttribute('data-id');
            apriModale(idPost);
        });
    }
    
    const chiusuraX = document.querySelector('.chiusura-modale');
    const bottoneAnnulla = document.querySelector('.btn-annulla');
    if (chiusuraX) {
        chiusuraX.addEventListener('click', chiudiModale);
    }

    if (bottoneAnnulla) {
        bottoneAnnulla.addEventListener('click', chiudiModale);
    }
});

function apriModale(idPost) {
    const linkEliminazione = 'elimina_post.php?Post_Id=' + idPost;
    const bottoneConferma = document.getElementById('linkConfermaEliminazione');
    
    if (bottoneConferma) {
        bottoneConferma.href = linkEliminazione;
    }
    
    const modale = document.getElementById('modaleEliminazione');
    if (modale) {
        modale.classList.add("attiva");
    }
}

function chiudiModale() {
    const modale = document.getElementById('modaleEliminazione');
    if (modale) {
        modale.classList.remove("attiva");
    }
}