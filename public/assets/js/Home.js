
document.addEventListener('DOMContentLoaded', function() {
    const reportsHeader = document.querySelector('.reports-header');
    const reportsContent = document.querySelector('.reports-content');
    const chevron = document.querySelector('.reports-header i');
    
    reportsHeader.addEventListener('click', function() {
        reportsContent.classList.toggle('show');
        chevron.classList.toggle('rotate');
    });
});
