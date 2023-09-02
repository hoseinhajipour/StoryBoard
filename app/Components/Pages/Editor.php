<?php

namespace App\Components\Pages;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Editor extends Component
{
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
