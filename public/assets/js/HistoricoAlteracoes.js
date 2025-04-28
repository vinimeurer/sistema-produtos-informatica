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
    var dataInicialInput = document.getElementById('data_inicial');
    var dataFinalInput = document.getElementById('data_final');
    
    dataInicialInput.addEventListener('change', function() {
        var dataInicial = new Date(this.value);
        var dataFinal = new Date(dataFinalInput.value);
        
        if (dataInicial > dataFinal) {
            dataFinalInput.value = this.value;
        }
        
        var maxDate = new Date(dataInicial);
        maxDate.setDate(maxDate.getDate() + 31);
        dataFinalInput.max = maxDate.toISOString().split('T')[0];
    });
    
    dataFinalInput.addEventListener('change', function() {
        var dataInicial = new Date(dataInicialInput.value);
        var dataFinal = new Date(this.value);
        
        if (dataFinal < dataInicial) {
            dataInicialInput.value = this.value;
        }
        
        var minDate = new Date(dataFinal);
        minDate.setDate(minDate.getDate() - 31);
        dataInicialInput.min = minDate.toISOString().split('T')[0];
    });
});

