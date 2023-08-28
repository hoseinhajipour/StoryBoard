<?php

namespace App\Components\Inc;

use App\Models\Animation;
use App\Models\Character;
use App\Models\Prop;
use App\Models\Scene;
use Livewire\Component;

class AssetLoader extends Component
{
    public $Scenes = [];
    public $Characters = [];
    public $Props = [];
    public $Animations = [];

    public function mount()
    {
        $this->Scenes = Scene::all();
        $this->Characters = Character::all();
        $this->Props = Prop::all();
        $this->Animations = Animation::all();
    }

    public function render()
    {
        return view('inc.asset-loader');
    }
}
