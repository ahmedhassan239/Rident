<?php

namespace App\Http\Controllers;

use App\Models\ContactForm;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
    }
    public function index()
    { 
       // Eager load the service and scheduleDayTime relationships
       $enquiries = ContactForm::with(['service'])->get();
    //    dd($enquiries);

       // Transform the enquiries to include the service name and slot times
       $transformed = $enquiries->map(function ($enquiry) {
           return [
               'id' => $enquiry->id,
               'name' => $enquiry->name,
               'email' => $enquiry->email,
               'phone' => $enquiry->phone,
               'message' => $enquiry->message,
               'status' => $enquiry->status,
               'source' => $enquiry->source,
               'country' => $enquiry->country,
               'service_name' => $enquiry->service ? $enquiry->service->name : null,
               'created_at' => $enquiry->created_at,
           ];
       });

       return response()->json($transformed);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'email',
            'phone' => 'required',
            'message'=>'nullable',
            'service_id'=>'required',
            'source'=>'nullable',
            'country'=>'nullable',
        ]);

        $data = ContactForm::create($validated);
        return response()->json($data, 201);
    }

    public function show(ContactForm $contact_form)
    {
        return response()->json($contact_form);
    }

    public function update(Request $request, ContactForm $contact_form)
    {
        $validated = $request->validate([
            'status'=>'nullable',
        ]);

        $contact_form->update($validated);
        return response()->json($contact_form);
    }

    public function destroy(ContactForm $contact_form)
    {
        $contact_form->delete();
        return response()->json(null, 204);
    }
}
