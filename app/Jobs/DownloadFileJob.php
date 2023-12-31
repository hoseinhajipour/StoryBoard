<?php

namespace App\Jobs;

use App\Models\character;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $character_id;

    public function __construct($id)
    {
        $this->character_id = $id;
    }

    public function handle()
    {
        set_time_limit(1024);
        ini_set('memory_limit', '1024M');
        $character = Character::find($this->character_id);
        // Get the filename from the URL
        $filename = basename($character->url);
        $icon_url = $character->url;
        // Get the contents of the remote file
        Log::info('Start Download: ' . $character->url);
        $contents = file_get_contents($character->url);

        // Save the contents to a local file using the Storage facade
        $path = "public/assets/Character/" . $filename;
        Storage::put($path, $contents);

        // Create an array with the desired structure
        $characterUrlData = [
            [
                'download_link' => "assets\\Character\\" . $filename,
                'original_name' => $filename
            ]
        ];

        // Convert the array to a JSON string
        $jsonCharacterUrlData = json_encode($characterUrlData);

        // Update the $character->url with the JSON string
        $character->url = $jsonCharacterUrlData;
        $character->save();



        $icon_url = str_replace(".glb", ".png", $icon_url);
        $icon_contents = file_get_contents($icon_url);

        $icon_filename = basename($icon_url);
        $icon_filename = str_replace(".glb", ".png", $icon_filename);

        // Save the contents to a local file using the Storage facade
        $save_path = "public/assets/Character/" . $icon_filename;
        Storage::put($save_path, $icon_contents);

        $character->icon = "assets\\Character\\" . $icon_filename;
        $character->save();

        Log::info('File saved: ' . $save_path);
    }

}
