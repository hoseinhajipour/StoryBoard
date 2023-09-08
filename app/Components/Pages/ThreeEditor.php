<?php

namespace App\Components\Pages;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class ThreeEditor extends Component
{
    public function route()
    {
        return Route::get('/three-editor')
            ->name('ThreeEditor');
    }
    public function render()
    {
        return view('pages.three-editor');
    }
}
