<?php

namespace App\Components\Inc;

use App\Models\Animation;
use Livewire\Component;

class DialogWizard extends Component
{
    public $animations = [];

    public function mount()
    {
        $this->animations = Animation::all();
    }

    public function render()
    {
        return view('inc.dialog-wizard');
    }
}
