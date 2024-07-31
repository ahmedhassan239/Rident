<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAll', 'getSingle','getFeatured']]);
    }
    public function index(Request $request)
    {
        // Check if a country_id is provided
        // $countryId = $request->header('country_id');
        // if (!is_null($countryId)) {

            // Filter specialties by the provided country_id
            $doctors = Doctor::with('files')->get();
        // } else {
        //     // Return all specialties if no country_id is provided
        //     $doctors = Doctor::with('country','files','specialty')->get();
        // }

        return response()->json($doctors);
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
            $doctor = new Doctor();

            // Define the translatable fields
            $translatableFields = [
                'name', 'slug',
                'description', 'overview', 'seo_title', 'seo_keywords', 'seo_description',
            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $doctorField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($doctorField as $locale => $value) {
                    $doctor->setTranslation($field, $locale, $value);
                }
            }

            // if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            //     $blog->addMediaFromRequest('banner')->toMediaCollection('banner');
            // }


            $doctor->status = $request->status;
            $doctor->featured = $request->featured;
            // $doctor->country_id = $request->country_id;
            // $doctor->governorate_id  = $request->governorate_id;
            // $doctor->area_id  = $request->area_id;
            // $doctor->specialtie_id   = $request->specialtie_id;
            // $doctor->sub_specialtie  = $request->sub_specialtie;
            // $doctor->healthcare_provider_id  = $request->healthcare_provider_id ;
            // $doctor->insurance = $request->insurance;
            $doctor->robots = $request->robots;
            // $doctor->waiting_time = $request->waiting_time;
            // $doctor->fees = $request->fees;

            // Persist the doctor instance into the database
            $doctor->save();
            $doctor->files()->attach($request->thumb, ['type' => 'thumb']);
            // $doctor->files()->attach($request->banner, ['type' => 'banner']);
            $gallery = json_decode($request->gallery, true);
            if (is_array($gallery)) {
                foreach ($gallery as $galleryId) {
                    $doctor->files()->attach($galleryId, ['type' => 'gallery']);
                }
            }

            return response()->json($doctor);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(Doctor $doctor)
    {
        // Initialize IDs and URLs
        $bannerId = null;
        $thumbId = null;
        $bannerUrl = '';
        $thumbUrl = '';

        // Loop through the files to find banner and thumb
        foreach ($doctor->files as $file) {
            if ($file->pivot->type == 'banner') {
                $bannerId = $file->id;  // Store the banner ID
                $bannerUrl = $file->file_url;
            } elseif ($file->pivot->type == 'thumb') {
                $thumbId = $file->id;  // Store the thumb ID
                $thumbUrl = $file->file_url;
            }
        }

        // Prepare response data
        $responseData = $doctor->toArray();
        $responseData['banner_id'] = $bannerId;
        $responseData['thumb_id'] = $thumbId;
        $responseData['banner_url'] = $bannerUrl;
        $responseData['thumb_url'] = $thumbUrl;
        unset($responseData['files']);

        return response()->json($responseData);
    }

    public function update(Request $request, Doctor $doctor)
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
                'name', 'slug',
                'description', 'overview', 'seo_title', 'seo_keywords', 'seo_description',
            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $doctorField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogField['en']) || !is_string($blogField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($doctorField as $locale => $value) {
                    $doctor->setTranslation($field, $locale, $value);
                }
            }

            // if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            //     $blog->addMediaFromRequest('banner')->toMediaCollection('banner');
            // }

            $doctor->status = $request->status;
            $doctor->featured = $request->featured;
            // $doctor->country_id = $request->country_id;
            // $doctor->governorate_id  = $request->governorate_id;
            // $doctor->area_id  = $request->area_id;
            // $doctor->specialtie_id   = $request->specialtie_id;
            // $doctor->sub_specialtie  = $request->sub_specialtie;
            // $doctor->healthcare_provider_id  = $request->healthcare_provider_id ;
            // $doctor->insurance = $request->insurance;
            $doctor->robots = $request->robots;
            // $doctor->waiting_time = $request->waiting_time;
            // $doctor->fees = $request->fees;


            // Persist the doctor instance into the database
            $doctor->save();

            $doctor->files()->detach();

            // $doctor->files()->attach($request->banner, ['type' => 'banner']);
            $doctor->files()->attach($request->thumb, ['type' => 'thumb']);

            // Retrieve banner and thumb URLs
            $doctor->load('files');
            // dd($blog->load('files'));// Reload the files relationship
            $bannerId = null;
            $thumbId = null;
            $bannerUrl = '';
            $thumbUrl = '';

            // Loop through the files to find banner and thumb
            foreach ($doctor->files as $file) {
               if ($file->pivot->type == 'thumb') {
                    $thumbId = $file->id;  // Store the thumb ID
                    $thumbUrl = $file->file_url;
                }
            }

            // Prepare response data
            $responseData = $doctor->toArray();
            // $responseData['banner_id'] = $bannerId;
            $responseData['thumb_id'] = $thumbId;
            // $responseData['banner_url'] = $bannerUrl;
            $responseData['thumb_url'] = $thumbUrl;
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

    public function softDelete($id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json(['message' => 'Doctor Provider not found'], Response::HTTP_NOT_FOUND);
        }

        $doctor->delete(); // Soft delete the country

        return response()->json(['message' => 'Doctor Provider soft deleted successfully'], Response::HTTP_OK);
    }

    public function forceDelete($id)
    {
        $doctor = Doctor::withTrashed()->find($id);
        if (!$doctor) {
            return response()->json(['message' => 'Doctor Provider not found'], Response::HTTP_NOT_FOUND);
        }

        if ($doctor->trashed()) {
            $doctor->forceDelete(); // Permanently delete the country
            return response()->json(['message' => 'Doctor Provider permanently deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Doctor Provider is not soft deleted'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAll()
    {
        // app()->setLocale($lang);

        $data = Doctor::where('status', 1)->get()
            ->map(function ($val) {
                $thumb = '';
                foreach ($val->files as $file) {
                    if ($file->pivot->type == 'thumb') {
                        $thumb = $file->file_url;
                    }
                }
                return [
                    'id' => $val->id,
                    'name' => $val->name ?? [],
                    'slug' => $val->slug ?? [],
                    'description' => $val->description ?? [],
                    'alt' => $val->name,
                    'thumb' => $thumb,
                    // 'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $val->created_at)->isoFormat('MMM Do YY'),

                ];
            });

        return response()->json([
            'data' => $data
        ]);
    }

    public function getFeatured()
    {
        // $lang = app()->getLocale();

        $data = Doctor::where('status', 1)->where('featured', 1)->get()
            ->map(function ($val) {
                $thumb = '';
                foreach ($val->files as $file) {
                    if ($file->pivot->type == 'thumb') {
                        $thumb = $file->file_url;
                    }
                }
                return [
                    'id' => $val->id,
                    'name' => $val->name ?? [],
                    'slug' => $val->slug ?? [],
                    'description' => $val->description ?? [],
                    'alt' => $val->name,
                    'thumb' => $thumb,
                  

                ];
            });

        return response()->json([
            'data' => $data
        ]);
    }

    public function getSingle($id)
    {
        // app()->setLocale($lang);
        $lang = app()->getLocale();
        

        // Try to find by ID first
        $value = Doctor::where('status', 1)->where('id', $id)->with('files')->first();

        // If not found, try to find by slug
        if (!$value) {
            $value = Doctor::where('status', 1)
                ->where("slug->$lang", $id)
                ->with('files')
                ->firstOrFail();
        }

        $banner = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'banner') {
                $banner = $file->file_url;
            }
        }


        $thumb = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'thumb') {
                $thumb = $file->file_url;
            }
        }
        $data[] = [
            'id' => $value->id,
            'name' => $value->name,
            'slug' => $value->slug,
            'overview' => $value->overview,
            'banner' => $banner,
            'thumb' => $thumb,
            'alt' => $value->name,
            'seo' => [
                'title' => $value->seo_title,
                'keywords' => $value->seo_keywords,
                'description' => $value->seo_description,
                'robots' => $value->robots,
                'facebook_title' => $value->seo_title,
                'facebook_description' => $value->seo_description,
                'twitter_title' => $value->seo_title,
                'twitter_description' => $value->seo_description,
                'twitter_image' => $thumb,
                'facebook_image' => $thumb,
            ],
        ];
        return response()->json([
            'data' => $data,
        ], '200');
    }


}
