import './bootstrap';
import 'preline';

// import './forum-listeners';

// Listen for Livewire `navigate` events
document.addEventListener('livewire:navigated', () => {
    // Reinitialize Flowbite
    window.dispatchEvent(new Event('load'));

    initFlowbite();

});