<?php

namespace App\Components\Pages;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class ProjectsPage extends Component
{
    public $title;

    public function route()
    {
        return Route::get('/projects')
            ->name('projects');
    }


    public function CreateNewProject()
    {
        $newProject = new Project();
        $newProject->title = $this->title;
        $newProject->user_id = Auth::user()->id;
        $newProject->save();
    }

    public function DeleteProject($p_id)
    {
        $project = Project::find($p_id);

        $project->delete();
    }

    public function render()
    {
        $projects = Project::where("user_id", Auth::user()->id)->get();
        return view('pages.projects-page', ["projects" => $projects]);
    }
}
