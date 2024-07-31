<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;


class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAllNews']]);
    }

    public function index()
    {
        $news = News::all();
        return response()->json($news);
    }

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
            $news = new News();

            // Define the translatable fields
            $translatableFields = [
                'name', 'slug', 'description'

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $newsField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($newsField as $locale => $value) {
                    $news->setTranslation($field, $locale, $value);
                }
            }

            // if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            //     $blog->addMediaFromRequest('banner')->toMediaCollection('banner');
            // }

            $news->status = $request->status;
            $news->link = $request->link;
            


            // Persist the doctor instance into the database
            $news->save();
          
            $news->files()->attach($request->thumb, ['type' => 'thumb']);
            $news->files()->attach($request->logo, ['type' => 'logo']);

            return response()->json($news);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(News $news)
    {
        // Initialize IDs and URLs
        $thumbId = null;
        $logoId = null;
        $thumbUrl = '';
        $logoUrl = '';

        // Loop through the files to find banner and thumb
        foreach ($news->files as $file) {
         if ($file->pivot->type == 'thumb') {
                $thumbId = $file->id;  // Store the thumb ID
                $thumbUrl = $file->file_url;
            }elseif($file->pivot->type == 'logo'){
                $logoId = $file->id;  // Store the thumb ID
                $logoUrl = $file->file_url;
            }
        }

        // Prepare response data
        $responseData = $news->toArray();

        $responseData['thumb_id'] = $thumbId;
        $responseData['logo_id'] = $logoId;
     
        $responseData['thumb_url'] = $thumbUrl;
        $responseData['logo_url'] = $logoUrl;
        unset($responseData['files']);

        return response()->json($responseData);
    }



    public function update(Request $request, News $news)
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
                'name', 'slug', 'description', 
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $newsField = json_decode($request->$field, true);

                    // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($newsField as $locale => $value) {
                        $news->setTranslation($field, $locale, $value);
                    }
                }
            }

            $news->status = $request->status;
            $news->link = $request->link;
          


            $news->save();

            // Detach existing files and attach new ones
            // if()
            $news->files()->detach();

          
            $news->files()->attach($request->thumb, ['type' => 'thumb']);
            $news->files()->attach($request->logo, ['type' => 'logo']);

            // Retrieve banner and thumb URLs
            $news->load('files');
            // dd($blog->load('files'));// Reload the files relationship
        
            $thumbId = null;
            $logoId = null;
            $thumbUrl = '';
            $logoUrl = '';
    
            // Loop through the files to find banner and thumb
            foreach ($news->files as $file) {
             if ($file->pivot->type == 'thumb') {
                    $thumbId = $file->id;  // Store the thumb ID
                    $thumbUrl = $file->file_url;
                }elseif($file->pivot->type == 'logo'){
                    $logoId = $file->id;  // Store the thumb ID
                    $logoUrl = $file->file_url;
                }
            }

            // Prepare response data
            $responseData = $news->toArray();

            $responseData['thumb_id'] = $thumbId;
            $responseData['logo_id'] = $logoId;
        
            $responseData['thumb_url'] = $thumbUrl;
            $responseData['logo_url'] = $logoUrl;
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

    public function destroy(News $news)
    {
        $news->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'News successfully deleted'], 200);
    }

    public function getAllNews()
    {
        // app()->setLocale($lang);

        $news = News::where('status', 1)->orderBy('created_at', 'desc')->get()
            ->map(function ($val) {
                $thumb = '';
                foreach ($val->files as $file) {
                    if ($file->pivot->type == 'thumb') {
                        $thumb = $file->file_url;
                    }
                }
                $logo = '';
                foreach ($val->files as $file) {
                    if ($file->pivot->type == 'logo') {
                        $logo = $file->file_url;
                    }
                }
                return [
                    'id' => $val->id,
                    'name' => $val->name ?? [],
                    'slug' => $val->slug ?? [],
                    'description' => $val->description ?? [],
                    'link' => $val->link,
                    'created_at' => $val->created_at,
                    'alt' => $val->name,
                    'thumb' => $thumb,
                    'logo' => $logo
                ];
            });

        return response()->json([
            'data' => $news
        ]);
    }

}
