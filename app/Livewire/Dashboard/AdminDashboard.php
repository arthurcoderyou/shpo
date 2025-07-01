<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use App\Models\Review;
use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{


    protected $listeners = [
        'projectCreated' => '$refresh',
        'projectUpdated' => '$refresh',
        'projectDeleted' => '$refresh',
        'projectSubmitted' => '$refresh',
        'projectQueued' => '$refresh', 
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'reviewerCreated' => '$refresh',
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',

        'projectDocumentCreated' => '$refresh',
        'projectDocumentUpdated' => '$refresh',
        'projectDocumentDeleted' => '$refresh',
    ];

    public int $usersAllTime;
    public int $usersUpdatePending;
    public int $reviewersUpdatePending;
    public int $usersThisWeek;
    public int $usersLastWeek;

    public string $userPercentage;

    public string $userChangeStatus = "none";


    
    public $projects_for_review;
    public $pending_update_projects;

    public $all_projects;
    public $in_review_projects;
    public $approved_projects;

    public $project_reviews;


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

        $this->usersUpdatePending = User::countUsers("no_role","user");

        $this->reviewersUpdatePending = User::countUsers("no_role","reviewer");
        // dd($this->usersUpdatePending);


        $this->usersAllTime = User::countUsers();
        

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $endOfThisWeek = Carbon::now()->endOfWeek();

        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Get user counts
        $this->usersThisWeek = User::whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])->count();
        $this->usersLastWeek = User::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        // $this->calculatePercentageChange();





        // $this->data_user_role_counts = User::leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        //     ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        //     ->select(
        //         DB::raw('COUNT(users.id) as total_users'),
        //         DB::raw('COALESCE(roles.name, "No Role") as role_name')
        //     )
        //     ->groupBy('role_name')
        //     ->get()
        //     ->toArray();

        // $this->data_user_count_tables = User::leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        //     ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        //     ->select(
        //         DB::raw('COALESCE(roles.name, "No Role") as role_name'),
        //         DB::raw('COUNT(users.id) as total_users'),
        //         DB::raw('SUM(CASE WHEN YEAR(users.created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as users_this_year'),
        //         DB::raw('SUM(CASE WHEN MONTH(users.created_at) = MONTH(CURDATE()) AND YEAR(users.created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as users_this_month'),
        //         DB::raw('SUM(CASE WHEN WEEK(users.created_at, 1) = WEEK(CURDATE(), 1) AND YEAR(users.created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as users_this_week')
        //     )
        //     ->groupBy('role_name')
        //     ->get()
        //     ->toArray();

        

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



        // dd($this->data_project_average_response_time_table);


        /**
         * 
         * 
        [
            'user_label' => 'Users this Year'
            'user_count' => $user_count,
        ]
         
        



         * 
         * 
         */



            
        $this->projects_for_review = Project::countProjectsForReview();
        $this->pending_update_projects = Project::countProjectsForUpdate();


        $this->all_projects = Project::countProjects();
        $this->in_review_projects  = Project::countProjects("in_review");
        $this->approved_projects  = Project::countProjects("approved");

        $this->project_reviews = Review::count();


    }

    public function calculatePercentageChange()
    {
        if ( $this->usersLastWeek == 0) {
            $this->userPercentage = $this->usersLastWeek > 0 ? 100 : 0; // 100% increase if new users exist, otherwise 0%
        }

        $percentageChange = (($this->usersLastWeek -  $this->usersLastWeek) /  $this->usersLastWeek) * 100;

        if ($percentageChange > 0) {
            $this->userPercentage =  "+".number_format($percentageChange, 2)."% ";
            $this->userChangeStatus = "up";
        } elseif ($percentageChange < 0) {
            $this->userPercentage =  "-".number_format($percentageChange, 2)."% ";
            $this->userChangeStatus = "down";
        } else {
            // $this->userPercentage =  "No Change (0%)";
            $this->userPercentage = "";
        }
    }



    public function project_count($status){

        return Project::where('status', $status)->count();

    }




    public function render()
    {
        return view('livewire.dashboard.admin-dashboard');
    }
}
