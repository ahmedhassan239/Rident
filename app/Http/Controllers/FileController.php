<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index(Folder $folder)
    {
        $files = $folder->files;
        return response()->json($files);
    }

    public function store(Request $request, Folder $folder)
    {
        $request->validate([
            'name' => 'required|file|max:2048|mimes:jpg,jpeg,png,gif,doc,docx,pdf,txt,webp', // 2MB
        ]);
    
        $path = $this->getFolderFullPath($folder);
        $file = $request->file('name');
        $isWebP = $file->getClientOriginalExtension() === 'webp';
    
        // Get the original file name
        $originalName = $file->getClientOriginalName();
    
        if ($file->getClientOriginalExtension() != 'webp') {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "-rident" . ".webp";
        } else {
            $originalName = $file->getClientOriginalName();
        }
    
        // Ensure the file name is unique to avoid overwriting existing files
        $filename = $originalName;
        $fileCounter = 1;
        while (Storage::disk('public')->exists($path . '/' . $filename)) {
            $baseName = $isWebP ? pathinfo($originalName, PATHINFO_FILENAME) : pathinfo($originalName, PATHINFO_FILENAME) . "-rident";
            $extension = $isWebP ? 'webp' : $file->getClientOriginalExtension();
            $filename = $baseName . '_' . $fileCounter . "-rident" . '.' . $extension;
            $fileCounter++;
        }
    
        // Check if the file size is greater than 100 KB
        if ($file->getSize() > 100 * 1024) {
            // File is larger than 100 KB, compress it
            if (!$isWebP) {
                // Convert and compress image to webp if not already webp
                $image = Image::make($file)->encode('webp', 25); // Adjust quality as needed
                Storage::disk('public')->put($path . '/' . $filename, (string) $image);
            } else {
                // If the file is already webp, compress it
                $image = Image::make($file)->encode('webp', 25); // Adjust quality as needed
                Storage::disk('public')->put($path . '/' . $filename, (string) $image);
            }
        } else {
            // File is smaller than or equal to 100 KB, save without compressing
            Storage::disk('public')->put($path . '/' . $filename, file_get_contents($file));
        }
    
        $newFile = new File;
        $newFile->folder_id = $folder->id;
        $newFile->name = $filename;
        $newFile->save();
    
        // Prepare the response data
        $responseData = [
            'file_name' => $filename,
            'folder_name' => $folder->name // Assuming your Folder model has a 'name' attribute
        ];
    
        return response()->json($responseData, 201);
    }
    


    public function getFolderFullPath(Folder $folder)
    {

        $path = $folder->name;
        $parent_id = $folder->parent_id;

        do {

            $parent_folder = Folder::where('id', $parent_id)->first();

            if ($parent_folder) {
                $path = $parent_folder->name . '/' . $path;
                $parent_id = $parent_folder->parent_id;
            } else {
                $parent_id = 0;
            }
        } while ($parent_id != 0 && $parent_id != null);

        return $path;
    }

    public function show(File $file)
    {
        return response()->json($file);
    }

    public function update(Request $request, Folder $folder, $fileId)
    {
        $request->validate([
            'name' => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,doc,docx,pdf,txt,webp', // 20MB
        ]);

        $fileToUpdate = File::findOrFail($fileId);

        $path = $this->getFolderFullPath($folder);
        $newFile = $request->file('name');

        // Get the original file name
        $originalName = $newFile->getClientOriginalName();

        // Ensure the file name is unique to avoid overwriting existing files
        $filename = $originalName;
        $fileCounter = 1;
        while (Storage::disk('public')->exists($path . '/' . $filename)) {
            $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . $fileCounter . '.' . $newFile->getClientOriginalExtension();
            $fileCounter++;
        }

        // Store the new file with the original file name
        $newFile->storeAs($path, $filename, 'public');

        // Delete the old file from storage
        Storage::disk('public')->delete($path . '/' . $fileToUpdate->name);

        // Update the file record
        $fileToUpdate->name = $filename;
        $fileToUpdate->save();

        // Prepare the response data
        $responseData = [
            'file_name' => $filename,
            'folder_name' => $folder->name // Assuming your Folder model has a 'name' attribute
        ];

        return response()->json($responseData, 200);
    }


    public function destroy(Folder $folder, $fileId)
    {
        $file = File::findOrFail($fileId);

        // Check if the file belongs to the specified folder
        if ($file->folder_id != $folder->id) {
            return response()->json(['error' => 'File does not belong to the specified folder'], 403);
        }

        $path = $this->getFolderFullPath($folder);

        // Delete the file from storage
        Storage::disk('public')->delete($path . '/' . $file->name);

        // Delete the file record from the database
        $file->delete();

        return response()->json(['message' => 'File successfully deleted'], 200);
    }
}
