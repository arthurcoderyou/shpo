import './bootstrap';
import 'preline';
import '@tailwindplus/elements'; // registers <el-dropdown>, <el-menu>, etc.

// import { initFlowbite } from 'flowbite';

// import './forum-listeners';

// Listen for Livewire `navigate` events
document.addEventListener('livewire:navigated', () => {
    // Reinitialize Flowbite
    window.dispatchEvent(new Event('load'));

    initFlowbite(); // function to reinitiate flowbite 

});



import './echo';
import './event';
import './sidebar';
 

//  document.addEventListener('alpine:init', () => {

 
    