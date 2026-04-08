const bodyTabella = document.getElementById("bodyTabella");

if (bodyTabella) {
    caricaClassifica();
}

async function caricaClassifica(){
    try{
        const response = await fetch('recupero_classifica.php');
    
        if(!response.ok){
            throw new Error("Errore: " + response.statusText);
        } 

        bodyTabella.innerHTML = "";

        const data = await response.json(); 

        if(data.length === 0){
            bodyTabella.innerHTML = "<tr><td colspan='3'>Classifica vuota</td></tr>";
            return;
        }

        for(let i = 0; i < data.length; i++){
            const partita = data[i];
            const tr = document.createElement('tr');

            const posizione = i + 1; 
            tr.classList.add(`rank-${posizione}`);

            const minuti = Math.floor(partita.tempo_secondi / 60);
            let secondi = partita.tempo_secondi % 60;
            if (secondi < 10) secondi = "0" + secondi;
            const tempoFormattato = `${minuti}:${secondi}`;

            let medaglia = "";
            if (posizione === 1) medaglia = "🥇 ";
            else if (posizione === 2) medaglia = "🥈 ";
            else if (posizione === 3) medaglia = "🥉 ";
            else medaglia = `#${posizione} `;

            tr.innerHTML = `
                <td>${medaglia} ${partita.nickname}</td>
                <td>${partita.mosse}</td>
                <td>${tempoFormattato}</td>
            `;

            bodyTabella.appendChild(tr);
        }

    } catch(error){
        console.error("Errore nel caricamento della classifica:", error);
        if(bodyTabella){
            bodyTabella.innerHTML = "<tr><td colspan='3'>Errore nel caricamento dati</td></tr>";
        } 
    }
}

