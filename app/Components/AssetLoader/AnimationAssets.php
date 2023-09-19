<?php

namespace App\Components\AssetLoader;

use App\Models\Animation;
use App\Models\AnimationCategory;
use Livewire\Component;

class AnimationAssets extends Component
{

    public $Animations = [];
    public $categories = [];


    protected $listeners = ["loadCategory"];

    public function mount()
    {
        $this->Animations = Animation::all();
        $this->categories = AnimationCategory::whereNull('parent_id')->with('subcategories')->get();
    }


    public function loadCategory($catid)
    {

        $this->Animations = Animation::where("category_id", $catid)->get();

    }

    public function render()
    {
        return view('asset-loader.animation-assets');
    }
}
