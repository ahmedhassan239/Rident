<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlaylistController extends Controller
{
    // Display a listing of the playlists
    public function index()
    {
        $playlists = Playlist::all();
        return response()->json($playlists);
    }

    // Store a newly created playlist in storage
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
            
            ]);

            // Initialize a new doctor instance
            $blog = new Playlist();

            // Define the translatable fields
            $translatableFields = [
                'name', 'slug',

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $blogField = json_decode($request->$field, true);

                foreach ($blogField as $locale => $value) {
                    $blog->setTranslation($field, $locale, $value);
                }
            }

         

            // Persist the doctor instance into the database
            $blog->save();
          

            return response()->json($blog);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    // Display the specified playlist
    public function show(Playlist $playlist)
    {
        return response()->json($playlist);
    }

    // Update the specified playlist in storage
    public function update(Request $request, Playlist $playlist)
    {
        try {
            $validatedData = $request->validate([
               
            ]);

            $translatableFields = [
                'name', 'slug', 
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $blogField = json_decode($request->$field, true);


                    foreach ($blogField as $locale => $value) {
                        $playlist->setTranslation($field, $locale, $value);
                    }
                }
            }

    

            $playlist->save();

            return response()->json($playlist);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    // Remove the specified playlist from storage
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();
        return response()->json(null, 204);
    }

    public function getAll()
    {
        // app()->setLocale($lang);

        $data = Playlist::get()
            ->map(function ($val) {
               
                return [
                    'id' => $val->id,
                    'name' => $val->name ?? [],
                    'slug' => $val->slug ?? [],
          
                ];
            });

        return response()->json([
            'data' => $data
        ]);
    }
}
