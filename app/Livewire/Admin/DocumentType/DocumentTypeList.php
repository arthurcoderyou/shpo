<?php

namespace App\Livewire\Admin\DocumentType;

use App\Models\User;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\DocumentType;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Events\DocumentType\DocumentTypeLogEvent;
use App\Helpers\ModelEventHelpers\ModelEventHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\ActivityLogHelpers\DocumentTypeLogHelper;
use App\Helpers\SystemNotificationHelpers\DocumentTypeNotificationHelper;

class DocumentTypeList extends Component
{
 
    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;
 
    public $name;




    protected $listeners = [
        // 'documentTypeCreated' => '$refresh',
        // 'documentTypeUpdated' => '$refresh',
        // 'documentTypeDeleted' => '$refresh',
        'documentTypeEvent'  => 'loadData',
    ];



    /**
     * 
     * @var array options
     */
    public array $assigned = [];
 
 



    public function mount(){
        
        // default sorting 
        $this->sort_by = 'Order 1 - 100';

        // dd($this->lastOrder);

        // $this->resetOrder();

          
        
        // dd($this->assigned);

        $this->loadData();


    }

    // load default data 
    public function loadData(){
        
        $this->assigned =  DocumentType::orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'order', 'updated_at'])
            ->map(function ($doc, $i) {
                return [
                    'row_uid'      => (string) Str::uuid(),   // unique slot id
                    'id'         => $doc->id,
                    'name'       => $doc->name,
                    'order'      => $doc->order ?? ($i + 1),
                    'document_count' => count($doc->project_documents) ?? 0,
                    'updated_at' => optional($doc->updated_at)->toDateTimeString(),
                ];
            })
            ->values()
            ->toArray();

    }



    /** Reorder rows for a given type by array of row_uids */
    public function reorder(array $rowUids): void
    { 
        $rowUids = array_values(array_unique(array_map('strval', $rowUids)));

        $map = collect($this->assigned ?? [])->keyBy('row_uid');
        $ordered = [];
        $i = 1;

        foreach ($rowUids as $uid) {
            if ($map->has($uid)) {
                $row = $map[$uid];
                $row['order'] = $i++;
                $ordered[] = $row;
            }
        }

        // keep any rows not present in $rowUids at the end (safety)
        foreach ($map as $uid => $row) {
            if (!in_array($uid, $rowUids, true)) {
                $row['order'] = $i++;
                $ordered[] = $row;
            }
        }

        $this->assigned = $ordered;
    }
    

    /** Add selected users N times (N = repeatByType[currentType]) */
    public function add(): void
    {   
        $name = $this->name;
        $this->validate([   
            'name' => 'required', 
        ]);
 
        if (!$name) return;
        $i = count($this->assigned) + 1;
 
 
        $newRow[] = [ 
            'row_uid'      => (string) Str::uuid(),   // unique slot id
            'id'         => null,
            'name'       => $this->name,
            'order'      => $i,
            'document_count' => 0,
            'updated_at' => optional(now())->toDateTimeString(),  
        ];
    

        // If nothing valid was built, stop
        if (empty($newRow)) {
            return;
        }

         
        $assigned = $this->assigned;
        $this->assigned = array_merge($assigned, $newRow);

        $this->name             = null;
        $this->period_value            = 0; 

    }

    /**Update the row value */
    public function update_row($rowUid, $updatedName){
    
              
        $map = collect($this->assigned ?? [])->keyBy('row_uid');
        $updated = [];
         

        foreach ($map as $uid => $row) {

            if ($map->has($uid) && $row['row_uid'] == $rowUid ) {
                $row = $map[$rowUid];
                $row['name'] = $updatedName;  
                $updated[] = $row;
            }else{

                $updated[] = $row;
            }
 
        }

         
        $this->assigned = $updated;
    }   

    public function remove(string $rowUid){

         // 1. Look at the assigned list for this typeId
        // 2. Keep only the rows where row_uid is NOT equal to the one we want to remove
        // 3. Reindex the array so keys are 0,1,2... again
        $this->assigned = array_values(array_filter(
            $this->assigned ?? [],
            fn ($r) => $r['row_uid'] !== $rowUid
        ));

         // Recalculate or normalize the "order" field after removal
        $this->reindexOrder();

    }

    protected function reindexOrder(): void
    {
        $order = 1;
        foreach ($this->assigned as &$row) {
            $row['order'] = $order++;
        }
        unset($row);
    }


    public function save()
    {
        // get the id of authenticated user 
        $authId = Auth::check() ? Auth::id() : null;

        // Check if at least one user has the "system access admin" permission
        $hasAdmin = User::whereHas('permissions', function ($q) {
            $q->whereIn('name', [
                'system access global admin',
                'system access admin',
            ]);
        })->get();

        // check if there are no admins in the system
        if (! $hasAdmin) {
            $message = DocumentTypeLogHelper::getActivityMessage('admin-missing-error',null,$authId);


            return redirect()->route('user.index')->with('alert.error', 
                $message
            );
        }




        // test 
        // check haab hear
        // $document_type = DocumentType::where('name','HAAB/HAER')->first();

        // dd($document_type->project_documents);



        $assigned = $this->assigned;

 

        // 1) IDs that the user kept (existing records only)
        $keptIds = collect($assigned)
            ->pluck('id')
            ->filter()       // remove null/empty
            ->values()
            ->all();

        // 2) IDs currently in the database
        $currentIds = DocumentType::pluck('id')->all();

        // 3) IDs that would be deleted (present in DB, but not in the new list)
        $deletedIds = array_diff($currentIds, $keptIds);

        // 4) If any of the would-be-deleted types have connected project_documents, stop
        if (!empty($deletedIds)) {
            $hasLocked = DocumentType::whereIn('id', $deletedIds)
                ->whereHas('project_documents')   // only those with related projects
                ->exists();

            if ($hasLocked) {

                $message = DocumentTypeLogHelper::getActivityMessage('document-type-locked',null,$authId);
                $route = DocumentTypeLogHelper::getRoute('document-type-locked' );

                return redirect($route)
                    // ->route('document_type.index')
                    ->with('alert.error', $message);
            }
        }




        // dd("ALl good");

        DB::transaction(function () use ($assigned) {

            // get the id of authenticated user 
            $authId = Auth::check() ? Auth::id() : null;
            
            $keptIds = [];

            foreach ($assigned as $index => $item) {
                $name = trim($item['name'] ?? '');

                // Skip blank rows
                if ($name === '') {
                    continue;
                }

                $data = [
                    'name'  => $name,
                    'order' => $index + 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,

                ];

                if (!empty($item['id'])) {
                    $doc = DocumentType::find($item['id']);
                    if ($doc) {

                        /** Detect if there are changes */
                            $model_columns_keys_to_check = ['name', 'order'];

                            $updated_data = $data;

                            $result = ModelEventHelper::detect_model_changes(
                                \App\Models\DocumentType::class,
                                $doc->id,
                                $model_columns_keys_to_check,
                                $updated_data
                            );
                            // dd($result);

                            // save to database
                            $doc->update($data);
                            $keptIds[] = $doc->id;


                            // check if the changed status is true || meaing there is a change detected 
                            if ($result['changed']) {
                                // there are changes

                                // log activity if there are changes
                                $docTypeId = $doc->id; 
                                DocumentTypeLogHelper::logActivity('updated' ,$docTypeId, $authId);

                            }  
                        /** ./ Detect if there are changes */

                        
                        


 

                    }
                } else {

                    // save to database
                    $doc = DocumentType::create($data);
                    $keptIds[] = $doc->id;


                    // log activity
                    $docTypeId = $doc->id; 
                    DocumentTypeLogHelper::logActivity('created' ,$docTypeId, $authId);

                }
            }

            // Delete removed rows
            if (!empty($keptIds)) {
                $documentTypesToDelete = DocumentType::whereNotIn('id', $keptIds)->get();

                foreach($documentTypesToDelete as $docToDelete){

                    // log activity
                    $docTypeId = $docToDelete->id;
                    DocumentTypeLogHelper::logActivity('deleted' ,$docToDelete->id, $authId);

                    $docToDelete->delete();
                }



            } else {
                DocumentType::query()->delete();
            }
        });

        // Reload fresh data (updated_at, etc.)
        $this->mount();




        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = DocumentTypeLogHelper::getActivityMessage('list-updated' ,null, $authId);

            // get the route
            $route = DocumentTypeLogHelper::getRoute('list-updated' );

            // log the event 
            event(new DocumentTypeLogEvent(
                $message ,
                $authId, 

            ));
    
            /** send system notifications to users */
                
                DocumentTypeNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications



 

        return
            //  redirect()->route('document_type.index') 
            redirect($route)
                ->with('alert.success',$message);
    }











    public function getLastOrderProperty(){
        return DocumentType::max('order') ?? 0;
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
            $this->selected_records = DocumentType::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }
 

    public function delete($id){
        $document_type = DocumentType::find($id);


        // dd($document_type->reviewers()->exists());
        if(!Auth::user()->can('system access global admin')){
            // Check if document type has related records
            if ($document_type->project_documents()->exists() || $document_type->reviewers()->exists()) {
                Alert::error('Error', 'Cannot delete document type because it has related records such as projects and reviewers. ');
                return redirect()->route('document_type.index');
            }
        }
        
 
        $document_type->delete();
        $this->resetOrder();
 
        // ActivityLog::create([
        //     'log_action' => "Global Project reviewer '".$document_type->name."' on list deleted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // Alert::success('Success','Document type deleted successfully');
        // return redirect()->route('document_type.index');

    }
 
 


    public function render()
    {

         

        


        return view('livewire.admin.document-type.document-type-list',[ 
        ]);
    }
    // public function render()
    // {
    //     return view('livewire.admin.document-type.document-type-list');
    // }
}
