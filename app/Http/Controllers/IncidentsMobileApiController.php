<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncidentsMobileApiController extends Controller
{
    public function getIncidents()
    {
        $incidents = Incident::where('status', '=',  Incident::STATUS_NEW)->all();

        return response()->json([
            'incidents' => $incidents,
        ]);
    }

    public function updateIncidentStatus(Request $request)
    {
        $incident = Incident::find($request->id);

        $incident->status = $request->status;

        $incident->save();

        return response()->json([
            'message' => 'Status zaktualizowany',
        ]);
    }

    public function saveIncidentImages(Request $request)
    {
        $incident = Incident::find($request->id);

        $incident->images = $request->images;

        $incident->save();
        
        return response()->json([
            'message' => 'Zdjęcia zapisane',
        ]);
    }
}
