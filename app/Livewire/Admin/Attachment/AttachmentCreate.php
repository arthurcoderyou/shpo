<?php

namespace App\Livewire\Admin\Attachment;

use Livewire\Component;
use App\Models\Attachments;
use App\Events\Attachment\Copied;
use App\Helpers\AttachmentHelper;
use App\Events\Attachment\Created;
use App\Helpers\FileCategoryHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use App\Helpers\ProjectDocumentHelpers;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\LivewireFilepond\WithFilePond;

class AttachmentCreate extends Component
{
    use WithFilePond;


    public $record_count = 10;
    public $count = 0;

    
    #[Validate('image|video|max:1024')]
    public $file;


    #[Validate('image|max:1024')]
    public $photo;


    #[Validate(['photos.*' => 'image|max:1024'])]
    public $photos = [];


    // #[Validate(['files.*' => 'image|max:1024'])]
    public $files = [];


    public function rules(): array
    {
        return [
             
            'files.*' => File::types([
                    'jpg','jpeg','png',
                    'pdf', 'xlsx',
                    'mp3', 'wav',
                    'mp4'
                    ])
                    ->min('1kb')
                    ->max('10mb'),



        ];
    }
 

    public function validateUploadedFile(){

        $this->validate();

        $user = Auth::user(); 

        // $this->dispatch("attachment-created"); 

        try {
            foreach($this->files as $file){
 
                $filename = $file->getClientOriginalName(); 

                // Core facts
                $originalName = $file->getClientOriginalName();
                $ext          = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                $mime         = $file->getMimeType();        // server-inferred, more trustworthy than ext
                $size         = $file->getSize() ?? 0;       // bytes
                $tmpPath      = $file->getRealPath();        // temp file for hashing/dimensions

                // Category
                // $category     = FileCategoryHelper::categorize($mime, $ext);

                 // Unique stored name (timestamp + uuid + ext)
                // $storedName   = now()->format('Ymd_His') . '-' . Str::uuid() . ($ext ? ".{$ext}" : '');
                $dir          = 'files_'.$user->id;
                $disk         = 'ftp';


                $path = $now = now();
                $to   = "uploads/project_attachments/project_240/project_document_238/{$now}/.$originalName ";

                // Store the file in the "photos" directory of the default filesystem disk
                $file->storeAs(path: 'files_'.$user->id,name: $filename,options: 'ftp'); 


                $from = 'files_'.$user->id."/".$filename;

                // check if the file is completely uploaded on the ftp server
                if (Storage::disk('ftp')->exists($from)) { 


                    $attachment = new Attachments();
                    $attachment->attachment = $filename;
    
                    $attachment->original_name = $originalName;
                    // $attachment->stored_name   = $storedName;
                    $attachment->disk          = $disk;
                    $attachment->path          = $dir; 

                    // $attachment->category      = $category;
                    $attachment->mime_type     = $mime;
                    $attachment->extension     = $ext;
                    $attachment->size_bytes    = $size;



                    // Optional: SHA-256 for dedupe/integrity   
                    $sha256 = null;
                    if (is_readable($tmpPath)) {
                        $sha256 = hash_file('sha256', $tmpPath);
                    }

                    // Optional: image dimensions
                    $width = $height = null;
                    if (str_starts_with(strtolower($mime ?? ''), 'image/')) {
                        try {
                            [$width, $height] = getimagesize($tmpPath) ?: [null, null];
                        } catch (\Throwable $e) {
                            Log::warning("Could not read image dimensions: {$originalName}", ['e' => $e->getMessage()]);
                        }
                    }


                    $attachment->width         = $width;
                    $attachment->height        = $height;
                    $attachment->duration_seconds = null; // fill later if you add FFmpeg/getID3

                    $attachment->sha256        = $sha256;




                    $attachment->created_by = Auth::user()->id;
                    $attachment->updated_by = Auth::user()->id;
                    $attachment->save();

                }

            }
    
            // Success message
            // $this->dispatch('post-created',type: 'success',message: "Files Uploaded!"); 

            
 


            // clear the files 
            $this->files = [];

             
           
        } catch (\Throwable $e) {
            // Log error for debugging
            Log::error('Photo upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Flash user-friendly error message
            // $this->dispatch('post-created',type: 'error',message: 'Error uploading photos: ' . $e->getMessage());  
            return true;
        }


        if(!empty($attachment)){

            
            /**Private Channel */
            try {


            event(new Created($attachment, Auth::user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send Created event: ' . $e->getMessage(), [
                    'authId' => Auth::user()->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            
        }


        return true;

    }


    public function delete($attachment_id){


        $user = Auth::user();

        $attachment = Attachments::find($attachment_id);

        if($attachment->attachment){

            $from = 'files_'.$user->id."/".$attachment->attachment;

            // check if the file is completely uploaded on the ftp server
            if (Storage::disk('ftp')->exists($from)) { 

                // delete the file on the ftp server
                Storage::disk('ftp')->delete("files_".$user->id."/{$attachment->attachment}"); 


                // check if the file does not exist any more 
                if (!Storage::disk('ftp')->exists($from)){
                    // delete the database record 
                    $attachment->delete();
                    $attachment->save();
                }
                

            }

            
        }

 




        

    }
 

    public function copy($attachment_id){
        $user = Auth::user();
 
        $attachment = Attachments::find($attachment_id);
        // dd($attachment);

        if($attachment->attachment){
            $from = 'files_'.$user->id.'/'.$attachment->attachment;
            $to   = 'files_copied_'.$user->id.'/'.$attachment->attachment;
            


            // $to = "uploads";


            
            try { 
                // dd(Storage::disk('ftp')->exists($from));
                if (Storage::disk('ftp')->exists($from)) {
                    Storage::disk('ftp')->copy($from, $to);
                    // dd("ok");
                }



                event(new Copied(Auth::user()->id,"success"));


                Alert::success('Success','Attachment copied successfully');
                return redirect()->route('test.attachment.create');


            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send Copied event: ' . $e->getMessage(), [
                    'authId' => Auth::user()->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }





            

        }


  

        
        return true;


    }


    // test for the events 
    public function save(){

        // Dispatch a browser event
        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => 'Record created successfully!'
        ]);
    }


    public function returnFormattedDatetime($datetime){
        return ProjectDocumentHelpers::returnFormattedDatetime($datetime);

    }

    public function returnFormattedUser($user_id){
        return ProjectDocumentHelpers::returnFormattedUser($user_id);
    }


    public function convertBytes($bytes){
        return AttachmentHelper::convertBytes($bytes,"MB");
    }
        

 
    public function getAttachmentsProperty()
    {

        $user = Auth::user();
        $userId = $user->id;

        // $projectIdsSub = $this->buildProjectBaseQuery();

            // dd($projectIdsSub );


        $attachments = Attachments::query();
            // Only attachments for the filtered projects
            // ->whereIn('project_id', $projectIdsSub)
            // Avoid N+1
            // ->with([
            //     // 'project:id,name,project_number,status,submitter_due_date,reviewer_due_date',
            //     // 'document_type:id,name' // adjust columns to your table
            // ]);

 
        // $attachments  = $this->applyRouteBasedFilters($attachments );
 
 
        $attachments = $attachments->ownedBy($userId);  
 




        // if (!empty($this->uploaded_from) && !empty($this->uploaded_to)) {
        //     $docs->whereBetween('created_at', [$this->uploaded_from, $this->uploaded_to]);
        // }

        // if(!empty($this->sort_by)){
        //     $docs = $docs->applySortingUsingWhereHas($this->sort_by)  // sorrting with aggregate to other model relationships
        //         ->applySorting($this->sort_by); // sorrting directly to the project document model
        // }else{

        //     $cods = $docs->applySorting($this->sort_by);
            
        // }
 
        // Paginate documents
        $paginated = $attachments->orderBy('created_at','DESC')
            ->paginate($this->record_count);
        $this->project_documents_count = $paginated->total();

        return $paginated;
    }



    public function render()
    {
        return view('livewire.admin.attachment.attachment-create',[
            'attachments' => $this->attachments,
        ]);
    }
}
