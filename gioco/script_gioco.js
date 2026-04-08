const tavolaGioco = document.getElementById("tavolo-gioco");
const cards = document.querySelectorAll(".carta");
const bottoneReset = document.getElementById("reset");
const displayMosse = document.getElementById("mosse");
const displayTempo = document.getElementById("tempo");
const bottonePausa = document.getElementById("bottonePausa"); 
const bottoneRegole = document.getElementById("bottoneRegole");
const bottoneDarkmode = document.getElementById("bottoneDarkmode");
const modale = document.getElementById("regoleModale");
const chiusuraModale = document.querySelector(".chiusura-modale");
const contenitorePrincipale = document.getElementById("contenitorePrincipale");
const barraSuperiore = document.getElementById("barraSuperiore");
const modaleVittoria = document.getElementById("modaleVittoria");
const messaggioVittoria = document.getElementById("messaggioVittoria");
const bottoneRestart = document.getElementById("bottoneRestart");

let carte = [ 
    "💻", "🖱️", "⌨️", "💾", "💿", "🖨️", "📱", "📷", 
    "💻", "🖱️", "⌨️", "💾", "💿", "🖨️", "📱", "📷" 
];

let primaCarta = null;
let secondaCarta = null;
let pannelloDiBlocco = false;
let mosse = 0;
let secondi = 0;
let intervalloTimer = null;
let coppie = 0;
let inPausa = false; 
let timerAvviato = false;

bottoneReset.addEventListener("click", inizioGioco);
bottonePausa.addEventListener("click", attivaDisattivaPausa); 
bottoneRegole.addEventListener("click", apriRegole);
chiusuraModale.addEventListener("click", chiudiRegole);
bottoneDarkmode.addEventListener("click", darkmode);
bottoneRestart.addEventListener("click", function() {
    modaleVittoria.classList.remove("attiva");
    inizioGioco();
});

inizioGioco();

function mescola(array){
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1)); 
        [array[i], array[j]] = [array[j], array[i]];          
    }
}

function inizioGioco(){
    primaCarta = null;
    secondaCarta = null;
    pannelloDiBlocco = false;
    coppie = 0;

    inPausa = false;
    bottonePausa.innerText = "⏸️";
    tavolaGioco.classList.remove("in-pausa");

    mosse = 0;
    displayMosse.innerText = mosse;

    clearInterval(intervalloTimer);
    intervalloTimer = null;
    timerAvviato = false;
    secondi = 0;
    displayTempo.innerText = "00:00";

    mescola(carte);

    for (var i = 0; i < cards.length; i++) {
        cards[i].classList.remove('flippata', 'combacia');
        cards[i].dataset.simbolo = carte[i];
        cards[i].innerHTML = `<span>${carte[i]}</span>`;
        cards[i].removeEventListener('click', flippaCarta);
        cards[i].addEventListener('click', flippaCarta);
    }
}

function attivaDisattivaPausa() {
    if (coppie === 8) return;
    inPausa = !inPausa;
    if (inPausa) {
        bottonePausa.innerText = "▶️"; 
        tavolaGioco.classList.add("in-pausa"); 
    } else {
        bottonePausa.innerText = "⏸️"; 
        tavolaGioco.classList.remove("in-pausa"); 
    }
}

function startTimer() {
    intervalloTimer = setInterval(() => {
        if (!inPausa) {
            secondi++;
            let minuti = Math.floor(secondi / 60);
            let secs = secondi % 60;
            if (secs < 10) secs = "0" + secs;
            if (minuti < 10) minuti = "0" + minuti;
            displayTempo.innerText = `${minuti}:${secs}`;
        }
    }, 1000);
}

function flippaCarta() {
    if (inPausa) return;
    if (pannelloDiBlocco) return;
    if (this === primaCarta) return; 

    if (!timerAvviato) {
        startTimer();
        timerAvviato = true;
    }

    this.classList.add('flippata');

    if (!primaCarta) {
        primaCarta = this;
        return;
    }

    secondaCarta = this;
    incrementaMosse();
    controllaUguali();
}

function incrementaMosse() {
    mosse++;
    displayMosse.innerText = mosse;
}

function controllaUguali() {
    if(primaCarta.dataset.simbolo === secondaCarta.dataset.simbolo) {
        disattivaCarta();
    } else {
        unflippaCarta();
    }
}

function disattivaCarta() {
    primaCarta.classList.add('combacia');
    secondaCarta.classList.add('combacia');
    
    primaCarta.removeEventListener('click', flippaCarta);
    secondaCarta.removeEventListener('click', flippaCarta);

    coppie++;

    if (coppie === 8) {
        clearInterval(intervalloTimer);
        salvaPartita(mosse, secondi);
        setTimeout(() => {
            const messaggio = `Sistema ripristinato in <strong>${mosse}</strong> cicli CPU e <strong>${displayTempo.innerText}</strong>!`;
            messaggioVittoria.innerHTML = messaggio;
            modaleVittoria.classList.add("attiva");
        }, 500); 
    }
    resetBoard();
}

function unflippaCarta() {
    pannelloDiBlocco = true;
    setTimeout(() => {
        primaCarta.classList.remove('flippata');
        secondaCarta.classList.remove('flippata');
        resetBoard();
    }, 1000);
}

function resetBoard() {
    primaCarta = null;
    secondaCarta = null;
    pannelloDiBlocco = false;
}

function apriRegole() {
    modale.classList.add("attiva");
    if (!inPausa && coppie < 8) {
        attivaDisattivaPausa();
    }
}

function chiudiRegole() {
    modale.classList.remove("attiva");
}

function darkmode() {
    document.body.classList.toggle("dark-mode");
    contenitorePrincipale.classList.toggle("dark-mode");
}

async function salvaPartita(mosseFinali, tempoFinale){
    try {
        const response = await fetch('salva_punteggio.php', {
            method: 'POST',
            headers: { 'Content-Type' : 'application/json' },
            body : JSON.stringify({ mosse: mosseFinali, tempo: tempoFinale })
        });

        if (!response.ok) {
            throw new Error(`Errore HTTP: ${response.status}`);
        }
        const data = await response.json();
    } catch(error) {
        console.error("Si è verificato un errore: ", error); 
    }
}