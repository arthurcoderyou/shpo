 
<!-- Card Section -->
<div class="  px-4 pb-10 sm:px-6 lg:px-8   mx-auto">
    {{-- <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div> --}}
     
    <!-- Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">


        {{-- 
        <!-- Pending Users without Role --> 
        @if (Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('user list view'))
            <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-300">
                
                <!-- Content Section -->
                <div class="p-5 flex justify-between items-center gap-x-4">
                    
                    <div>
                        <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">
                            Users Role update pending
                        </p>
                        <div class="mt-2 flex items-center gap-x-3">
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">
                                {{ $usersUpdatePending ?? 0 }}
                            </h3>
                        </div>
                    </div>

                    <div class="shrink-0 flex justify-center items-center size-12 bg-blue-600 text-white rounded-full shadow-md hover:scale-105 transform transition duration-300">
                        

                        <svg class="shrink-0 size-5" class="size-6"   xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464l349.5 0c-8.9-63.3-63.3-112-129-112l-91.4 0c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3z"/></svg>
                
                

                    </div>

                </div>

                <!-- Footer Link -->
                <a href="{{ route('user.index',['selected_role' => 'no_role','role_request' => 'user']) }}"
                    class="py-4 px-5 flex justify-between items-center text-sm font-medium text-gray-700 border-t border-gray-200 hover:bg-gray-100 active:bg-gray-200 focus:outline-none rounded-b-2xl transition-colors duration-200">
                    View
                    <svg class="size-4 ml-2 text-gray-500 group-hover:text-gray-700 transition" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </a>

            </div>

 
        @endif
        <!-- End Pending Users without Role -->
        --}}


        
        <!-- Total Users -->
        {{-- @if (Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('user list view'))
        <div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-300">
            
            <!-- Content Section -->
            <div class="p-5 flex justify-between items-center gap-x-4">
                
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        Total Users
                    </p>
                    <div class="mt-2 flex items-center gap-x-3">
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-800">
                            {{ $usersAllTime ?? 0 }}
                        </h3>
                    </div>
                </div>

                <div class="shrink-0 flex justify-center items-center size-12 bg-blue-600 text-white rounded-full shadow-md hover:scale-105 transform transition duration-300">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>

            </div>

            <!-- Footer Link -->
            <a href="{{ route('user.index') }}"
                class="py-4 px-5 flex justify-between items-center text-sm font-medium text-gray-700 border-t border-gray-200 hover:bg-gray-100 active:bg-gray-200 focus:outline-none rounded-b-2xl transition-colors duration-200">
                View
                <svg class="size-4 ml-2 text-gray-500 group-hover:text-gray-700 transition" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </a>

        </div>
        @endif --}}
        <!-- ./ Total Users -->

 
        <!-- User Panels -->
        @if (
            Auth::user()->hasPermissionTo('system access global admin') || 
            Auth::user()->hasPermissionTo('system access admin')
            )   

             
            <livewire:dashboard.dashboard-tile.user-tile
                    title="New Users (No Role)"  
                    :icon="view('components.icons.user-circle')->render()" 
                    :route="route('user.index',['selected_role' => 'no_role','role_request' => 'user'])" 
                    selected_role="no_role"
                    role_request="user"
                    {{-- :dataKey="Auth::user()->can('system access global admin') ? 'admin-usersUpdatePending' : 'usersUpdatePending'"  --}}
                    
                    :iconColor="$iconColor"
                    :iconBg="$iconBg" 
                />

            <livewire:dashboard.dashboard-tile.user-tile
                title="New Reviewers (No Role)"  
                :icon="view('components.icons.user-request-reviewer')->render()" 
                :route="route('user.index',['selected_role' => 'no_role','role_request' => 'reviewer'])" 
                selected_role="no_role"
                role_request="reviewer"
                {{-- :dataKey="Auth::user()->can('system access global admin') ? 'admin-reviewersUpdatePending' : 'reviewersUpdatePending'"   --}}
                :iconColor="$iconColor"
                :iconBg="$iconBg" 
            />


            <livewire:dashboard.dashboard-tile.user-tile
                    title="Total Users"  
                    :icon="view('components.icons.users-total')->render()" 
                    :route="route('user.index')" 
                    :dataKey="Auth::user()->can('system access global admin') ? 'admin-usersAllTime' : 'usersAllTime'"   
                    :iconColor="$iconColor"
                    :iconBg="$iconBg"
                /> 

            <!-- project documents list view --> 
            <livewire:dashboard.dashboard-tile.project-document-tile
                    title="Open Review Project Documents"  
                    :icon="view('components.icons.projects-total')->render()" 
                    :route="route('project-document.index',[
                        'review_status' => 'open_review'
                    ])" 
                    reviewStatus="open_review" 
                    :iconColor="$iconColor"
                    :iconBg="$iconBg" 
                />


             <!-- project documents list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="All Project Documents"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index')" 
                        reviewStatus="all" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="All Project Documents with Required Changes"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index',[
                            'review_status' => 'changes_requested'
                        ])" 
                        reviewStatus="changes_requested"  
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
      
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="Pending Review"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index',[
                            'review_status' => 'pending'
                        ])" 
                        reviewStatus="pending"  
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
 
        @endif
        <!-- ./ User Panels -->


        <!-- Project Panels -->
            
            @if(Auth::user()->hasPermissionTo('system access user'))
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-tile
                        title="My Projects"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project.index')" 
                        routeKey="project.index" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />

                <!-- project documents list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="My Project Documents"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index')" 
                        reviewStatus="all" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="My Project Documents with Required Changes"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index',[
                            'review_status' => 'changes_requested'
                        ])" 
                        reviewStatus="changes_requested"  
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
      
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="My Project Documents Pending Review"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index',[
                            'review_status' => 'pending'
                        ])" 
                        reviewStatus="pending"  
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
    
            @endif


            @if(Auth::user()->hasPermissionTo('system access reviewer'))
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-tile
                        title="All Projects"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project.index.all.no-drafts')" 
                        routeKey="project.index.all.no-drafts" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />

                <!-- project documents list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="All Project Documents"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index')" 
                        reviewStatus="all" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="All Project Documents with Required Changes"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index',[
                            'review_status' => 'changes_requested'
                        ])" 
                        reviewStatus="changes_requested"  
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
      
                <!-- project list view --> 
                <livewire:dashboard.dashboard-tile.project-document-tile
                        title="Pending Review"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project-document.index',[
                            'review_status' => 'pending'
                        ])" 
                        reviewStatus="pending"  
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
    
            @endif





            <!-- only one needs to be shown on this portion -->
                <!-- project list view all  -->
                @if (Auth::user()->hasPermissionTo('system access global admin') 
                || Auth::user()->hasPermissionTo('project list view all') )    

                    
                    <livewire:dashboard.dashboard-tile.project-tile
                            title="All Projects"  
                            :icon="view('components.icons.projects-total')->render()" 
                            :route="route('project.index.all')" 
                            routeKey="project.index.all" 
                            :iconColor="$iconColor"
                            :iconBg="$iconBg" 
                        />
        
        
                {{-- @endif --}}

                <!-- project list view all no drafts  -->
                @elseif (Auth::user()->hasPermissionTo('system access global admin')  ||
                // || Auth::user()->hasPermissionTo('project list view all no drafts') 
                Auth::user()->hasPermissionTo('system access admin') 
                
                )   

                    
                    <livewire:dashboard.dashboard-tile.project-tile
                            title="All Projects (No Drafts )"  
                            :icon="view('components.icons.projects-total')->render()" 
                            :route="route('project.index.all.no-drafts')" 
                            routeKey="project.index.all.no-drafts" 
                            :iconColor="$iconColor"
                            :iconBg="$iconBg" 
                        />
        
        
                @endif
            <!-- ./ only one needs to be shown on this portion -->

            {{-- - 
            <!-- project list view update pending all  -->
            @if (Auth::user()->hasPermissionTo('system access global admin') ||
            Auth::user()->hasPermissionTo('system access admin') 
            // || Auth::user()->hasPermissionTo('project list view update pending all') 
            )    

                
                <livewire:dashboard.dashboard-tile.project-tile
                        title="All Projects Update Pending"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project.index.update-pending.all')" 
                        routeKey="project.index.update-pending.all" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
    
            @endif

            <!-- project list view review pending all  -->
            @if (Auth::user()->hasPermissionTo('system access global admin') || 
            Auth::user()->hasPermissionTo('system access admin') 
            //  || Auth::user()->hasPermissionTo('project list view review pending all')
              )    

                
                <livewire:dashboard.dashboard-tile.project-tile
                        title="All Projects Review Pending"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project.index.review-pending.all')" 
                        routeKey="project.index.review-pending.all" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
    
            @endif



        
            

            <!-- project list view update pending all linked -->
            @if (Auth::user()->hasPermissionTo('system access global admin') || 
            // Auth::user()->hasPermissionTo('project list view update pending all linked') 
            Auth::user()->hasPermissionTo('system access admin')            
            )    

                
                <livewire:dashboard.dashboard-tile.project-tile
                        title="Linked Projects Update Pending"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project.index.update-pending.all-linked')" 
                        routeKey="project.index.update-pending.all" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
    
            @endif

            <!-- project list view review pending all  -->
            @if (Auth::user()->hasPermissionTo('system access global admin') || 
            Auth::user()->hasPermissionTo('system access admin')
            // Auth::user()->hasPermissionTo('project list view review pending all linked') 
            )    

                
                <livewire:dashboard.dashboard-tile.project-tile
                        title="Linked Projects Review Pending"  
                        :icon="view('components.icons.projects-total')->render()" 
                        :route="route('project.index.review-pending.all-linked')" 
                        routeKey="project.index.review-pending.all-linked" 
                        :iconColor="$iconColor"
                        :iconBg="$iconBg" 
                    />
    
    
            @endif
            
            --}}
        

        <!-- ./ Project Panels -->




    </div>
    <!-- End Grid --> 

    @if(Auth::check() && !Auth::user()->hasPermissionTo('system access user')  )
    
    <!-- User Report -->
    <div class="relative overflow-hidden" wire:ignore>
        <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-10 ">
            <div class="text-center ">

                <h1 class="text-4xl sm:text-6xl font-bold text-gray-800 ">
                    <span class="text-sky-500">Users</span> Report
                </h1>

                <div class="mt-7 sm:mt-12 mx-auto max-w-full relative">
                    <div class="grid grid-cols-12 gap-6">


                        <div class="col-span-12  md:col-span-6">
                            <canvas wire:ignore id="data_user_count_table_report" class="w-auto h-auto "></canvas>
                        </div>
 
                        <div class="col-span-12 md:col-span-6">
                            <canvas wire:ignore id="data_user_registered_count_table_report" class="w-auto h-auto "></canvas>
                        </div>
                        

                    </div>

                </div>
 
            </div>
        </div>

        <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-10 ">
          <div class="text-center ">

              <h1 class="text-4xl sm:text-6xl font-bold text-gray-800 ">
                  <span class="text-sky-500">Projects</span> Report
              </h1>

              <div class="mt-7 sm:mt-12 mx-auto max-w-full relative">
                  <div class="grid grid-cols-12 gap-6">

                    <div class="col-span-12 md:col-span-6">
                        <canvas wire:ignore id="data_project_count_per_status_table_report" class="w-auto h-auto "></canvas>
                    </div> 

                    <div class="col-span-12 md:col-span-6">
                        <canvas wire:ignore id="data_project_count_per_month_table_report" class="w-auto h-auto "></canvas>
                    </div>
                  
                    <div class="col-span-12  md:col-span-4">
                        <canvas wire:ignore id="data_project_average_approval_time_table_report" class="w-auto h-auto "></canvas>
                    </div>


                    <div class="col-span-12  md:col-span-4">
                        <canvas wire:ignore id="data_project_average_response_time_table_report" class="w-auto h-auto "></canvas>
                    </div>

                    <div class="col-span-12  md:col-span-4">
                        <canvas wire:ignore id="data_project_average_review_time_table_report" class="w-auto h-auto "></canvas>
                    </div>
                    
                    
                      

                  </div>

              </div>

          </div>
      </div>



    </div>
    <!-- ./User Report -->



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

     <!-- User Report  -->
    <script>

      /* Total User per Role Report  */
        const ctx_data_user_count_table_report = document.getElementById('data_user_count_table_report');

        const data_user_count_table = @json($data_user_count_table );
        const user_count_table_labels = data_user_count_table.map(item => item.label);
        const user_count_table_values = data_user_count_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_user_count_table_report, {
            type: 'bar',
            data: {
            labels: user_count_table_labels,
            datasets: [{
                label: '# of Total Users',
                data: user_count_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: true,
                  suggestedMin: 0,   // Ensure small values are visible
                 // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total User per Role Report  */



      /* Total Registered User Report  */
        const ctx_data_user_registered_count_table_report = document.getElementById('data_user_registered_count_table_report');

        const data_user_registered_count_table = @json($data_user_registered_count_table );
        const user_registered_count_table_labels = data_user_registered_count_table.map(item => item.label);
        const user_registered_count_table_values = data_user_registered_count_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_user_registered_count_table_report, {
            type: 'bar',
            data: {
            labels: user_registered_count_table_labels,
            datasets: [{
                label: '# of Total Registered Users',
                data: user_registered_count_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: true,
                  suggestedMin: 0,   // Ensure small values are visible
                 // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total Registered User Report  */


    </script>
    <!-- ./ User Report  -->


     <!-- Project Report  -->
     <script>

      /* Total Project per Status  */
        const ctx_data_project_count_per_status_table_report = document.getElementById('data_project_count_per_status_table_report');

        const data_project_count_per_status_table = @json($data_project_count_per_status_table );
        const project_count_per_status_table_labels = data_project_count_per_status_table.map(item => item.label);
        const project_count_per_status_table_values = data_project_count_per_status_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_count_per_status_table_report, {
            type: 'bar',
            data: {
            labels: project_count_per_status_table_labels,
            datasets: [{
                label: '# of Total Projects',
                data: project_count_per_status_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: true,
                  suggestedMin: 0,   // Ensure small values are visible
                 // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total Project per Status  */


      /* Total Project per month */
        const ctx_data_project_count_per_month_table_report = document.getElementById('data_project_count_per_month_table_report');

        const data_project_count_per_month_table = @json($data_project_count_per_month_table );
        const project_count_per_month_table_labels = data_project_count_per_month_table.map(item => item.label);
        const project_count_per_month_table_values = data_project_count_per_month_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_count_per_month_table_report, {
            type: 'bar',
            data: {
            labels: project_count_per_month_table_labels,
            datasets: [{
                label: '# of Total Projects per Month',
                data: project_count_per_month_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: false,
                  suggestedMin: 0,   // Ensure small values are visible
                  // max: Math.max(...project_count_per_month_table_values) // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total Project per month */
 
      /* Average Project Approval Time  */
        const ctx_data_project_average_approval_time_table_report = document.getElementById('data_project_average_approval_time_table_report');

        const data_project_average_approval_time_table = @json($data_project_average_approval_time_table );
        const project_average_approval_time_table_labels = data_project_average_approval_time_table.map(item => item.label);
        const project_average_approval_time_table_values = data_project_average_approval_time_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_average_approval_time_table_report, {
            type: 'line',
            data: {
            labels: project_average_approval_time_table_labels,
            datasets: [{
                label: 'Average Project Approval time in Hours',
                data: project_average_approval_time_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {


              animations: {
                tension: {
                  duration: 1000,
                  easing: 'linear',
                  from: 1,
                  to: 0,
                  loop: false
                }
              },

              scales: {
                  y: {
                    beginAtZero: true, 
                    suggestedMin: 0,   // Ensure small values are visible
                   // Adjust based on your range
                    ticks: {
                        stepSize: 5.0005, // Set a step size suitable for small values
                        callback: function(value) {
                            return value.toFixed(5); // Display full decimal precision
                        }
                    }



                  }
              }
            }
        });
      /* ./ Average Project Approval Time  */


      /* Average Project Response Time  */
        const ctx_data_project_average_response_time_table_report = document.getElementById('data_project_average_response_time_table_report');

        const data_project_average_response_time_table = @json($data_project_average_response_time_table );
        const project_average_response_time_table_labels = data_project_average_response_time_table.map(item => item.label);
        const project_average_response_time_table_values = data_project_average_response_time_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_average_response_time_table_report, {
            type: 'line',
            data: {
            labels: project_average_response_time_table_labels,
            datasets: [{
                label: 'Average Project Response time in Hours',
                data: project_average_response_time_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {


              animations: {
                tension: {
                  duration: 1000,
                  easing: 'linear',
                  from: 1,
                  to: 0,
                  loop: false
                }
              },

              scales: {
                  y: {
                    beginAtZero: true, 
                    suggestedMin: 0,   // Ensure small values are visible
                   // Adjust based on your range
                    ticks: {
                        stepSize: 5.0005, // Set a step size suitable for small values
                        callback: function(value) {
                            return value.toFixed(5); // Display full decimal precision
                        }
                    }



                  }
              }
            }
        });
      /* ./ Average Project Response Time  */

      /* Average Project Review Time  */
        const ctx_data_project_average_review_time_table_report = document.getElementById('data_project_average_review_time_table_report');

        const data_project_average_review_time_table = @json($data_project_average_review_time_table );
        const project_average_review_time_table_labels = data_project_average_review_time_table.map(item => item.label);
        const project_average_review_time_table_values = data_project_average_review_time_table.map(item => item.value); 

            // console.log(data);

        new Chart(ctx_data_project_average_review_time_table_report, {
            type: 'line',
            data: {
            labels: project_average_review_time_table_labels,
            datasets: [{
                label: 'Average Project Review time in Hours',
                data: project_average_review_time_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {

              responsive: true,
              maintainAspectRatio: false,
              animations: {
                tension: {
                  duration: 1000,
                  easing: 'linear',
                  from: 1,
                  to: 0,
                  loop: false
                }
              },

              scales: {
                  y: {
                    beginAtZero: true, 
                    suggestedMin: 0,   // Ensure small values are visible
                     
                    ticks: {
                      // autoSkip: false, // Ensure all values are shown

                        stepSize: 5.0005, // Set a step size suitable for small values
                        callback: function(value) {
                            return value.toFixed(5); // Display full decimal precision
                        }
                    }



                  }
              }
            }
        });
      /* ./ Average Project Review Time  */
 
    </script>
    <!-- ./ Project Report  -->


    <script>
        // Keep chart instances so we can destroy them before re-creating
        window.__dashCharts = window.__dashCharts || {};

        // Common options for bars
        const barOptions = {
            scales: {
            y: {
                beginAtZero: true,
                suggestedMin: 0,
                ticks: {
                stepSize: 5.0005,
                callback: (v) => Number(v).toFixed(5),
                },
            },
            },
        };

        // Common options for lines
        const lineOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animations: {
            tension: { duration: 1000, easing: 'linear', from: 1, to: 0, loop: false },
            },
            scales: {
            y: {
                beginAtZero: true,
                suggestedMin: 0,
                ticks: {
                stepSize: 5.0005,
                callback: (v) => Number(v).toFixed(5),
                },
            },
            },
        };

        function makeChart(id, type, labels, values, datasetLabel, opts = {}) {
            const canvas = document.getElementById(id);
            if (!canvas) return;

            // Destroy previous instance if any
            if (window.__dashCharts[id]) {
            window.__dashCharts[id].destroy();
            delete window.__dashCharts[id];
            }

            const cfg = {
            type,
            data: {
                labels,
                datasets: [{
                label: datasetLabel,
                data: values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9',
                borderColor: 'blue',
                }],
            },
            options: opts,
            };

            // Create & store
            window.__dashCharts[id] = new Chart(canvas, cfg);
        }

        // One place to (re)build all charts on the page
        function initReports() {
            // --- USER REPORTS ---
            const data_user_count_table = @json($data_user_count_table);
            makeChart(
            'data_user_count_table_report',
            'bar',
            data_user_count_table.map(i => i.label),
            data_user_count_table.map(i => i.value),
            '# of Total Users',
            barOptions
            );

            const data_user_registered_count_table = @json($data_user_registered_count_table);
            makeChart(
            'data_user_registered_count_table_report',
            'bar',
            data_user_registered_count_table.map(i => i.label),
            data_user_registered_count_table.map(i => i.value),
            '# of Total Registered Users',
            barOptions
            );

            // --- PROJECT REPORTS ---
            const data_project_count_per_status_table = @json($data_project_count_per_status_table);
            makeChart(
            'data_project_count_per_status_table_report',
            'bar',
            data_project_count_per_status_table.map(i => i.label),
            data_project_count_per_status_table.map(i => i.value),
            '# of Total Projects',
            barOptions
            );

            const data_project_count_per_month_table = @json($data_project_count_per_month_table);
            // custom bar option (beginAtZero: false for this one)
            const monthBarOptions = JSON.parse(JSON.stringify(barOptions));
            monthBarOptions.scales.y.beginAtZero = false;
            makeChart(
            'data_project_count_per_month_table_report',
            'bar',
            data_project_count_per_month_table.map(i => i.label),
            data_project_count_per_month_table.map(i => i.value),
            '# of Total Projects per Month',
            monthBarOptions
            );

            const data_project_average_approval_time_table = @json($data_project_average_approval_time_table);
            makeChart(
            'data_project_average_approval_time_table_report',
            'line',
            data_project_average_approval_time_table.map(i => i.label),
            data_project_average_approval_time_table.map(i => i.value),
            'Average Project Approval time in Hours',
            lineOptions
            );

            const data_project_average_response_time_table = @json($data_project_average_response_time_table);
            makeChart(
            'data_project_average_response_time_table_report',
            'line',
            data_project_average_response_time_table.map(i => i.label),
            data_project_average_response_time_table.map(i => i.value),
            'Average Project Response time in Hours',
            lineOptions
            );

            const data_project_average_review_time_table = @json($data_project_average_review_time_table);
            makeChart(
            'data_project_average_review_time_table_report',
            'line',
            data_project_average_review_time_table.map(i => i.label),
            data_project_average_review_time_table.map(i => i.value),
            'Average Project Review time in Hours',
            lineOptions
            );
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            // wait a tick to ensure canvases exist (esp. with Livewire/Volt)
            requestAnimationFrame(initReports);
        });

        // Re-run after Livewire SPA navigation
        document.addEventListener('livewire:navigated', () => {
            // Give Livewire a microtask to finish morphing the DOM
            requestAnimationFrame(initReports);
        });
    </script>


    @endif


</div>
<!-- End Card Section -->
 
