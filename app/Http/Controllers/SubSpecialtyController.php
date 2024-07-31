<?php

namespace App\Http\Controllers;

use App\Models\SubSpecialty;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SubSpecialtyController extends Controller
{
       // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['getAllBlogs', 'getSingleBlog','getFeaturedBlogs']]);
    // }
    
    public function index(Request $request)
    {
        // Check if a country_id is provided
        $countryId = $request->header('country_id');
        if (!is_null($countryId)) {
            // Filter specialties by the provided country_id
            $sub_specialties = SubSpecialty::where('country_id', $countryId)->with('country','files')->get();
        } else {
            // Return all specialties if no country_id is provided
            $sub_specialties = SubSpecialty::with('country','files')->get();
        }

        return response()->json($sub_specialties);
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
            $sub_specialty = new SubSpecialty();

            // Define the translatable fields
            $translatableFields = [
                'name', 'slug', 'description','overview',
                'seo_title', 'seo_keywords', 'seo_description',

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $sub_specialtyField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($sub_specialtyField as $locale => $value) {
                    $sub_specialty->setTranslation($field, $locale, $value);
                }
            }

            // if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            //     $blog->addMediaFromRequest('banner')->toMediaCollection('banner');
            // }

            $sub_specialty->status = $request->status;
            $sub_specialty->country_id = $request->country_id;
            $sub_specialty->specialtie_id = $request->specialtie_id;
         
            $sub_specialty->robots = $request->robots;

            // Persist the doctor instance into the database
            $sub_specialty->save();
            // $blog->files()->attach($request->thumb, ['type' => 'thumb']);

            return response()->json($sub_specialty);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(SubSpecialty $subSpecialty)
    {
        // Prepare response data
        $responseData = $subSpecialty->toArray();
        return response()->json($responseData);
    }
    
    public function update(Request $request, SubSpecialty $subSpecialty)
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
                'name', 'slug', 'description','overview',
                'seo_title', 'seo_keywords', 'seo_description',
            ];
    
            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $subSpecialtyField = json_decode($request->$field, true);
    
                    // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }
    
                    foreach ($subSpecialtyField as $locale => $value) {
                        $subSpecialty->setTranslation($field, $locale, $value);
                    }
                }
            }
    

            $subSpecialty->status = $request->status;
            $subSpecialty->country_id = $request->country_id;
            $subSpecialty->specialtie_id = $request->specialtie_id;
            $subSpecialty->robots = $request->robots;

    
            $subSpecialty->save();
        
            // Prepare response data
            $responseData = $subSpecialty->toArray();
    
    
    
            return response()->json($responseData);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }
    
    public function softDelete($id)
    {
        $subSpecialty = SubSpecialty::find($id);
        if (!$subSpecialty) {
            return response()->json(['message' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        $subSpecialty->delete(); // Soft delete the country

        return response()->json(['message' => 'Item soft deleted successfully'], Response::HTTP_OK);
    }

    public function forceDelete($id)
    {
        $subSpecialty = SubSpecialty::withTrashed()->find($id);
        if (!$subSpecialty) {
            return response()->json(['message' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        if ($subSpecialty->trashed()) {
            $subSpecialty->forceDelete(); // Permanently delete the country
            return response()->json(['message' => 'Item permanently deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Item is not soft deleted'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getSubSpecialtyBySpecialtyid($specialty_id)
    {
        $subSpecialty = SubSpecialty::where('status',1)
                                    ->where('specialtie_id',$specialty_id)
                                    ->get()
            ->map(function ($val){
    
                return [
                    'id'=>$val->id,
                    'name' => $val->name ?? [],
                    'slug' => $val->slug ?? [],
                   
                ];
            });

        return response()->json([
            'data'=>$subSpecialty
        ]);

    }
}
