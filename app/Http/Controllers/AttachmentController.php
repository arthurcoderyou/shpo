<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{

    /*
    public function ftpDownload($id)
    {

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('project download attachment');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification

        if(
            // Auth::user()->hasRole('Global Administrator') 
            // || Auth::user()->can('funeral schedule download attachments') 
            Auth::check()

            ){
            $attachment = ProjectAttachments::findOrFail($id);

            // $folder = $attachment->funeral_schedule->folder;

                

            // Destination path on FTP
            $folder = "project_{$attachment->project->id}/project_document_{$attachment->project_document->id}_{$attachment->project_document->document_type->name}/{$attachment->created_at}";
                // dd($folder);

            if (empty($folder)) {
                abort(404, 'File not found on FTP.');
            }
 

            $ftpPath = 'uploads/project_attachments/'.$folder.'/' . $attachment->attachment;
            $fileName = $attachment->attachment;

            if (!Storage::disk('ftp')->exists($ftpPath)) {
                abort(404, 'File not found on FTP.');
            }

            $fileContents = Storage::disk('ftp')->get($ftpPath);

            return response($fileContents)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        }else{

            
            // For error message
            session()->flash('alert.error', 'You do not have permission to download this file'); 
            return redirect()->back();

        }


        
    }
    */


    // index
    public function index(){
        

        return view('admin.file_manager.attachment.index');
    }

    // create
    public function create(){
        @set_time_limit(0);
        ini_set('max_execution_time', '0');
        ini_set('max_input_time', '0');
         

        return view('admin.file_manager.attachment.create');
    }

    // edit
    public function edit($id){

        

        // $permission = Permission::findOrFail($id);
        // return view('admin.permission.edit',compact('permission'));
    }







    public function ftpDownload($id)
    {

        $attachment = ProjectAttachments::findOrFail($id);

        if (Auth::check()) {
           

            // $folder = "project_{$attachment->project->id}/project_document_{$attachment->project_document->id}_{$attachment->project_document->document_type->name}/{$attachment->created_at}";

            $folder = "project_{$attachment->project->id}/project_document_{$attachment->project_document->id}/{$attachment->created_at}";

            if (empty($folder)) {
                abort(404, 'File not found on FTP.');
            }


            // dd($folder ." + ".$attachment->id);


            $attachment_name = $attachment->stored_name ?? $attachment->attachment;

            $ftpPath = 'uploads/project_attachments/'.$folder.'/' . $attachment_name;

            // dd($attachment->created_at);

            // $ftpPath = "uploads/project_attachments/project_227/project_document_217/2025-10-09 05:30:10/1759951818-johncathedral.jpg"; // to check if the record is working, try uncommenting this 




            $fileName = $attachment->attachment;


            // dd($ftpPath);


            try {
                // Check if file exists on FTP
                if (!Storage::disk('ftp')->exists($ftpPath)) {
                    abort(404, 'File not found on FTP.');
                }

                // Attempt to retrieve the file
                $fileContents = Storage::disk('ftp')->get($ftpPath);

                return response($fileContents)
                    ->header('Content-Type', 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

            } catch (\Exception $e) {
                \Log::error('FTP download error: '.$e->getMessage());

                return redirect()->back()->with('alert.error', 'Failed to connect to FTP server. Please try again later or contact the administrator.');
            }
        }

        session()->flash('alert.error', 'You do not have permission to download this file');
        return redirect()->route('project.project-document.show',[
            'project' => $attachment->project_id,
            'project_document' => $attachment->project_document_id,
        ]);
    }



    public function download($id, Request $request)
    {
        if (!Auth::check()) {
            return back()->with('alert.error', 'You do not have permission to download this file');
        }

        $attachment = ProjectAttachments::with(['project', 'project_document'])->findOrFail($id);

        // --- Build the expected FTP path ---
        // TIP: created_at often contains colons/spaces; make sure you format it
        // to the exact folder naming you used during upload.
        // Example format; change if your FTP folder uses something else:
        // $createdFolder = Carbon::parse($attachment->created_at)->format('Y-m-d_His');
        $createdFolder = $attachment->created_at;

        // If during upload you used a different structure, mirror it here:
        // project_{project_id}/project_document_{project_document_id}/{created_at_formatted}
        $folder = "project_{$attachment->project->id}/".
                  "project_document_{$attachment->project_document->id}/".
                  "{$createdFolder}";

        $relative = 'uploads/project_attachments/'.$folder.'/'.$attachment->attachment;


        // dd($relative);



        $disk = Storage::disk('ftp');

        if (!$disk->exists($relative)) {
            // Fallbacks (optional): try without the created_at folder or raw created_at
            $fallbacks = [
                'uploads/project_attachments/'.
                    "project_{$attachment->project->id}/".
                    "project_document_{$attachment->project_document->id}/".
                    $attachment->attachment,
                'uploads/project_attachments/'.
                    "project_{$attachment->project->id}/".
                    "project_document_{$attachment->project_document->id}/".
                    "{$attachment->created_at}/".
                    $attachment->attachment,
            ];

            $found = null;
            foreach ($fallbacks as $try) {
                if ($disk->exists($try)) { $found = $try; break; }
            }

            if (!$found) {
                return back()->with('alert.error', 'File not found on FTP (path mismatch).');
            }

            $relative = $found;
        }

        // Guess mime by filename (FTP adapters often can’t read content-type)
        $mime = (new MimeTypes())->guessMimeType($attachment->attachment) ?? 'application/octet-stream';

        // Stream out the FTP file as a download
        return response()->streamDownload(function () use ($disk, $relative) {
            $stream = $disk->readStream($relative);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, $attachment->attachment, [
            'Content-Type'        => $mime,
            'Cache-Control'       => 'private, max-age=0, no-cache, no-store',
            'Pragma'              => 'no-cache',
            // NOTE: streamDownload sets Content-Disposition: attachment; filename=...
        ]);
    }



}
