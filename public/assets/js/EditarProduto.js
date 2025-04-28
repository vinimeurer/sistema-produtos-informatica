document.addEventListener('DOMContentLoaded', function() {
    const MAX_IMAGES = 10;
    let currentImages = 0;
    let existingImages = [];
    let removedImages = [];

    const modal = document.getElementById("infoModal");
    const infoIcon = document.getElementById("infoIcon");
    const span = document.getElementById("closeModal");
    const closeModalButton = document.getElementById("closeModalButton");
    const imagePreview = document.getElementById('imagePreview');
    const fileInput = document.getElementById('imagens');
    const removedImagesInput = document.getElementById('imagensRemovidas');

    // Modal Handling
    function setupModalHandling() {
        infoIcon.onclick = () => {
            modal.classList.add("show");
            modal.style.display = "block"; 
        }

        span.onclick = closeModal;
        closeModalButton.onclick = closeModal;

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        function closeModal() {
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none"; 
            }, 300); 
        }
    }

    // Currency Formatting
    function formatarValor(input) {
        let valor = input.value.replace(/\D/g, '');
        valor = (parseInt(valor) / 100).toFixed(2);
        
        if (parseFloat(valor) > 99999.99) {
            valor = '99999.99';
        }
        
        valor = valor.replace('.', ',');
        input.value = valor;
    }

    // Setup Currency Inputs
    function setupCurrencyInputs() {
        const camposValor = ['valor'];
        camposValor.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                elemento.parentNode.insertBefore(wrapper, elemento);
                wrapper.appendChild(elemento);

                const prefix = document.createElement('span');
                prefix.textContent = 'R$ ';
                prefix.style.position = 'absolute';
                prefix.style.left = '8px';
                prefix.style.top = '36%';
                prefix.style.transform = 'translateY(-50%)';
                prefix.style.color = '#000';
                wrapper.insertBefore(prefix, elemento);

                elemento.style.paddingLeft = '30px';

                elemento.addEventListener('input', function() {
                    formatarValor(this);
                });
                elemento.addEventListener('blur', function() {
                    if (this.value) {
                        let valor = this.value.replace(/\D/g, '');
                        if (valor) {
                            formatarValor(this);
                        }
                    }
                });
            }
        });
    }

    // Image Preview Management
    function updateImageUploadStatus() {
        const uploadLabel = document.querySelector('.upload-container label');
        const totalImages = currentImages + existingImages.length;
        uploadLabel.textContent = `Adicionar/Substituir Imagens (${totalImages}/10)`;
        validateForm();
    }

    // Remove Existing Image
    window.removeExistingImage = function(index) {
        const existingImageElements = document.querySelectorAll('.preview-image');
        if (existingImageElements[index]) {
            const imageSource = existingImageElements[index].querySelector('img').getAttribute('src');
            
            existingImageElements[index].remove();
            existingImages = existingImages.filter(img => img !== imageSource);
            
            removedImages.push(imageSource.split('base64,')[1]);
            removedImagesInput.value = removedImages.join(',');
            
            updateImageUploadStatus();
        }
    }

    // New Image Preview
    function setupImagePreview() {
        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;
            
            if (existingImages.length + currentImages + files.length > MAX_IMAGES) {
                alert(`Você pode adicionar no máximo ${MAX_IMAGES} imagens.`);
                fileInput.value = '';
                return;
            }

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'preview-image';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'remove-image';
                    removeButton.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
                    
                    removeButton.addEventListener('click', function() {
                        previewDiv.remove();
                        currentImages--;
                        updateImageUploadStatus();
                    });
                    
                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeButton);
                    
                    imagePreview.appendChild(previewDiv);
                    currentImages++;
                    updateImageUploadStatus();
                };
                
                reader.readAsDataURL(file);
            });
        });
    }

    // Form Validation
    function validateForm() {
        const requiredFields = ['nome', 'codigo', 'tipoProduto', 'valor', 'descricao'];
        const submitBtn = document.getElementById('submitBtn');
        
        const allFieldsFilled = requiredFields.every(field => {
            const element = document.getElementById(field);
            return element && element.value.trim() !== '';
        });

        const hasImages = currentImages > 0 || existingImages.length > 0;

        submitBtn.disabled = !(allFieldsFilled && hasImages);
        submitBtn.style.backgroundColor = submitBtn.disabled ? '#85ad98' : '#0f2566';
        submitBtn.style.cursor = submitBtn.disabled ? 'not-allowed' : 'pointer';
    }

    // Initial Setup
    function initializeExistingImages() {
        document.querySelectorAll('.preview-image').forEach((el, index) => {
            existingImages.push(el.querySelector('img').getAttribute('src'));
        });
        updateImageUploadStatus();
    }

    // Run All Setup Functions
    setupModalHandling();
    setupCurrencyInputs();
    setupImagePreview();
    initializeExistingImages();

    // Add Validation Listeners
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', validateForm);
    });
});