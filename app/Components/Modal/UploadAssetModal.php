<?php

namespace App\Components\Modal;

use App\Models\Animation;
use App\Models\AnimationCategory;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadAssetModal extends Component
{
    use WithFileUploads;

    public $glbfile;
    public $thumbnail;

    public $type;
    public $title;

    public $category_select;
    public $categories = [];

    public function mount()
    {
        $this->categories = AnimationCategory::all();
    }

    public function uploadFile()
    {
        if ($this->type == "Character") {
            $thumbnailPath = $this->thumbnail->store('assets/Character', 'public');
            $filePath = $this->glbfile->store('assets/Character', 'public');

            $newChar = new Character();
            $newChar->icon = $thumbnailPath;
            $newChar->url = $filePath;
            $newChar->title = $this->title;
            $newChar->user_id = Auth::user()->id;
            $newChar->save();

        }

        if ($this->type == "Animation") {
            $thumbnailPath = $this->thumbnail->store('assets/Animation', 'public');
            $filePath = $this->glbfile->store('assets/Animation', 'public');

            $newChar = new Animation();
            $newChar->icon = $thumbnailPath;
            $newChar->url = $filePath;
            $newChar->title = $this->title;
            $newChar->user_id = Auth::user()->id;
            $newChar->category_id = $this->category_select;
            $newChar->save();
        }

    }

    public function render()
    {
        return view('modal.upload-asset-modal');
    }
}
