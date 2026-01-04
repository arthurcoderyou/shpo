<?php

namespace App\Livewire\Notification;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Events\NotificationsUpdated;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class NotificationList extends Component
{   
    protected $listeners = [
        'systemEvent' => '$refresh',

        'notificationsCreated' => '$refresh',
        'notificationsDeleted' => '$refresh',
        'notificationsUpdated' => '$refresh',
        
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'activitylogCreated' => '$refresh',
        'reviewerCreated' => '$refresh', 
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
    ];

    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $sort_by = 'latest';
    public $read_filter = 'all'; // Read/Unread filter

    public $date_filter = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;


    public $unread_count;

    public function mount(){
        $this->unread_count = Auth::user()->notifications()
            ->whereNull('read_at') // Unread notifications
            ->count();
    }

    // Method to delete selected records
    public function deleteSelected()
    {
        Auth::user()->notifications()->whereIn('id', $this->selected_records)->delete(); // Delete the selected records

        Auth::user()->notifications()->whereIn('id', $this->selected_records)->notifications()->delete();


        $this->selected_records = []; // Clear selected records

        // Alert::success('Success','Selected notifications deleted successfully');
        // return redirect()->route('dashboard');
    }

    // Method to markAsReadSelected selected records
    public function markAsReadSelected()
    {
        $notifications = auth()->user()->notifications()->whereIn('id', $this->selected_records)->get(); // update the selected records
        if(!empty($notifications)){
            foreach($notifications as $notification){

                if(empty($notification->read_at)){
                    $notification->read_at = now();
                    $notification->save();


                    // $alreadyNotified = $admin->notifications()
                    //     ->where('type', NewUserRegisteredNotificationDB::class)
                    //     // ->where('notifiable_id', $admin->id)
                    //     ->whereJsonContains('data->user_id', $user->id)
                    //     ->exists();



                }
            }
        }
        

        $this->selected_records = []; // Clear selected records
        $this->count = 0;
        $this->selectAll = false;

        $databaseNotification = auth()->user()->notifications()->whereIn('id', $this->selected_records)->first();

        try {
            // Dispatch your custom event or log it
            event(new NotificationsUpdated($databaseNotification,Auth::user()->id));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch NotificationsUpdated event: ' . $e->getMessage(), [
                'notification_id' => $databaseNotification->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
 
    }


    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    

    public function toggleSelectAll()
    {
        if ($this->selectAll) {


            $notifications = auth()->user()->notifications(); // Paginate 10 per page

            // **Search by Message**
            if (!empty($this->search)) {
                $notifications->whereJsonContains('data->message', $this->search);
            }
            // **Search by Message using LIKE**
            // if (!empty($this->search)) {
            //     $notifications->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.message')) LIKE ?", ["%{$this->search}%"]);
            // }

            // **Date Filter**
            if ($this->date_filter === 'today') {
                $notifications->whereDate('created_at', Carbon::today());
            } elseif ($this->date_filter === 'this_week') {
                $notifications->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($this->date_filter === 'this_month') {
                $notifications->whereMonth('created_at', Carbon::now()->month);
            }


            // **Read/Unread Filter**
            if ($this->read_filter === 'unread') {
                $notifications->whereNull('read_at'); // Unread notifications
            } elseif ($this->read_filter === 'read') {
                $notifications->whereNotNull('read_at'); // Read notifications
            }


            // **Sorting**
            if ($this->sort_by === 'latest') {
                $notifications->orderBy('created_at', 'DESC');
            } elseif ($this->sort_by === 'oldest') {
                $notifications->orderBy('created_at', 'ASC');
            }



            // **Pagination**
            $this->selected_records = $notifications->pluck('id')->toArray();





            // $this->selected_records = auth()->user()->notifications()->pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);

        // dd($this->selected_records);
    }

    public function delete($notificationId){
 

        // Retrieve the notification again
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();


        $notification->delete();     

        // Alert::success('Success','Notification deleted successfully');
        // return redirect()->route('dashboard');

    }

    public function markAsReadandOpen($notificationId)
    {   
        // Retrieve the notification again
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();

        if(empty($notification->read_at)){
            $notification->read_at = now();
            $notification->save();
        }
        // Update the notification as read
        // auth()->user()->notifications()->where('id', $notificationId)->update(['read_at' => now()]);

        

        if ($notification && isset($notification->data['url'])) {
            return redirect()->to($notification->data['url']); // Correct redirection
        }

        Alert::error('Error','Invalid notification or URL not found');
        return redirect()->route('dashboard');
    }


    public function getNotificationsProperty(){
        $notifications = auth()->user()->notifications(); // Paginate 10 per page

        // **Search by Message**
        if (!empty($this->search)) {
            $notifications->whereJsonContains('data->message', $this->search);
        }
        // **Search by Message using LIKE**
        // if (!empty($this->search)) {
        //     $notifications->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.message')) LIKE ?", ["%{$this->search}%"]);
        // }

        // **Date Filter**
        if ($this->date_filter === 'today') {
            $notifications->whereDate('created_at', Carbon::today());
        } elseif ($this->date_filter === 'this_week') {
            $notifications->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->date_filter === 'this_month') {
            $notifications->whereMonth('created_at', Carbon::now()->month);
        }


        // **Read/Unread Filter**
        if ($this->read_filter === 'unread') {
            $notifications->whereNull('read_at'); // Unread notifications
        } elseif ($this->read_filter === 'read') {
            $notifications->whereNotNull('read_at'); // Read notifications
        }


        // **Sorting**
        if ($this->sort_by === 'latest') {
            $notifications->orderBy('created_at', 'DESC');
        } elseif ($this->sort_by === 'oldest') {
            $notifications->orderBy('created_at', 'ASC');
        }



        // **Pagination**
        $notifications = $notifications->paginate($this->record_count);

        return $notifications;
    }


    public function render()
    {
        

        return view('livewire.notification.notification-list', [
            'notifications' => $this->notifications
        ]);
    }




    // public function render()
    // {
    //     return view('livewire.notification.notification-list');
    // }
}
