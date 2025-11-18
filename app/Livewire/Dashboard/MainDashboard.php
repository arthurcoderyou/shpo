<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MainDashboard extends Component
{

     protected $listeners = [
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'reviewerCreated' => '$refresh',
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
        'roleCreated' => '$refresh',
        'roleUpdated' => '$refresh',
        'roleDeleted' => '$refresh',
        'userCreated' => '$refresh',
        'userUpdated' => '$refresh',
        'userDeleted' => '$refresh',
    ];


    // Metrics table data
        //data of the users count
        public $data_user_count_table = []; 

        //data of registered users count 
        public $data_user_registered_count_table = [];

        // data of project count per status
        public $data_project_count_per_status_table = [];

        // data of project count per month
        public $data_project_count_per_month_table = [];

        // data of project average approval time
        public $data_project_average_approval_time_table = [];

        // data of project average response time
        public $data_project_average_response_time_table = [];

        // data of project average review time
        public $data_project_average_review_time_table = [];


    // end of Metrics table data

    public function mount(){

        // Users Table Report
            /** Count of Users per role */
                $this->data_user_count_table = 
                [
                    
                    [
                        'label' => 'All Users',
                        'value' => User::count(),
                    ],
                    [
                        'label' => 'Admin',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'Admin')
                                    ->count(),
                    ],
                    [
                        'label' => 'Reviewer',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'Reviewer')
                                    ->count(),
                    ],
                    [
                        'label' => 'User',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'User')
                                    ->count(),
                    ],
                    [
                        'label' => 'No Role',
                        'value' => User::leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->whereNull('model_has_roles.model_id')
                                    ->count(),
                    ],
                ];
            /** ./ Count of Users per role */

            /** Count of Registered Users */ 
                $this->data_user_registered_count_table = 
                [
                    
                    [
                        'label' => 'This Year',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'User')
                                    ->whereYear('users.created_at', Carbon::now()->year)
                                    ->count(),
                    ],
                    [
                        'label' => 'This Month',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'User')
                                    ->whereYear('users.created_at', Carbon::now()->year)
                                    ->whereMonth('users.created_at', Carbon::now()->month)
                                    ->count(),
                    ],
                    [
                        'label' => 'This Week',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'User')
                                    ->whereBetween('users.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                    ->count(),
                    ],
                    [
                        'label' => 'Today',
                        'value' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                    ->where('roles.name', 'User')
                                    ->whereDate('users.created_at', Carbon::now()->toDateString())
                                    ->count(),
                    ],
                ];
            /** ./ Count of Registered Users */ 
 


        // ./ end of Users Table Report
 

        // Projects Table Report
            /** Count of Projects per Status */
                $this->data_project_count_per_status_table = 
                [
                    
                    [
                        'label' => 'All Projects',
                        'value' => Project::count(),
                    ],
                    [
                        'label' => 'Drafts',
                        'value' => Project::where('status', 'draft')
                                    ->count(),
                    ],
                    [
                        'label' => 'In Review',
                        'value' => Project::where('status', 'in_review')
                                    ->count(),
                    ],
                    [
                        'label' => 'Approved',
                        'value' => Project::where('status', 'approved')
                                    ->count(),
                    ], 
                ];
            /** ./ Count of Projects per Status  */   


            /** Count of Projects on each month , with this month as teh last */
                $startDate = Carbon::now()->subMonths(11)->startOfMonth(); // 12 months ago
                $endDate = Carbon::now()->endOfMonth(); // Current month
                
                $projects = Project::selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(id) as count")
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->keyBy(fn ($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT)); // Key by Year-Month
                
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $yearMonth = $date->format('Y-m'); // Format: 2024-03
                
                    $this->data_project_count_per_month_table[] = [
                        'label' => $date->format('F Y'), // Month name
                        'value' => $projects[$yearMonth]->count ?? 0, // Get count or default to 0
                    ];
                }

                // dd($this->data_project_count_per_month_table);

            /** Count of Projects on each month , with this month as teh last./ */


            /** Average Approval time */
                $this->data_project_average_approval_time_table = 
                [
                    [
                        'label' => 'Today',
                        'value' => Project::getAverageApprovalTime('today'),
                    ],

                    [
                        'label' => 'This Week',
                        'value' => Project::getAverageApprovalTime('this_week'),
                    ],

                    [
                        'label' => 'This Month',
                        'value' => Project::getAverageApprovalTime('this_month'),
                    ],

                    [
                        'label' => 'This Year',
                        'value' => Project::getAverageApprovalTime('this_year'),
                    ],

                    [
                        'label' => 'All Time',
                        'value' => Project::getAverageApprovalTime(),
                    ],
                    
                ];
            /** ./ Average Project Approval time */



            /** Average Project Response time */
                $this->data_project_average_response_time_table = 
                [
                    [
                        'label' => 'Today',
                        'value' => Review::getAverageResponseTime('today'),
                    ],

                    [
                        'label' => 'This Week',
                        'value' => Review::getAverageResponseTime('this_week'),
                    ],

                    [
                        'label' => 'This Month',
                        'value' => Review::getAverageResponseTime('this_month'),
                    ],

                    [
                        'label' => 'This Year',
                        'value' => Review::getAverageResponseTime('this_year'),
                    ],

                    [
                        'label' => 'All Time',
                        'value' => Review::getAverageResponseTime(),
                    ],
                    
                ];
            /** ./ Average Project Response time */

            /** Average Project Review time */
                $this->data_project_average_review_time_table = 
                [
                    [
                        'label' => 'Today',
                        'value' => Review::getAverageReviewTime('today'),
                    ],

                    [
                        'label' => 'This Week',
                        'value' => Review::getAverageReviewTime('this_week'),
                    ],

                    [
                        'label' => 'This Month',
                        'value' => Review::getAverageReviewTime('this_month'),
                    ],

                    [
                        'label' => 'This Year',
                        'value' => Review::getAverageReviewTime('this_year'),
                    ],

                    [
                        'label' => 'All Time',
                        'value' => Review::getAverageReviewTime(),
                    ],
                    
                ];
            /** ./ Average Project Review time */

            

        // ./ Projects Table Report

    }
    
    public function render()
    {   





  
        // set the iconBg and iconColor based on user access
        $user = Auth::user();

        $accessColors = [
            'system access user' => ['bg' => 'bg-blue-900', 'text' => 'text-blue-100'],
            'system access reviewer' => ['bg' => 'bg-slate-900', 'text' => 'text-white'],
            'system access admin' => ['bg' => 'bg-purple-900', 'text' => 'text-purple-100'],
            'system access global admin' => ['bg' => 'bg-green-900', 'text' => 'text-green-100'],
        ];

        // default fallback
        $iconBg = 'bg-gray-900';
        $iconColor = 'text-gray-100';

        foreach ($accessColors as $permission => $colors) {
            if ($user->can($permission)) {
                $iconBg = $colors['bg'];
                $iconColor = $colors['text'];
                break; // stop at the first matching role
            }
        }


        

        return view('livewire.dashboard.main-dashboard',[ 
            'iconBg' => $iconBg,
            'iconColor' => $iconColor,
        ]);
    }
}
