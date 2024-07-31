<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TestimonialController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAll']]);
    }

    public function index()
    {
        $data = Testimonial::all();
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
            $data = new Testimonial();

            // Define the translatable fields
            $translatableFields = [
                'title', 'description',

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


            $data->status = $request->status;
            $data->link = $request->link;
            $data->service_id = $request->service_id;
      

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
    public function show(Testimonial $testimonial)
    {
        return response()->json($testimonial);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
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
                    'title', 'description',
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
                            $testimonial->setTranslation($field, $locale, $value);
                        }
                    }
                }
    
                $testimonial->status = $request->status;
                $testimonial->link = $request->link;
                $testimonial->service_id = $request->service_id;
                
    
    
                $testimonial->save();
    
              
                // Prepare response data
                $responseData = $testimonial->toArray();
               
    
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
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json(null, 204);
    }


    public function getAll(Request $request)
    {
        $serviceId = $request->query('service_id');
        $serviceSlug = $request->query('service_slug');
    
        // Build the initial query with necessary relationships loaded
        $query = Testimonial::with('service', 'files')->where('status', 1);
    
        // Conditionally add filters to the query based on service_id or service_slug
        if ($serviceId && $serviceId !== 'all') {
            $query->whereHas('service', function ($q) use ($serviceId) {
                $q->where('id', $serviceId);
            });
        }
    
        if ($serviceSlug && $serviceSlug !== 'all') {
            $query->whereHas('service', function ($q) use ($serviceSlug) {
                $q->where('slug', 'like', '%' . $serviceSlug . '%');
            });
        }
    
        // Execute the query to get a collection of Testimonials
        $data = $query->get();
    
        // Group the results by service_id and format the output
        $groupedData = $data->groupBy('service_id')->map(function ($group) {
            return [
                'service' => [
                    'id' => $group->first()->service->id,
                    'name' => $group->first()->service->name,
                    'icon_tag' => $group->first()->service->icon_tag,
                    'svg' => $group->first()->service->svg,
                ],
                'testimonials' => $group->map(function ($val) {
                    return [
                        'id' => $val->id,
                        'title' => $val->title,
                        'description' => $val->description,
                        'link' => $val->link,
                    ];
                })->values(),
            ];
        })->values();
    
        return response()->json([
            'data' => $groupedData
        ]);
    }
    

}
