<?php

namespace App\Http\Controllers;

use App\Models\ProjectAttachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

    public function ftpDownload($id)
    {
        if (Auth::check()) {
            $attachment = ProjectAttachments::findOrFail($id);

            $folder = "project_{$attachment->project->id}/project_document_{$attachment->project_document->id}_{$attachment->project_document->document_type->name}/{$attachment->created_at}";

            if (empty($folder)) {
                abort(404, 'File not found on FTP.');
            }

            $ftpPath = 'uploads/project_attachments/'.$folder.'/' . $attachment->attachment;
            $fileName = $attachment->attachment;

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
        return redirect()->back();
    }


}
