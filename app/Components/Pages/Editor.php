<?php

namespace App\Components\Pages;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Editor extends Component
{
    public $project;
    public function mount(Request $request)
    {
        if ($request->id) {
            $this->project = Project::find($request->id);
        }
    }

    public function route()
    {
        return Route::get('/editor')
            ->name('editor');
    }

    public function render()
    {
        return view('pages.editor')->layout("layouts.app_editor");
    }
}
