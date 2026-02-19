<?php

namespace App\View\Components\Ui\Project\PageHeader;

use App\Models\ProjectDocument;
use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProjectDiscussionBox extends Component
{
    public $project;
    public ?ProjectDocument $project_document;

    public function __construct($project, $project_document = null)
    {
        $this->project = $project;

        $this->project_document = $project_document;
    }
 

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.project.page-header.project-discussion-box');
    }
}
