<?php

namespace App\Components\Pages;

use App\Jobs\DownloadFileJob;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class AvatarCreator extends Component
{
    protected $listeners = ["uploadFile"];

    public $title = "";

    public function uploadFile($downloadUrl)
    {
        if ($downloadUrl) {
            // Save the file path in the database
            $newChar = new Character();
            $newChar->url = $downloadUrl;
            $newChar->title = $this->title;
            $newChar->user_id = Auth::user()->id;
            $newChar->save();

            DownloadFileJob::dispatch($newChar->id);
        }
    }


    public function route()
    {
        return Route::get('/avatar-creator')
            ->name('AvatarCreator')
            ->middleware('auth');
    }

    public function render()
    {
        return view('pages.avatar-creator');
    }
}
