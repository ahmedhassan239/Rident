<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAll']]);
    }

    public function index()
    {
        $data = Slider::all();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                // 'title' => 'required|string',
                // 'description' => 'nullable|string',
                // 'link_name' => 'nullable|string',
                // 'link' => 'nullable|url'
            ]);
    
            // Initialize a new Slider instance
            $data = new Slider();
    
            // Define the translatable fields
            $translatableFields = ['title', 'description', 'link_name', 'link'];
    
            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $dataField = $request->input($field);  // Get the field data from the request
    
                if (is_string($dataField)) {  // Check if the field data is a string
                    $decodedField = json_decode($dataField, true);  // Decode the JSON string
    
                    // if (json_last_error() !== JSON_ERROR_NONE) {
                    //     // Handle JSON errors, such as returning an error response
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Invalid JSON for ' . $field,
                    //     ], 400);
                    // }
    
                    foreach ($decodedField as $locale => $value) {
                        $data->setTranslation($field, $locale, $value);
                    }
                }
            }
    
            // Save the Slider instance to the database
            $data->save();
    
            // Return a successful response with the created data
            return response()->json($data);
        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }
    

    public function show(Slider $slider)
    {
        // Initialize IDs and URLs
        $bannerId = null;
       
        $bannerUrl = '';
     

        // Loop through the files to find banner and thumb
        foreach ($slider->files as $file) {
            if ($file->pivot->type == 'banner') {
                $bannerId = $file->id;  // Store the banner ID
                $bannerUrl = $file->file_url;
            } 
        }

        // Prepare response data
        $responseData = $slider->toArray();
        $responseData['banner_id'] = $bannerId;

        $responseData['banner_url'] = $bannerUrl;

        unset($responseData['files']);

        return response()->json($responseData);
    }



    public function update(Request $request, Slider $slider)
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
                'title', 'description', 'link_name', 'link'
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $sliderField = json_decode($request->$field, true);

                    // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($sliderField as $locale => $value) {
                        $slider->setTranslation($field, $locale, $value);
                    }
                }
            }

            $slider->save();

            // Detach existing files and attach new ones
            // if()
            $slider->files()->detach();

            $slider->files()->attach($request->banner, ['type' => 'banner']);


            // Retrieve banner and thumb URLs
            $slider->load('files');
            // dd($blog->load('files'));// Reload the files relationship
            $bannerId = null;

            $bannerUrl = '';
     

            // Loop through the files to find banner and thumb
            foreach ($slider->files as $file) {
                if ($file->pivot->type == 'banner') {
                    $bannerId = $file->id;  // Store the banner ID
                    $bannerUrl = $file->file_url;
                } 
            }

            // Prepare response data
            $responseData = $slider->toArray();
            $responseData['banner_id'] = $bannerId;
            
            $responseData['banner_url'] = $bannerUrl;
     
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

    public function destroy(Slider $slider)
    {
        $slider->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Slider successfully deleted'], 200);
    }

    public function getAll()
    {
        // app()->setLocale($lang);

        $sliders = Slider::get()
            ->map(function ($val) {
                $banner = '';
                foreach ($val->files as $file) {
                    if ($file->pivot->type == 'banner') {
                        $banner = $file->file_url;
                    }
                }
                return [
                    'id' => $val->id,
                    'title' => $val->title,
                    'description' => $val->description,
                    'link_name' => $val->link_name,
                    'link' => $val->link,
                    'alt' => $val->title,
                    'banner' => $banner,
                ];
            });

        return response()->json([
            'data' => $sliders
        ]);
    }
}
