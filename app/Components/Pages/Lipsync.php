<?php

namespace App\Components\Pages;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Lipsync extends Component
{
    public function route()
    {
        return Route::get('/lip-sync')
            ->name('Lipsync');
    }
    public function render()
    {
        return view('pages.lipsync');
    }
}
