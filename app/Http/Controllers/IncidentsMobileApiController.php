<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use Illuminate\Support\Facades\Storage;
use App\Models\IncidentImage;

class IncidentsMobileApiController extends Controller
{
    public function getIncidents()
    {
        $incidents = Incident::with('address')->where('status', '=',  Incident::STATUS_OPEN)->get();

        return response()->json([
            'incidents' => $incidents->toArray() ?? [],
        ], 200);
    }

    public function updateIncidentStatus(Request $request)
    {
        $incident = Incident::find($request->id);
     
        if (!$incident) {
            return response()->json([
                'message' => 'Incident not found',
            ], 400);
        }

        $incident->status = $request->status;
        $incident->save();

        return response()->json([
            'message' => 'Status zaktualizowany',
        ], 200);
    }

    public function saveIncidentImages(Request $request)
    {
        $incident = Incident::with('images')->find($request->id);

        if (!$incident) {
            return response()->json([
                'message' => 'Incident not found',
            ], 400);
        }

        if (!$request->hasFile('images')) {
            return response()->json([
                'message' => 'No images uploaded',
            ], 400);
        }

        if ($incident->images->count() > 0) {
            Storage::deleteDirectory(IncidentImage::IMAGES_DIRECTORY . '/' . $incident->id);
            IncidentImage::where('incident_id', $request->id)->delete();
        }

        foreach ($request->file('images') as $image) {
            $imageName = $image->getClientOriginalName() . '.' . $image->getClientOriginalExtension();
            
            Storage::putFileAs(IncidentImage::IMAGES_DIRECTORY . '/' . $incident->id, $image, $imageName);
            IncidentImage::create([
                'incident_id' => $incident->id,
                'image_path' => IncidentImage::IMAGES_DIRECTORY . '/' . $incident->id . '/' . $imageName,
            ]);
        }

        return response()->json([
            'message' => 'Zdjęcia zapisane',
        ], 200);
    }
}
