<?php

namespace App\Components\Modal;

use App\Models\Character;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadAssetModal extends Component
{
    use WithFileUploads;

    public $glbfile;
    public $thumbnail;

    public $type;
    public $title;

    public function uploadFile()
    {
        if ($this->type == "Character") {
            $filePath = $this->glbfile->store('assets/Character', 'public');
            $thumbnailPath = $this->thumbnail->store('assets/Character', 'public');

            $newChar = new Character();
            $newChar->icon = $thumbnailPath;
            $newChar->url = $filePath;
            $newChar->title = $this->title;
            $newChar->save();
        }

    }

    public function render()
    {
        return view('modal.upload-asset-modal');
    }
}
