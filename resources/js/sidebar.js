// Simple toggle for mobile sidebar
function toggleSidebar(open) {
    const sidebar = document.getElementById('mobile-drawer');
    const backdrop = document.getElementById('backdrop');
    if (open) {
        sidebar.classList.remove('translate-x-[-100%]');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    } else {
        sidebar.classList.add('translate-x-[-100%]');
        backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

function toggleOffcanvas(open) {
    const panel = document.getElementById('notif-offcanvas');
    const backdrop = document.getElementById('offcanvas-backdrop');
    if (open) {
    panel.classList.remove('translate-x-full');
    backdrop.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    } else {
    panel.classList.add('translate-x-full');
    backdrop.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    }
}


function toggleProfileLg(){
    const dd = document.getElementById('profile-dd-lg');
    dd.classList.toggle('hidden');

    const dd_mobile = document.getElementById('profile-dd-lg-mobile');
    dd_mobile.classList.toggle('hidden');

}



function closeSidebarDetails() {
    document
    .querySelectorAll('#desktop-sidebar details[open]')
    .forEach(d => d.open = false); // or d.removeAttribute('open')
}

function setSidebarCollapsed(state){
    const aside = document.getElementById('desktop-sidebar');
    const iconCollapse = document.getElementById('icon-collapse');
    const iconExpand = document.getElementById('icon-expand');
    if(!aside) return;

    // Attribute for Tailwind data-variants
    aside.setAttribute('data-collapsed', state ? 'true' : 'false');

    // Fallback width classes (in case data-variant isn’t picked up)
    aside.classList.toggle('w-72', !state);
    aside.classList.toggle('w-20', !!state);

    // Hide text labels when collapsed (elements with .label)
    document.querySelectorAll('#desktop-sidebar .label').forEach(el=>{
        el.classList.toggle('hidden', !!state);
    });

    // Flip icons
    if(iconCollapse && iconExpand){
        if(state){ // collapsed
        iconCollapse.classList.add('hidden');
        iconExpand.classList.remove('hidden');
        } else {   // expanded
        iconCollapse.classList.remove('hidden');
        iconExpand.classList.add('hidden');
        }
    }

    // NEW: when collapsing, close all <details>
    // if (state) closeSidebarDetails();

    try { localStorage.setItem('sidebar-collapsed', state ? '1' : '0'); } catch(e){}
}

function toggleSidebarCollapsed(){
    const aside = document.getElementById('desktop-sidebar');
    const isCollapsed = aside?.getAttribute('data-collapsed') === 'true';
    setSidebarCollapsed(!isCollapsed);
}

document.addEventListener('DOMContentLoaded', ()=>{
    const saved = (typeof localStorage !== 'undefined' && localStorage.getItem('sidebar-collapsed') === '1');
    setSidebarCollapsed(saved);
});

// Listen for Livewire `navigate` events
document.addEventListener('livewire:navigated', () => {
    const saved = (typeof localStorage !== 'undefined' && localStorage.getItem('sidebar-collapsed') === '1');
    setSidebarCollapsed(saved);

});

