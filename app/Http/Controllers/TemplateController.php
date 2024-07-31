<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;


class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getSingleTemplate']]);
    }

    public function index()
    {
        $templates = Template::all();
        return response()->json($templates);
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                // 'name' => 'required|json',
                // 'slug' => 'required|json',
                // 'description' => 'required|json',
                // 'seo_title' => 'nullable|json',
                // 'seo_keywords' => 'nullable|json',
                // 'seo_description' => 'nullable|json'
            ]);

            // Initialize a new doctor instance
            $template = new Template();

            // Define the translatable fields
            $translatableFields = [
                'name', 'slug', 'description',
                'seo_title', 'seo_keywords', 'seo_description',

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $templateField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($templateField['en']) || !is_string($templateField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($templateField as $locale => $value) {
                    $template->setTranslation($field, $locale, $value);
                }
            }

            if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                $template->addMediaFromRequest('banner')->toMediaCollection('banner');
            }


            $template->robots = $request->robots;
            // Persist the doctor instance into the database
            $template->save();
            $template->files()->attach($request->banner, ['type' => 'banner']);

            return response()->json($template);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(Template $template)
    {
        $bannerId = null;

        $bannerUrl = '';


        // Loop through the files to find banner and thumb
        foreach ($template->files as $file) {
            if ($file->pivot->type == 'banner') {
                $bannerId = $file->id;  // Store the banner ID
                $bannerUrl = $file->file_url;
            }
        }

        // Prepare response data
        $responseData = $template->toArray();
        $responseData['banner_id'] = $bannerId;
        $responseData['banner_url'] = $bannerUrl;
        unset($responseData['files']);
        return response()->json($responseData);
    }

    public function update(Request $request, Template $template)
    {
        try {
            $validatedData = $request->validate([
                // 'name' => 'required|json',
                // 'slug' => 'required|json',
                // 'description' => 'sometimes|required|json',
                // 'seo_title' => 'nullable|json',
                // 'seo_keywords' => 'nullable|json',
                // 'seo_description' => 'nullable|json',
                // 'banner' => 'required', // Assuming banner file ID is required

            ]);

            $translatableFields = [
                'name', 'slug', 'description',
                'seo_title', 'seo_keywords', 'seo_description',
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $templateField = json_decode($request->$field, true);

                    // if (!isset($templateField['en']) || !is_string($templateField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($templateField as $locale => $value) {
                        $template->setTranslation($field, $locale, $value);
                    }
                }
            }

            $template->robots = $request->robots;
            $template->save();

            // Detach existing files and attach new ones
            $template->files()->detach();
            $template->files()->attach($request->banner, ['type' => 'banner']);

            // Retrieve banner and thumb URLs
            $template->load('files'); // Reload the files relationship
            $bannerId = null;

            $bannerUrl = '';


            // Loop through the files to find banner and thumb
            foreach ($template->files as $file) {
                if ($file->pivot->type == 'banner') {
                    $bannerId = $file->id;  // Store the banner ID
                    $bannerUrl = $file->file_url;
                }
            }

            // Prepare response data
            $responseData = $template->toArray();
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

    public function destroy(Template $template)
    {
        $template->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Template successfully deleted'], 200);
    }

    public function getSingleTemplate($id)
    {
        $lang = app()->getLocale();

        $value = Template::where('id', $id)->with('files')->first();

        // If not found, try to find by slug
        if (!$value) {
            $value = Template::where("slug->$lang", $id)
                ->with('files')
                ->firstOrFail();
        }
        $banner = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'banner') {
                $banner = $file->file_url;
            }
        }


        $data[] = [
            'id' => $value->id,
            'name' => $value->name,
            'slug' => $value->slug,
            'description' => $value->description,
            'banner' => $banner,
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
                'twitter_image' => $banner,
                'facebook_image' => $banner,
            ],
        ];
        return response()->json([
            'data' => $data,
        ], '200');
    }
}
