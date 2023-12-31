<?php

namespace App\Components\Pages;

use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\WithFileUploads;

class Lipsync extends Component
{

    use WithFileUploads;

    public $audio;


    public function AnalyzeAudio()
    {
        // Store the uploaded audio file and get its path
        $audioPath = $this->audio->store('audio', 'public');

        // Get the full URL of the stored audio file
        $audioUrl = storage_path("app\\public\\" . $audioPath);

        // Get the original name of the uploaded audio file
        $originalFileName = pathinfo($this->audio->getClientOriginalName(), PATHINFO_FILENAME);

        // Create the output file path with the same name as the audio file but with .txt extension
        $outputFilePath = storage_path("app/public/audio/{$originalFileName}.txt");


        $outputFilePath = str_replace("/", "\\", $outputFilePath);
        $audioUrl = str_replace("/", "\\", $audioUrl);

        // Construct the Python command with the audio URL and output file path
        $pythonCommand = "python -m allosaurus.run --timestamp=True -i $audioUrl --output=$outputFilePath";
        exec($pythonCommand);


        // Read the content of the output file
        $outputFileContent = file_get_contents($outputFilePath);

        // Process the content into an array
        $lines = explode("\n", trim($outputFileContent));
        $phonemes = [];
        foreach ($lines as $line) {
            $parts = explode(' ', $line);
            $start = floatval($parts[0]);
            $duration = floatval($parts[1]);
            $content = implode(' ', array_slice($parts, 2));
            $phonemes[] = [
                'start' => $start,
                'duration' => $duration,
                'content' => $content,
            ];
        }

        // Convert the array to JSON
        $jsonPhonemes = json_encode($phonemes);


        $this->dispatchBrowserEvent('conversationHistoryLoaded', [
            'phonemes' => $jsonPhonemes,
            'audio_url' => asset("storage/" . $audioPath),
        ]);
    }

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
