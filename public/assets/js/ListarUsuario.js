var modal = document.getElementById("infoModal");

var infoIcon = document.getElementById("infoIcon");

var span = document.getElementById("closeModal");

var closeModalButton = document.getElementById("closeModalButton");

infoIcon.onclick = function() {
    modal.classList.add("show");
    modal.style.display = "block"; 
}

span.onclick = function() {
    closeModal();
}

window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}

function closeModal() {
    modal.classList.remove("show");
    setTimeout(function() {
        modal.style.display = "none"; 
    }, 300); 
}

closeModalButton.onclick = function() {
    closeModal();
}

document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-btn');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const documento = this.getAttribute('data-documento');
            const codVerificacao = this.getAttribute('data-cod-verificacao');

            const tempTextArea = document.createElement('textarea');
            tempTextArea.value = `Documento: ${documento}\nCódigo da Revenda: ${codVerificacao}`;
            document.body.appendChild(tempTextArea);

            tempTextArea.select();
            document.execCommand('copy');

            document.body.removeChild(tempTextArea);

            this.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                this.innerHTML = '<i class="fa-regular fa-copy"></i>';
            }, 1000);
        });
    });
});