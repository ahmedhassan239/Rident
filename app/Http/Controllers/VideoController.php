<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VideoController extends Controller
{
    public function __construct()
    {
        //  $this->middleware('auth:api', ['except' => ['getAll']]);
    }

    public function index()
    {
        $data = Video::all();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                // 'name' => 'required|json',
                // 'slug' => 'required|json',
                // 'description' => 'required|json',
                // 'overview' => 'required|json',
                // 'seo_title' => 'nullable|json',
                // 'seo_keywords' => 'nullable|json',
                // 'seo_description' => 'nullable|json'
            ]);

            // Initialize a new doctor instance
            $data = new Video();

            // Define the translatable fields
            $translatableFields = [
                'title',

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $dataField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($dataField as $locale => $value) {
                    $data->setTranslation($field, $locale, $value);
                }
            }

            $data->link = $request->link;
            $data->playlist_id = $request->playlist_id;


            // Persist the doctor instance into the database
            $data->save();


            return response()->json($data);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        return response()->json($video);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    { {
            try {
                $validatedData = $request->validate([
                    // 'name' => 'required|json',
                    // 'slug' => 'required|json',
                    // 'description' => 'sometimes|required|json',
                    // 'overview' => 'sometimes|required|json',
                    // 'seo_title' => 'nullable|json',
                    // 'seo_keywords' => 'nullable|json',
                    // 'seo_description' => 'nullable|json',
                    // 'status' => 'required', // Assuming status is required
                    // 'featured' => 'required', // Assuming featured is required
                    // 'banner' => 'required', // Assuming banner file ID is required
                    // 'thumb' => 'required', // Assuming thumb file ID is required
                ]);

                $translatableFields = [
                    'title',
                ];

                foreach ($translatableFields as $field) {
                    if ($request->has($field)) {
                        $dataField = json_decode($request->$field, true);

                        // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                        //     return response()->json([
                        //         'status' => 'error',
                        //         'message' => 'Validation failed',
                        //         'errors' => ['English ' . $field . ' is required and must be a string'],
                        //     ], 422);
                        // }

                        foreach ($dataField as $locale => $value) {
                            $video->setTranslation($field, $locale, $value);
                        }
                    }
                }


                $video->link = $request->link;
                $video->playlist_id = $request->playlist_id;



                $video->save();


                // Prepare response data
                $responseData = $video->toArray();


                return response()->json($responseData);
            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->validator->errors()
                ], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return response()->json(null, 204);
    }


    public function getAll(Request $request)
    {
        $playlistId = $request->query('playlist_id');
        $playlistSlug = $request->query('playlist_slug');

        // Build the initial query with necessary relationships loaded
        $query = Playlist::with('videos');

        // Conditionally add filters to the query based on playlist_id or playlist_slug
        if ($playlistId && $playlistId !== 'all') {
            $query->where('id', $playlistId);
        }

        if ($playlistSlug && $playlistSlug !== 'all') {
            $query->where('slug', 'like', '%' . $playlistSlug . '%');
        }

        // Execute the query to get a collection of playlists
        $data = $query->get();

        // Format the output
        if ($playlistSlug === 'all') {
            // Collect all videos across all playlists
            $allVideos = $data->flatMap(function ($playlist) {
                return $playlist->videos->map(function ($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'link' => $video->link,
                    ];
                });
            })->values();

            return response()->json([
                'videos' => $allVideos
            ]);
        } else {
            // Format the output for specific playlist or playlist id
            $formattedData = $data->map(function ($playlist) {
                return [
                    'playlist' => [
                        'id' => $playlist->id,
                        'name' => $playlist->name,
                        'slug' => $playlist->slug,
                    ],
                    'videos' => $playlist->videos->map(function ($video) {
                        return [
                            'id' => $video->id,
                            'title' => $video->title,
                            'link' => $video->link,
                        ];
                    })->values(),
                ];
            });

            return response()->json([
                'data' => $formattedData
            ]);
        }
    }
}
