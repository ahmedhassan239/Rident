<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentDetailsToAdmin;
use App\Mail\AppointmentRequestToPatient;
use App\Mail\AppointmentRequestToPatientAr;
use App\Models\Enquiry;
use App\Models\ScheduleDayTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
    }
    public function index()
    {
        // Eager load the service and scheduleDayTime relationships
        $enquiries = Enquiry::with(['service', 'scheduleDayTime', 'scheduleDayTimeSlot'])->get();

        //    dd( $enquiries);


        // Transform the enquiries to include the service name and slot times
        $transformed = $enquiries->map(function ($enquiry) {
            return [
                'id' => $enquiry->id,
                'name' => $enquiry->name,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'date' => $enquiry->date,
                'branch' => $enquiry->branch,
                'status' => $enquiry->status,
                'age' => $enquiry->age,
                'country' => $enquiry->country,
                'service_name' => $enquiry->service ? $enquiry->service->name : null,
                'source' => $enquiry->source,
                'slot_start_from' => $enquiry->scheduleDayTimeSlot ? $enquiry->scheduleDayTimeSlot->start_from : null,
                'slot_end_to' => $enquiry->scheduleDayTimeSlot ? $enquiry->scheduleDayTimeSlot->end_to : null,
                'created_at' => $enquiry->created_at,

            ];
        });

        return response()->json($transformed);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'email' => 'email', // Make email required
                'phone' => 'required',
                'date' => 'required',
                'branch' => 'required',
                'service_id' => 'required|integer',
                'slot_id' => 'required|integer',
                'lang' => 'required',
                'source' => 'nullable',
                // 'age' => 'required',
                'country' => 'required',
            ]);
    
            // Store the enquiry and check if it is created
            $enquiry = Enquiry::create($validated);
            if (!$enquiry) {
                // Handle the case when the enquiry is not created
                return response()->json([
                    'message' => 'Failed to create the enquiry'
                ], 400);
            }
    
            // Check if the email is valid before sending emails
            if (!empty($request->email)) {
                // Choose the appropriate Mailable based on language
                $mailable = $request->lang == "en" ? new AppointmentRequestToPatient($enquiry) : new AppointmentRequestToPatientAr($enquiry);
    
                // Send email to patient
                Mail::to($request->email)->send($mailable);
    
                // Send email to admin
                Mail::to('admin@ridentdentalcenters.com')->send(new AppointmentDetailsToAdmin($enquiry));
            }
    
            // Return the created enquiry
            return response()->json($enquiry, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return a JSON response with errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // General error handling (optional)
            return response()->json([
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    public function show(Enquiry $enquiry)
    {
        return response()->json($enquiry);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([

            'status' => 'nullable'
        ]);

        $enquiry->update($validated);
        return response()->json($enquiry);
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        return response()->json(null, 204);
    }
}
