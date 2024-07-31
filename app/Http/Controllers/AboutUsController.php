<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AboutUsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getSingleAboutus']]);
    }
    public function index()
    {
        $aboutus = AboutUs::all();
        return response()->json($aboutus);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {
            // Validate the incoming request
            $request->validate([
                // 'name' => 'required|json',
                // 'slug' => 'required|json',
                // 'mission' => 'required|json',
                // 'vision' => 'required|json',
                // 'philosophy' => 'required|json',
                // 'weserve' => 'required|json',
                // 'seo_title' => 'nullable|json',
                // 'seo_keywords' => 'nullable|json',
                // 'seo_description' => 'nullable|json'
            ]);

            // Initialize a new doctor instance
            $aboutus = new AboutUs();

            // Define the translatable fields
            $translatableFields = [
                'name', 'slug', 'mission',
                'vision', 'wy_choose_us','title1','title2','des1','des2', 'about_us',
                'seo_title', 'seo_keywords', 'seo_description'

            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $aboutUsField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($aboutUsField['en']) || !is_string($aboutUsField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($aboutUsField as $locale => $value) {
                    $aboutus->setTranslation($field, $locale, $value);
                }
            }
            $aboutus->robots = $request->robots;
            $aboutus->num1 = $request->num1;
            $aboutus->num2 = $request->num2;
            $aboutus->num3 = $request->num3;
            $aboutus->num4 = $request->num4;
            $aboutus->save();
            $aboutus->files()->attach($request->banner, ['type' => 'banner']);
            $aboutus->files()->attach($request->thumb, ['type' => 'thumb']);
            $aboutus->files()->attach($request->image1, ['type' => 'image1']);
            $aboutus->files()->attach($request->image2, ['type' => 'image2']);

            return response()->json($aboutus);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(AboutUs $aboutu)
    {
        // Initialize IDs and URLs
        $bannerId = null;
        $thumbId = null;
        $image1Id = null;
        $image2Id = null;
        $bannerUrl = '';
        $thumbUrl = '';
        $image1Url = '';
        $image2Url = '';

        // Loop through the files to find banner and thumb
        foreach ($aboutu->files as $file) {
            if ($file->pivot->type == 'banner') {
                $bannerId = $file->id;  // Store the banner ID
                $bannerUrl = $file->file_url;
            } elseif ($file->pivot->type == 'thumb') {
                $thumbId = $file->id;  // Store the thumb ID
                $thumbUrl = $file->file_url;
            }elseif ($file->pivot->type == 'image1') {
                $image1Id = $file->id;  // Store the thumb ID
                $image1Url = $file->file_url;
            }elseif ($file->pivot->type == 'image2') {
                $image2Id = $file->id;  // Store the thumb ID
                $image2Url = $file->file_url;
            }
        }

        // Prepare response data
        $responseData = $aboutu->toArray();
        $responseData['banner_id'] = $bannerId;
        $responseData['thumb_id'] = $thumbId;
        $responseData['image_1_id'] = $image1Id;
        $responseData['image_2_id'] = $image2Id;
        
        $responseData['banner_url'] = $bannerUrl;
        $responseData['thumb_url'] = $thumbUrl;
        $responseData['image_1_url'] = $image1Url;
        $responseData['image_2_url'] = $image2Url;
      
        unset($responseData['files']);

        return response()->json($responseData);
    }
    public function update(Request $request, AboutUs $aboutu)
    {
        try {
            $validatedData = $request->validate([
                // 'name' => 'required|json',
                // 'slug' => 'required|json',
                // 'mission' => 'sometimes|required|json',
                // 'vision' => 'sometimes|required|json',
                // 'philosophy' => 'sometimes|required|json',
                // 'weserve' => 'sometimes|required|json',
                // 'seo_title' => 'nullable|json',
                // 'seo_keywords' => 'nullable|json',
                // 'seo_description' => 'nullable|json',
                // 'banner' => 'required',
            ]);

            $translatableFields = [
                'name', 'slug', 'mission',
                'vision', 'wy_choose_us','title1','title2','des1','des2', 'about_us',
                'seo_title', 'seo_keywords', 'seo_description'
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $aboutusField = json_decode($request->$field, true);

                    // if (!isset($aboutusField['en']) || !is_string($aboutusField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($aboutusField as $locale => $value) {
                        $aboutu->setTranslation($field, $locale, $value);
                    }
                }
            }
            $aboutu->robots = $request->robots;
            $aboutu->num1 = $request->num1;
            $aboutu->num2 = $request->num2;
            $aboutu->num3 = $request->num3;
            $aboutu->num4 = $request->num4;
            $aboutu->save();

            // Detach existing files and attach new ones
            $aboutu->files()->detach();
            $aboutu->files()->attach($request->banner, ['type' => 'banner']);
            $aboutu->files()->attach($request->thumb, ['type' => 'thumb']);
            $aboutu->files()->attach($request->image1, ['type' => 'image1']);
            $aboutu->files()->attach($request->image2, ['type' => 'image2']);

            // Retrieve banner and thumb URLs
            $aboutu->load('files'); // Reload the files relationship
            $bannerId = null;
            $thumbId = null;
            $image1Id = null;
            $image2Id = null;
            $bannerUrl = '';
            $thumbUrl = '';
            $image1Url = '';
            $image2Url = '';

            // Loop through the files to find banner and thumb
            foreach ($aboutu->files as $file) {
                if ($file->pivot->type == 'banner') {
                    $bannerId = $file->id;  // Store the banner ID
                    $bannerUrl = $file->file_url;
                } elseif ($file->pivot->type == 'thumb') {
                    $thumbId = $file->id;  // Store the thumb ID
                    $thumbUrl = $file->file_url;
                }elseif ($file->pivot->type == 'image1') {
                    $image1Id = $file->id;  // Store the thumb ID
                    $image1Url = $file->file_url;
                }elseif ($file->pivot->type == 'image2') {
                    $image2Id = $file->id;  // Store the thumb ID
                    $image2Url = $file->file_url;
                }
            }

            // Prepare response data
            $responseData = $aboutu->toArray();
            $responseData['banner_id'] = $bannerId;
            $responseData['thumb_id'] = $thumbId;
            $responseData['image_1_id'] = $image1Id;
            $responseData['image_2_id'] = $image2Id;
            
            $responseData['banner_url'] = $bannerUrl;
            $responseData['thumb_url'] = $thumbUrl;
            $responseData['image_1_url'] = $image1Url;
            $responseData['image_2_url'] = $image2Url;
          
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


    public function destroy(AboutUs $aboutu)
    {
        $aboutu->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Successfully deleted'], 200);
    }

    public function getSingleAboutus($id)
    {
        $lang = app()->getLocale();

        $value = AboutUs::where('id', $id)->with('files')->first();

        // If not found, try to find by slug
        if (!$value) {
            $value = AboutUs::where("slug->$lang", $id)
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

        $image1 = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'image1') {
                $image1 = $file->file_url;
            }
        }
        $image2 = '';
        foreach ($value->files as $file) {
            if ($file->pivot->type == 'image2') {
                $image2 = $file->file_url;
            }
        }

        
        $data[] = [
            'id' => $value->id,
            'name' => $value->name,
            'slug' => $value->slug,
            'mission' => $value->mission,
            'vision' => $value->vision,
            'wy_choose_us' => $value->wy_choose_us,
            'wy_choose_us_images' => [
                'image1'=>$image1,
                'image2'=>$image2,
            ],
            'wy_choose_us_data' => [
                'title1'=>$value->title1,
                'des1'=>$value->des1,
                'title2'=>$value->title2,
                'des2'=>$value->des2,
            ],
            'about_us' => $value->about_us,
            'num1' => $value->num1,
            'num2' => $value->num2,
            'num3' => $value->num3,
            'num4' => $value->num4,
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
                'twitter_image' => $thumb,
                'facebook_image' => $thumb,
            ],
        ];
        return response()->json([
            'data' => $data,
        ], '200');
    }
}
