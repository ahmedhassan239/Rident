<?php

namespace App\Http\Controllers;

use App\Models\GlobalSeo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class GlobalSeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [ 'getSingleGlobaleSeo']]);
    }

    public function index()
    {
        $globalseos = GlobalSeo::all();
        return response()->json($globalseos);
    }

    public function store(Request $request)
    {
        try {
            // Initialize a new doctor instance
            $globalseo = new GlobalSeo();

            // Define the translatable fields
            $translatableFields = [
                'title', 'keywords',
                'description', 'facebook_description',
                'twitter_title', 'twitter_description', 'twitter_card', 'og_title', 'twitter_label1', 'twitter_data1'
            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $globalseoField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($globalseoField['en']) || !is_string($globalseoField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($globalseoField as $locale => $value) {
                    $globalseo->setTranslation($field, $locale, $value);
                }
            }

            if ($request->hasFile('facebook_image') && $request->file('facebook_image')->isValid()) {
                $globalseo->addMediaFromRequest('facebook_image')->toMediaCollection('facebook_image');
            }
            if ($request->hasFile('twitter_image') && $request->file('twitter_image')->isValid()) {
                $globalseo->addMediaFromRequest('twitter_image')->toMediaCollection('twitter_image');
            }
            $globalseo->revisit_after = $request->revisit_after;
            $globalseo->facebook_page_id = $request->facebook_page_id;
            $globalseo->author = $request->author;
            $globalseo->google_site_verification = $request->google_site_verification;
            $globalseo->facebook_site_name = $request->facebook_site_name;
            $globalseo->facebook_admins = $request->facebook_admins;
            $globalseo->og_url = $request->og_url;
            $globalseo->og_type = $request->og_type;
            $globalseo->twitter_site = $request->twitter_site;
            $globalseo->robots = $request->robots;

            // Persist the doctor instance into the database
            $globalseo->save();
            $globalseo->files()->attach($request->facebook_image, ['type' => 'facebook_image']);
            $globalseo->files()->attach($request->twitter_image, ['type' => 'twitter_image']);

            return response()->json($globalseo);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(GlobalSeo $globalseo)
    {
         // Initialize IDs and URLs
         $facebook_imageId = null;
         $twitter_imageId = null;
         $facebook_imageUrl = '';
         $twitter_imageUrl = '';
     
         // Loop through the files to find banner and thumb
         foreach ($globalseo->files as $file) {
             if ($file->pivot->type == 'facebook_image') {
                 $facebook_imageId = $file->id;  // Store the banner ID
                 $facebook_imageUrl = $file->file_url;
             } elseif ($file->pivot->type == 'twitter_image') {
                 $twitter_imageId = $file->id;  // Store the thumb ID
                 $twitter_imageUrl = $file->file_url;
             }
         }
     
         // Prepare response data
         $responseData = $globalseo->toArray();
         $responseData['facebook_image_id'] = $facebook_imageId;
         $responseData['twitter_image_id'] = $twitter_imageId;
         $responseData['facebook_image_url'] = $facebook_imageUrl;
         $responseData['twitter_image_url'] = $twitter_imageUrl;
         unset($responseData['files']);
        return response()->json($responseData);
    }

    public function update(Request $request, GlobalSeo $globalseo)
    {
        try {

            $translatableFields = [
                'title', 'keywords',
                'description', 'facebook_description',
                'twitter_title', 'twitter_description', 'twitter_card', 'og_title', 'twitter_label1', 'twitter_data1'
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $globalSeoField = json_decode($request->$field, true);

                    // if (!isset($globalSeoField['en']) || !is_string($globalSeoField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($globalSeoField as $locale => $value) {
                        $globalseo->setTranslation($field, $locale, $value);
                    }
                }
            }

            $globalseo->revisit_after = $request->revisit_after;
            $globalseo->facebook_page_id = $request->facebook_page_id;
            $globalseo->author = $request->author;
            $globalseo->google_site_verification = $request->google_site_verification;
            $globalseo->facebook_site_name = $request->facebook_site_name;
            $globalseo->facebook_admins = $request->facebook_admins;
            $globalseo->og_url = $request->og_url;
            $globalseo->og_type = $request->og_type;
            $globalseo->twitter_site = $request->twitter_site;
            $globalseo->robots = $request->robots;

            // Persist the doctor instance into the database
            $globalseo->save();

            // Detach existing files and attach new ones
            $globalseo->files()->detach();
            $globalseo->files()->attach($request->facebook_image, ['type' => 'facebook_image']);
            $globalseo->files()->attach($request->twitter_image, ['type' => 'twitter_image']);

            // Retrieve banner and thumb URLs
            $globalseo->load('files'); // Reload the files relationship
            $facebook_imageId = null;
            $twitter_imageId = null;
            $facebook_imageUrl = '';
            $twitter_imageUrl = '';
        
            // Loop through the files to find banner and thumb
            foreach ($globalseo->files as $file) {
                if ($file->pivot->type == 'facebook_image') {
                    $facebook_imageId = $file->id;  // Store the banner ID
                    $facebook_imageUrl = $file->file_url;
                } elseif ($file->pivot->type == 'twitter_image') {
                    $twitter_imageId = $file->id;  // Store the thumb ID
                    $twitter_imageUrl = $file->file_url;
                }
            }
        
            // Prepare response data
            $responseData = $globalseo->toArray();
            $responseData['facebook_image_id'] = $facebook_imageId;
            $responseData['twitter_image_id'] = $twitter_imageId;
            $responseData['facebook_image_url'] = $facebook_imageUrl;
            $responseData['facebook_image_url'] = $twitter_imageUrl;
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

    public function destroy(GlobalSeo $globalseo)
    {
        $globalseo->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'GlobalSeo successfully deleted'], 200);
    }

    public function getSingleGlobaleSeo($id)
    {
        $lang = app()->getLocale();
        $value = GlobalSeo::where('id', $id)
            ->with('files')
            ->firstOrFail();

        $facebook_image = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'facebook_image') {
                $facebook_image = $file->file_url;
            }
        }

        $twitter_image = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'twitter_image') {
                $twitter_image = $file->file_url;
            }
        }
        $data[] = [

            'title' => $value->title,
            'keywords' => $value->keywords,
            'description' => $value->description,
            'facebook_description' => $value->description,
            'twitter_title' => $value->title,
            'twitter_description' => $value->description,
            'robots' => $value->robots,
            'og_title' => $value->title,
            'revisit_after' => $value->revisit_after,
            'facebook_page_id' => $value->facebook_page_id,
            'author' => $value->author,
            'google_site_verification' => $value->google_site_verification,
            'og_url' => $value->og_url,
            'facebook_admins' => $value->facebook_admins,
            'og_type' => $value->og_type,
            'twitter_site' => $value->twitter_site,
            'facebook_image' => $facebook_image,
            'twitter_image' => $twitter_image,
        ];
        return response()->json([
            'data' => $data,
        ], '200');
    }
}
