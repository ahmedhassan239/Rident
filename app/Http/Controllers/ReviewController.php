<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAll']]);
    }

    public function index()
    {
        $data = Review::with('files')->get();
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
                'name' => 'required|json',
                'description' => 'required|json',
                'link_name' => 'required|json',
                'status' => 'required',
                'link' => 'required|url',
                'rate' => 'required|numeric'
            ]);

            // Initialize a new Review instance
            $data = new Review();

            // Define the translatable fields
            $translatableFields = ['name', 'description', 'link_name'];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $dataField = json_decode($request->$field, true);

                // Check if dataField is an array
                if (!is_array($dataField)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid JSON format for ' . $field,
                    ], 422);
                }

                foreach ($dataField as $locale => $value) {
                    $data->setTranslation($field, $locale, $value);
                }
            }

            $data->status = $request->status;
            $data->link = $request->link;
            $data->rate = $request->rate;

            // Persist the Review instance into the database
            $data->save();

            // Attach avatar if present
            if ($request->has('avatar')) {
                $data->files()->attach($request->avatar, ['type' => 'avatar']);
            }

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
    public function show(Review $review)
    {
        // Initialize IDs and URLs

        $avatarId = null;
        $avatarUrl = '';


        // Loop through the files to find banner and thumb
        foreach ($review->files as $file) {
            if ($file->pivot->type == 'avatar') {
                $avatarId = $file->id;  // Store the banner ID
                $avatarUrl = $file->file_url;
            }
        }

        // Prepare response data
        $responseData = $review->toArray();
        $responseData['avatar_id'] = $avatarId;

        $responseData['avatar_url'] = $avatarUrl;

        unset($responseData['files']);

        return response()->json($responseData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {

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
                'name', 'description', 'link_name'
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
                        $review->setTranslation($field, $locale, $value);
                    }
                }
            }

            $review->status = $request->status;
            $review->link = $request->link;
            $review->rate = $request->rate;

            $review->save();


            // Prepare response data
            $responseData = $review->toArray();

            $review->files()->detach();

            $review->files()->attach($request->avatar, ['type' => 'avatar']);
            $review->load('files');
            $avatarId = null;
            $avatarUrl = '';


            // Loop through the files to find banner and thumb
            foreach ($review->files as $file) {
                if ($file->pivot->type == 'avatar') {
                    $avatarId = $file->id;  // Store the banner ID
                    $avatarUrl = $file->file_url;
                }
            }
            $responseData = $review->toArray();
            $responseData['avatar_id'] = $avatarId;

            $responseData['avatar_url'] = $avatarUrl;

            unset($responseData['files']);
            return response()->json($responseData);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json("success", 204);
    }


    public function getAll()
    {
        // app()->setLocale($lang);

        $data = Review::where('status', 1)->get()
            ->map(function ($val) {
                $avatar = '';
                foreach ($val->files as $file) {
                    if ($file->pivot->type == 'avatar') {
                        $avatar = $file->file_url;
                    }
                }
                return [
                    'id' => $val->id,
                    'name' => $val->name ?? [],
                    'description' => $val->description ?? [],
                    'link_name' => $val->link_name ?? [],
                    'link' => $val->link,
                    'rate' => $val->rate,
                    'alt' => $val->name,
                    'avatar' => $avatar,


                ];
            });

        return response()->json([
            'data' => $data
        ]);
    }
}
