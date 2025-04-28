function mascara(i) {
    var v = i.value.replace(/\D/g, ''); 
  
    i.setAttribute("maxlength", "14");
    
    if (v.length > 11) v = v.slice(0, 11);
    
    if (v.length > 2) v = v.replace(/(\d{3})(\d)/, "$1.$2");
        if (v.length > 5) v = v.replace(/(\d{3}\.\d{3})(\d)/, "$1.$2");
        if (v.length > 8) v = v.replace(/(\d{3}\.\d{3}\.\d{3})(\d)/, "$1-$2");
  
    i.value = v;
  }

function atualizarLogin(input) {
  var loginField = document.getElementById('login');
  var documentoValue = input.value.replace(/\D/g, ''); 
  loginField.value = documentoValue; 
}

function removerMascara() {
  var documentoField = document.getElementById('documento');
  documentoField.value = documentoField.value.replace(/\D/g, ''); 
}


function validaCPF(cpf) {
  var Soma = 0;
  var Resto;

  var strCPF = String(cpf).replace(/[^\d]/g, '');

  if (strCPF.length !== 11) return false;

  if ([
      '00000000000',
      '11111111111',
      '22222222222',
      '33333333333',
      '44444444444',
      '55555555555',
      '66666666666',
      '77777777777',
      '88888888888',
      '99999999999'
  ].indexOf(strCPF) !== -1) return false;

  for (let i = 1; i <= 9; i++) {
      Soma += parseInt(strCPF.substring(i - 1, i)) * (11 - i);
  }

  Resto = (Soma * 10) % 11;

  if (Resto === 10 || Resto === 11) Resto = 0;

  if (Resto !== parseInt(strCPF.substring(9, 10))) return false;

  Soma = 0;

  for (let i = 1; i <= 10; i++) {
      Soma += parseInt(strCPF.substring(i - 1, i)) * (12 - i);
  }

  Resto = (Soma * 10) % 11;

  if (Resto === 10 || Resto === 11) Resto = 0;

  if (Resto !== parseInt(strCPF.substring(10, 11))) return false;

  return true;
}

function validarDocumento(input) {
  var cpf = input.value.replace(/\D/g, ''); 
  var errorMessage = document.getElementById('cpfError');

  if (cpf.length < 11) {
      errorMessage.style.display = 'none'; 
      input.style.borderColor = ''; 
      return;
  }

  if (!validaCPF(cpf)) {
      errorMessage.style.display = 'inline'; 
      input.style.borderColor = 'red'; 
      input.style.color = 'red'; 
      input.style.backgroundColor = '#ffe4e4'; 

  } else {
      errorMessage.style.display = 'none'; 
      input.style.borderColor = ''; 
      input.style.borderColor = ''; 
      input.style.color = ''; 
      input.style.backgroundColor = ''; 
  }
}

function removerMascara() {
  var documentoField = document.getElementById('documento');
  var cpf = documentoField.value.replace(/\D/g, ''); 

  if (!validaCPF(cpf)) {
      alert("O CPF inserido é inválido!");
      return false; 
  }

  documentoField.value = cpf;
}

function validarSenha(input) {
    var senha = typeof input === 'string' ? input : input.value;
    var errorMessage = document.getElementById('senhaError');
    
    if (senha.length === 0) {
        if (typeof input !== 'string') {
            errorMessage.style.display = 'none';
            input.style.borderColor = '';
            input.style.color = '';
            input.style.backgroundColor = '';
        }
        return true;
    }
    
    var regexMaiuscula = /[A-Z]/;
    var regexMinuscula = /[a-z]/;
    var regexNumero = /[0-9]/;
    var regexEspecial = /[@#$%&*]/;

    var validaTamanho = senha.length >= 8;
    var validaMaiuscula = regexMaiuscula.test(senha);
    var validaMinuscula = regexMinuscula.test(senha);
    var validaNumero = regexNumero.test(senha);
    var validaEspecial = regexEspecial.test(senha);

    var senhaValida = validaTamanho && validaMaiuscula && validaMinuscula && validaNumero && validaEspecial;

    if (typeof input !== 'string') {
        if (!senhaValida && senha.length > 0) {
            errorMessage.style.display = 'inline';
            input.style.borderColor = 'red';
            input.style.color = 'red';
            input.style.backgroundColor = '#ffe4e4';
        } else {
            errorMessage.style.display = 'none';
            input.style.borderColor = '';
            input.style.color = '';
            input.style.backgroundColor = '';
        }
    }

    return senhaValida || senha.length === 0;
}

// Adicionando as funções de endereço
function mascararCEP(input) {
    var value = input.value.replace(/\D/g, '');
    
    if (value.length > 8) value = value.slice(0, 8);
    
    if (value.length > 5) {
        value = value.slice(0, 5) + '-' + value.slice(5);
    }
    
    input.value = value;
}

function validarCEP(cep) {
    cep = cep.replace(/\D/g, '');
    return cep.length === 8;
}

function buscarCEP() {
    var cep = document.getElementById('cep').value.replace(/\D/g, '');
    var cepError = document.getElementById('cepError');
    
    if (cep.length !== 8) {
        cepError.style.display = 'inline';
        return;
    }
    
    cepError.style.display = 'none';
    
    // Mostrar indicador de carregamento
    document.getElementById('cep').style.backgroundColor = '#f0f0f0';
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('cep').style.backgroundColor = '';
            
            if (data.erro) {
                cepError.style.display = 'inline';
                return;
            }
            
            document.getElementById('uf').value = data.uf;
            document.getElementById('municipio').value = data.localidade;
            document.getElementById('rua').value = data.logradouro;
            
            // Foco no campo número após preenchimento automático
            if (data.logradouro) {
                document.getElementById('numero').focus();
            }
        })
        .catch(error => {
            document.getElementById('cep').style.backgroundColor = '';
            cepError.style.display = 'inline';
            console.error('Erro ao buscar CEP:', error);
        });
}

// ################################################## SCRIPT PARA MODAL LEGNDA

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

if (closeModalButton) {
    closeModalButton.onclick = function() {
        closeModal();
    }
}

// ###########################################################################

function validarFormulario() {
    var nome = document.getElementById('nome').value.trim();
    var documento = document.getElementById('documento').value;
    var login = document.getElementById('login').value;
    var senha = document.getElementById('senha').value;
    var cep = document.getElementById('cep').value.replace(/\D/g, '');
    var uf = document.getElementById('uf').value.trim();
    var municipio = document.getElementById('municipio').value.trim();
    var rua = document.getElementById('rua').value.trim();
    var numero = document.getElementById('numero').value.trim();
    

    var documentoValido = validaCPF(documento.replace(/\D/g, ''));
    var senhaValida = validarSenha(senha);
    var cepValido = cep.length === 8;

    var submitBtn = document.getElementById('submitBtn');
    if (nome && documentoValido && login && senhaValida && cepValido && uf && municipio && rua && numero) {
        submitBtn.disabled = false;
        submitBtn.style.backgroundColor = '#0f2566'; 
        submitBtn.style.color = "#ffffff";
        submitBtn.style.cursor = 'pointer'; 
    } else {
        submitBtn.disabled = true;
        submitBtn.style.backgroundColor = '#85ad98'; 
        submitBtn.style.color = "#ffffff";
        submitBtn.style.cursor = 'not-allowed';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    validarFormulario();
    
    // Aplicar máscara ao CEP inicial
    var cepInput = document.getElementById('cep');
    if (cepInput && cepInput.value) {
        mascararCEP(cepInput);
    }
});

document.querySelectorAll('input, select').forEach(function(element) {
    element.addEventListener('input', validarFormulario);
});

document.getElementById('senha').addEventListener('input', function() {
    validarSenha(this);
    validarFormulario();
});

// Adicionando eventos para os campos de endereço
document.getElementById('cep').addEventListener('input', function() {
    mascararCEP(this);
    validarFormulario();
});

document.getElementById('cep').addEventListener('blur', buscarCEP);

document.getElementById('uf').addEventListener('input', validarFormulario);
document.getElementById('municipio').addEventListener('input', validarFormulario);
document.getElementById('rua').addEventListener('input', validarFormulario);
document.getElementById('numero').addEventListener('input', validarFormulario);