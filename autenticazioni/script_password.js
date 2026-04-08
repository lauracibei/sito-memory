const iconeOcchio = document.querySelectorAll(".icona-password");

for (let i = 0; i < iconeOcchio.length; i++) {
    iconeOcchio[i].addEventListener("click", function() {
        let contenitore = this.parentElement; 
        let campoPassword = contenitore.querySelector("input");
        
        if (campoPassword.type === "password") {
            campoPassword.type = "text";
            this.src = "../immagini/occhiochiuso.png";
        } else {
            campoPassword.type = "password";
            this.src = "../immagini/occhioaperto.png";
        }
    });
}