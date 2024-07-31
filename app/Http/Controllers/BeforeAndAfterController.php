<?php

namespace App\Http\Controllers;

use App\Models\BeforeAndAfter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BeforeAndAfterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAll', 'getFeatured']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $val = BeforeAndAfter::with('files')->get();
        return response()->json($val);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
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
            $val = new BeforeAndAfter();

            // Define the translatable fields
            $translatableFields = [
                'description',
            ];



            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $valField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($homeField['en']) || !is_string($homeField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }


                if (is_array($valField) || is_object($valField)) {
                    foreach ($valField as $locale => $value) {
                        $val->setTranslation($field, $locale, $value);
                    }
                }
            }
            $val->status = $request->status;
            $val->featured = $request->featured;
            $val->service_id = $request->service_id;
            $val->save();

            // Persist the doctor instance into the database
            $val->save();
            $val->files()->attach($request->before, ['type' => 'before', 'service_id' => $request->service_id]);
            $val->files()->attach($request->after, ['type' => 'after', 'service_id' => $request->service_id]);

            return response()->json($val);
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
    public function show(BeforeAndAfter $baf)
    {
        // Initialize IDs and URLs
        $beforeId = null;
        $afterId = null;
        $beforeUrl = '';
        $afterUrl = '';

        // Loop through the files to find banner and thumb
        foreach ($baf->files as $file) {
            if ($file->pivot->type == 'before') {
                $beforeId = $file->id;  // Store the banner ID
                $beforeUrl = $file->file_url;
            } elseif ($file->pivot->type == 'after') {
                $afterId = $file->id;  // Store the thumb ID
                $afterUrl = $file->file_url;
            }
        }

        // Prepare response data
        $responseData = $baf->toArray();
        $responseData['before_id'] = $beforeId;
        $responseData['after_id'] = $afterId;
        $responseData['before_url'] = $beforeUrl;
        $responseData['after_url'] = $afterUrl;
        unset($responseData['files']);

        return response()->json($responseData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BeforeAndAfter $baf)
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
                'description',
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $bafField = json_decode($request->$field, true);

                    // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($bafField as $locale => $value) {
                        $baf->setTranslation($field, $locale, $value);
                    }
                }
            }

            $baf->status = $request->status;
            $baf->featured = $request->featured;
            $baf->service_id = $request->service_id;



            $baf->save();

            // Detach existing files and attach new ones
            // if()
            $baf->files()->detach();

            $baf->files()->attach($request->before, ['type' => 'before', 'service_id' => $request->service_id]);
            $baf->files()->attach($request->after, ['type' => 'after', 'service_id' => $request->service_id]);

            // Retrieve banner and thumb URLs
            $baf->load('files');
            // dd($blog->load('files'));// Reload the files relationship
            $beforeId = null;
            $afterId = null;
            $beforeUrl = '';
            $afterUrl = '';

            // Loop through the files to find banner and thumb
            foreach ($baf->files as $file) {
                if ($file->pivot->type == 'before') {
                    $beforeId = $file->id;  // Store the banner ID
                    $beforeUrl = $file->file_url;
                } elseif ($file->pivot->type == 'after') {
                    $afterId = $file->id;  // Store the thumb ID
                    $afterUrl = $file->file_url;
                }
            }

            // Prepare response data
            $responseData = $baf->toArray();
            $responseData['before_id'] = $beforeId;
            $responseData['after_id'] = $afterId;
            $responseData['before_url'] = $beforeUrl;
            $responseData['after_url'] = $afterUrl;
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
    public function destroy(BeforeAndAfter $baf)
    {
        $baf->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'B A F successfully deleted'], 200);
    }

    public function getAll(Request $request)
    {
        $serviceId = $request->query('service_id');
        $serviceSlug = $request->query('service_slug');

        // Build the initial query with the necessary relationships loaded
        $query = BeforeAndAfter::where('status', 1)->with('service', 'files');

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

        // Execute the query to get a collection of BeforeAndAfter records
        $data = $query->get();

        // Group the results by service_id and structure the output
        $groupedBafs = $data->groupBy('service_id')->map(function ($group) {
            return [
                'service' => [
                    'id' => $group->first()->service->id,
                    'name' => $group->first()->service->name,
                    'slug' => $group->first()->service->slug,
                    'icon_tag' => $group->first()->service->icon_tag,
                    'svg' => $group->first()->service->svg,
                ],
                'baf' => $group->map(function ($val) {
                    $before = '';
                    $after = '';
                    foreach ($val->files as $file) {
                        if ($file->pivot->type == 'before') {
                            $before = $file->file_url;
                        }
                        if ($file->pivot->type == 'after') {
                            $after = $file->file_url;
                        }
                    }
                    return [
                        'id' => $val->id,
                        'description' => $val->description,
                        'before' => $before,
                        'after' => $after,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'data' => $groupedBafs
        ]);
    }



    public function getFeatured(Request $request)
    {
        $serviceId = $request->query('service_id');
        $serviceSlug = $request->query('service_slug');

        // Build the initial query with the necessary relationships loaded
        $query = BeforeAndAfter::where('status', 1)->where('featured', 1)->with('service', 'files');

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

        // Execute the query to get a collection of BeforeAndAfter records
        $data = $query->get();

        // Group the results by service_id and structure the output
        $groupedBafs = $data->groupBy('service_id')->map(function ($group) {
            return [
                'service' => [
                    'id' => $group->first()->service->id,
                    'name' => $group->first()->service->name,
                    'slug' => $group->first()->service->slug,
                    'icon_tag' => $group->first()->service->icon_tag,
                    'svg' => $group->first()->service->svg,
                ],
                'baf' => $group->map(function ($val) {
                    $before = '';
                    $after = '';
                    foreach ($val->files as $file) {
                        if ($file->pivot->type == 'before') {
                            $before = $file->file_url;
                        }
                        if ($file->pivot->type == 'after') {
                            $after = $file->file_url;
                        }
                    }
                    return [
                        'id' => $val->id,
                        'description' => $val->description,
                        'before' => $before,
                        'after' => $after,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'data' => $groupedBafs
        ]);
    }
}
