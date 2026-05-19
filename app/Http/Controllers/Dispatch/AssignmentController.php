<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Contact Driver
    | Called by the Contact Driver quick action button.
    | Returns driver contact info as JSON.
    | The frontend can use this to open a phone/SMS link.
    |------------------------------------------------------------------
    */
    public function contactDriver(int $loadId): JsonResponse
    {
        $load = Load::with('driver.user:id,name,phone')
            ->select('id', 'driver_id')
            ->findOrFail($loadId);

        if (!$load->driver) {
            return response()->json([
                'error' => 'No driver assigned to this load.',
            ], 404);
        }

        return response()->json([
            'name'  => $load->driver->user?->name,
            'phone' => $load->driver->user?->phone,
        ]);
    }

    /*
    |------------------------------------------------------------------
    | Report Issue
    | Called by the Report Issue quick action button.
    | Marks the load as failed and logs the issue.
    |------------------------------------------------------------------
    */
    public function reportIssue(Request $request, int $loadId): JsonResponse
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $load = Load::findOrFail($loadId);

        $load->update([
            'status'     => 'failed',
            'is_delayed' => true,
        ]);

        $load->statusLogs()->create([
            'from_status' => $load->getOriginal('status'),
            'to_status'   => 'failed',
            'changed_by'  => auth()->id(),
            'source'      => 'dispatcher',
            'notes'       => $request->notes,
        ]);

        return response()->json(['success' => true]);
    }
}
