<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
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
            
            ]);

            // Initialize a new doctor instance
            $category = new Category();

            // Define the translatable fields
            $translatableFields = [
                'name'

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $categoryField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($categoryField as $locale => $value) {
                    $category->setTranslation($field, $locale, $value);
                }
            }

            // if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            //     $blog->addMediaFromRequest('banner')->toMediaCollection('banner');
            // }

            // $blog->status = $request->status;
            // $blog->featured = $request->featured;
            // $blog->robots = $request->robots;
            // $blog->related_blogs = $request->related_blogs;

            // Persist the doctor instance into the database
            $category->save();
            // $blog->files()->attach($request->banner, ['type' => 'banner']);
            // $blog->files()->attach($request->thumb, ['type' => 'thumb']);

            return response()->json($category);
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
    public function show(Category $category)
    {
        // Prepare response data
        $responseData = $category->toArray();
        return response()->json($responseData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validatedData = $request->validate([
                // 'name' => 'required|json',
                
            ]);
    
            $translatableFields = [
                'name'
            ];
    
            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $categoryField = json_decode($request->$field, true);
    
                    // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }
    
                    foreach ($categoryField as $locale => $value) {
                        $category->setTranslation($field, $locale, $value);
                    }
                }
            }
    
        

    
            $category->save();
    
            
        
            // Prepare response data
            $responseData = $category->toArray();
    
    
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
    public function destroy(Category $category)
    {
        $category->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Category successfully deleted'], 200);
    }
}
