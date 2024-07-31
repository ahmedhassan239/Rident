<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class FaqController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['getAllFaqs']]);
    }

    public function index()
    {
        $faq = Faq::all();
        return response()->json($faq);
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                // 'title' => 'required|json',
                // 'description' => 'required|json',
                // 'category_id' => 'required',
            ]);

            $faq = new Faq();

            $translatableFields = [
                'title', 'description',
            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $faqField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($faqField['en']) || !is_string($faqField['en'])) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Validation failed',
                //         'errors' => ['English ' . $field . ' is required and must be a string'],
                //     ], 422);
                // }

                foreach ($faqField as $locale => $value) {
                    $faq->setTranslation($field, $locale, $value);
                }
            }

            $faq->service_id = $request->service_id;

            $faq->save();


            return response()->json($faq);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function update(Request $request, Faq $faq)
    {
        try {
            $validatedData = $request->validate([
                // 'title' => 'sometimes|required|json',
                // 'description' => 'sometimes|required|json',
                // 'category_id' => 'sometimes|required',
            ]);

            $translatableFields = [
                'title', 'description',
            ];

            foreach ($translatableFields as $field) {
                if ($request->has($field)) {
                    $faqField = json_decode($request->$field, true);

                    // if (!isset($faqField['en']) || !is_string($faqField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($faqField as $locale => $value) {
                        $faq->setTranslation($field, $locale, $value);
                    }
                }
            }

            $faq->service_id = $request->service_id;

            $faq->save();

            // Prepare response data
            $responseData = $faq->toArray();


            return response()->json($responseData);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(Faq $faq)
    {

        // Prepare response data
        $responseData = $faq->toArray();
        return response()->json($responseData);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Faq successfully deleted'], 200);
    }

    // public function getAllFaqs($lang)
    // {
    //     app()->setLocale($lang);

    //     $faqs = Faq::with('category')->get()
    //         ->map(function ($val){

    //             return [
    //                 'id'=>$val->id,
    //                 'title' => $val->title ?? [],
    //                 'description' => $val->description ?? [],
    //                 'category'=>[
    //                     'id'=>$val->category->id,
    //                     'name'=>$val->category->name,
    //                     'slug'=>$val->category->slug,
    //                 ]
    //             ];
    //         });

    //     return response()->json([
    //         'data'=>$faqs
    //     ]);

    // }

    public function getAllFaqs(Request $request)
    {
        $serviceId = $request->query('service_id');
        $serviceSlug = $request->query('service_slug');

        // Start building the query with the relationship loaded
        $query = Faq::with('service');

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

        // Execute the query to get a collection of FAQs
        $faqs = $query->get();

        // Group FAQs by service_id using the collection's groupBy method
        $groupedFaqs = $faqs->groupBy('service_id')->map(function ($group) {
            if ($group->isEmpty()) {
                return null;
            }
            $service = $group->first()->service;
            return [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,  // Optionally include slug if useful for clients
                    'icon_tag' => $service->icon_tag,  // Optionally include slug if useful for clients
                    'svg' => $service->svg,  // Optionally include slug if useful for clients
                ],
                'faqs' => $group->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'title' => $faq->title,
                        'description' => $faq->description,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $groupedFaqs->filter()->values()  // Ensure to remove any nulls and reindex
        ]);
    }
}
