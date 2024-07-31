<?php

namespace App\Http\Controllers;

use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class TermsAndConditionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAllData']]);
    }

    public function index()
    {
        $data = TermsAndCondition::all();
        return response()->json($data);
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

            $data = new TermsAndCondition();

            $translatableFields = [
                'title', 'description',
            ];

            // Loop through each translatable field and set the translation
            foreach ($translatableFields as $field) {
                $dataField = json_decode($request->$field, true);

                // Validate English translation
                // if (!isset($faqField['en']) || !is_string($faqField['en'])) {
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

    public function update(Request $request, TermsAndCondition $termsandcondition)
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
                    $dataField = json_decode($request->$field, true);

                    // if (!isset($faqField['en']) || !is_string($faqField['en'])) {
                    //     return response()->json([
                    //         'status' => 'error',
                    //         'message' => 'Validation failed',
                    //         'errors' => ['English ' . $field . ' is required and must be a string'],
                    //     ], 422);
                    // }

                    foreach ($dataField as $locale => $value) {
                        $termsandcondition->setTranslation($field, $locale, $value);
                    }
                }
            }

           
            $termsandcondition->save();

            // Prepare response data
            $responseData = $termsandcondition->toArray();


            return response()->json($responseData);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    public function show(TermsAndCondition $termsandcondition)
    {

        // Prepare response data
        $responseData = $termsandcondition->toArray();
        return response()->json($responseData);
    }

    public function destroy(TermsAndCondition $termsandcondition)
    {
        $termsandcondition->delete();

        // Return a success message in JSON format
        return response()->json(['message' => 'Data successfully deleted'], 200);
    }

    public function getAllData()
    {
        // app()->setLocale($lang);

        // Retrieve FAQs with their categories
        $data = TermsAndCondition::get();

        // Group FAQs by category_id
        $groupedFaqs = $data->map(function ($group) {
            return [
                'data' => $group->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'title' => $faq->title,
                        'description' => $faq->description,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $groupedFaqs->values()
        ]);
    }
}
