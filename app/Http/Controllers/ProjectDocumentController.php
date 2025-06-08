<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\ProjectDocument;

class ProjectDocumentController extends Controller
{
    public function index($project_id, $project_document_id){
        $project = Project::findOrFail($project_id);
        $project_document = ProjectDocument::findOrFail($project_document_id);
        
        return view('admin.project_document.index',compact('project','project_document'));
        
    }
}
