<?php

namespace App\View\Components\Ui\Project\PageHeader;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProjectReferenceBox extends Component
{
    public $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.project.page-header.project-reference-box');
    }
}
