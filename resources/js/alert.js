export function showAlert(message) {
    const alertElement = document.getElementById('dismiss-alert');
    const messageElement = alertElement.querySelector('h3');
    messageElement.innerHTML = message;
    alertElement.classList.remove('hidden');
    alertElement.classList.add('opacity-100');

    setTimeout(() => {
        alertElement.classList.add('hidden');
    }, 10000);

 
}


