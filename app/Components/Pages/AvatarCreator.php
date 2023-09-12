<?php

namespace App\Components\Pages;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class AvatarCreator extends Component
{
    public function route()
    {
        return Route::get('/avatar-creator')
            ->name('AvatarCreator');
    }
    public function render()
    {
        return view('pages.avatar-creator');
    }
}
