<?php

namespace App\Components\Inc;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Menu extends Component
{
    use LivewireAlert;

    public $project;

    public function mount(Request $request)
    {
        if ($request->id) {
            $this->project = Project::find($request->id);
        }
    }

    protected $listeners = ['SaveProject'];

    public function SaveProject($serializedSceneJson, $imagedata)
    {
        $directory = storage_path('app\public\projects');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'project_' . time() . '_' . Str::random(10) . '.json';

        $filePath = $directory . '/' . $filename;

        file_put_contents($filePath, $serializedSceneJson);

        // Get the storage URL for the $filePath
        $storageUrl = Storage::url('public/projects/' . $filename);


        $this->project->file = $storageUrl;
        $this->project->save();


        list($type, $data) = explode(';', $imagedata);
        list(, $data) = explode(',', $data);

        $fileName = uniqid() . '.' . explode('/', $type)[1];

        $imgData = base64_decode($data);
        $filePath = $directory . '/' . $fileName;
        file_put_contents($filePath, $imgData);


        $storageUrl = 'projects/' . $fileName;
        $this->project->icon = $storageUrl;
        $this->project->save();


        $this->alert('success', 'Save success');
    }

    public function render()
    {
        return view('inc.menu');
    }
}
