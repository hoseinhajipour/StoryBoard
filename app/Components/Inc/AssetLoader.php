<?php

namespace App\Components\Inc;

use App\Models\Character;
use App\Models\Prop;
use App\Models\Scene;
use Livewire\Component;

class AssetLoader extends Component
{
    public $Scenes = [];
    public $Characters = [];
    public $Props = [];

    public function mount()
    {
        $this->Scenes = Scene::all();
        $this->Characters = Character::all();
        $this->Props = Prop::all();
    }

    public function render()
    {
        return view('inc.asset-loader');
    }
}
