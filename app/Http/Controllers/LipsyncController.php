<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LipsyncController extends Controller
{

    public function AnalyzeAudio(Request $request)
    {
        if ($request->hasFile('audioFile')) {
            $audioFile = $request->file('audioFile');

            // Generate a unique file name to prevent overwriting
            $fileName = time() . '_' . $audioFile->getClientOriginalName();

            // Store the audio file
            $audioPath = $audioFile->storeAs('audio', $fileName, 'public');

            // Get the full storage path of the stored audio file
            $audioStoragePath = storage_path("app/public/{$audioPath}");

            // Get the original name of the uploaded audio file
            $originalFileName = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);

            // Create the output file path with the same name as the audio file but with .txt extension
            $outputFileName = "{$fileName}.txt";
            $outputFilePath = storage_path("app/public/audio/{$outputFileName}");

            // Construct the Python command with the audio storage path and output file path
            $pythonCommand = "python -m allosaurus.run --timestamp=True -i {$audioStoragePath} --output={$outputFilePath}";
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

            // Return the JSON data to the frontend
            return response()->json([
                'message' => 'File uploaded and analyzed successfully',
                'phonemes' => $jsonPhonemes,
                'audio_url' => $audioStoragePath, // Use storage path instead of asset URL
            ]);
        } else {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }



}
