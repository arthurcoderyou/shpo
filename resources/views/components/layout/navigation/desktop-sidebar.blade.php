
<aside id="desktop-sidebar" data-collapsed="true"  class="hidden lg:flex lg:flex-col data-[collapsed=true]:w-20 transition-[width] lg:w-72 lg:min-h-screen lg:sticky lg:top-0 lg:border-r bg-white">

    <x-layout.navigation.brand
        variant="desktop"
        :use-auth-brand="true"
        name="SHPO"
        subtitle="SHPO Portal"
        badge-class="bg-indigo-600 text-white"
        :collapsed-aware="true"
    />


    <div class="group data-[collapsed=true]:w-16 w-full">
         
        <x-layout.navigation.menu variant="desktop" />
    </div>

</aside>