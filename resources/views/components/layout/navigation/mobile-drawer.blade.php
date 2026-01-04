<div>
    <div id="backdrop" class="lg:hidden fixed inset-0 bg-black/50 hidden z-40" onclick="toggleSidebar(false)"></div>
    <aside id="mobile-drawer" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white border-r z-50 translate-x-[-100%] transition-transform duration-200 ease-out">


        <x-layout.navigation.brand
            variant="mobile"
            :use-auth-brand="true"
            name="SHPO"
            subtitle="SHPO Portal"
            badge-class="bg-indigo-600 text-white"
            :collapsed-aware="true"
        />

  
        <x-layout.navigation.menu variant="mobile" /> 

    </aside>

</div> 