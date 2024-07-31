<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;


class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getSingleContact']]);
    }

    public function index()
    {
        $contact = Contact::all();
        return response()->json($contact);
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            // $request->validate([
            //     'name' => 'required|json',
            //     'slug' => 'required|json',
            //     'description' => 'required|json',
            //     'overview' => 'required|json',
            //     'seo_title' => 'nullable|json',
            //     'seo_keywords' => 'nullable|json',
            //     'seo_description' => 'nullable|json'
            // ]);

            // Initialize a new doctor instance
            $contact = new Contact();

            // Define the translatable fields
            $translatableFields = [
                'address1', 'address2', 'phone1', 'phone2', 'location1', 'location2',
                'email1', 'email2', 'facebook', 'linkedin', 'twitter', 'snapchat',
                'instagram', 'youtube'

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $contactField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($blogFcontactFieldield['en']) || !is_string($contactField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($contactField as $locale => $value) {
                    $contact->setTranslation($field, $locale, $value);
                }
            }

            // if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            //     $blog->addMediaFromRequest('banner')->toMediaCollection('banner');
            // }


            // Persist the doctor instance into the database
            $contact->save();
            // $blog->files()->attach($request->banner, ['type' => 'banner']);
            // $blog->files()->attach($request->thumb, ['type' => 'thumb']);

            return response()->json($contact);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    // public function show(Blog $blog)
    // {
    //     $bannerUrl = '';
    //     $thumbUrl = '';
    //     foreach ($blog->files as $file) {
    //         if ($file->pivot->type == 'banner') {
    //             $bannerUrl = $file->file_url;
    //         } elseif ($file->pivot->type == 'thumb') {
    //             $thumbUrl = $file->file_url;
    //         }
    //     }
    //     $responseData = $blog->toArray();
    //         $responseData['banner'] = $bannerUrl;
    //         $responseData['thumb'] = $thumbUrl;
    //         unset($responseData['files']);
    //     return response()->json($responseData);
    // }

    public function update(Request $request, Contact $contact)
    {
        try {
            // $validatedData = $request->validate([
            //     'name' => 'sometimes|required|json',
            //     'slug' => 'sometimes|required|json',
            //     'description' => 'sometimes|required|json',
            //     'overview' => 'sometimes|required|json',
            //     'seo_title' => 'nullable|json',
            //     'seo_keywords' => 'nullable|json',
            //     'seo_description' => 'nullable|json',
            //     'status' => 'sometimes|required', // Assuming status is required
            //     'featured' => 'sometimes|required', // Assuming featured is required
            //     'banner' => 'sometimes|required', // Assuming banner file ID is required
            //     'thumb' => 'sometimes|required', // Assuming thumb file ID is required
            // ]);

            $translatableFields = [
                'address1', 'address2', 'phone1', 'phone2', 'location1', 'location2',
                'email1', 'email2', 'facebook', 'linkedin', 'twitter', 'snapchat',
                'instagram', 'youtube'
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $contactField = json_decode($request->$field, true);

                    // if (!isset($contactField['en']) || !is_string($contactField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($contactField as $locale => $value) {
                        $contact->setTranslation($field, $locale, $value);
                    }
                }
            }

            // $blog->status = $request->status;
            // $blog->featured = $request->featured;

            $contact->save();

            // Detach existing files and attach new ones
            // $blog->files()->detach();
            // $blog->files()->attach($request->banner, ['type' => 'banner']);
            // $blog->files()->attach($request->thumb, ['type' => 'thumb']);

            // Retrieve banner and thumb URLs
            // $blog->load('files'); // Reload the files relationship
            // $bannerUrl = '';
            // $thumbUrl = '';
            // foreach ($blog->files as $file) {
            //     if ($file->pivot->type == 'banner') {
            //         $bannerUrl = $file->file_url;
            //     } elseif ($file->pivot->type == 'thumb') {
            //         $thumbUrl = $file->file_url;
            //     }
            // }

            // Prepare the response data
            $responseData = $contact->toArray();
            // $responseData['banner'] = $bannerUrl;
            // $responseData['thumb'] = $thumbUrl;
            // unset($responseData['files']); // Remove the files array

            return response()->json($responseData);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Contact successfully deleted'], 200);
    }



    public function getSingleContact($id)
    {
        $value = Contact::where('id', $id)->firstOrFail();
        $data[] = [
            'address1' => $value->address1,
            'address2' => $value->address2,
            'phone1' => $value->phone1,
            'phone2' => $value->phone2,
            'location1' => $value->location1,
            'location2' => $value->location2,
            'email1' => $value->email1,
            'email2' => $value->email2,
            'facebook' => $value->facebook,
            'linkedin' => $value->linkedin,
            'twitter' => $value->twitter,
            'snapchat' => $value->snapchat,
            'instagram' => $value->instagram,
            'youtube' => $value->youtube
        ];
        return response()->json([
            'data' => $data,
        ], '200');
    }
}
