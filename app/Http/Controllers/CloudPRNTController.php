<?php

namespace App\Http\Controllers;

use App\Models\PrintJob;
use App\Models\RestaurantPrinter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Handles the Star Micronics CloudPRNT polling protocol.
 *
 * Flow:
 *   1. POST   /cloudprnt/{token}          — printer polls for pending jobs
 *   2. GET    /cloudprnt/{token}?type=... — printer downloads ticket content
 *   3. DELETE /cloudprnt/{token}          — printer confirms successful print
 */
class CloudPRNTController extends Controller
{
    /**
     * POST /cloudprnt/{token}
     *
     * Printer reports its status and asks whether a job is waiting.
     */
    public function poll(Request $request, string $token): JsonResponse
    {
        $printer = RestaurantPrinter::where('token', $token)->first();

        if (!$printer) {
            return response()->json(['error' => 'Printer not found'], 404);
        }

        if (!$printer->is_active) {
            return response()->json(['jobReady' => false]);
        }

        $printer->last_poll_at = now();
        $printer->save();

        $hasJob = PrintJob::where('restaurant_id', $printer->restaurant_id)
            ->where('status', 'pending')
            ->exists();

        if ($hasJob) {
            return response()->json([
                'jobReady'   => true,
                'mediaTypes' => ['text/plain'],
            ]);
        }

        return response()->json(['jobReady' => false]);
    }

    /**
     * GET /cloudprnt/{token}?type=...
     *
     * Printer downloads the next pending job content.
     */
    public function getJob(Request $request, string $token): Response
    {
        $printer = RestaurantPrinter::where('token', $token)->first();

        if (!$printer) {
            return response('Printer not found', 404);
        }

        $job = PrintJob::where('restaurant_id', $printer->restaurant_id)
            ->where('status', 'pending')
            ->oldest()
            ->first();

        if (!$job) {
            return response('', 204);
        }

        $job->update(['status' => 'printing']);

        $printer->last_job_at = now();
        $printer->save();

        return response($job->content, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * DELETE /cloudprnt/{token}
     *
     * Printer confirms the job was printed successfully.
     */
    public function confirmJob(Request $request, string $token): JsonResponse
    {
        $printer = RestaurantPrinter::where('token', $token)->first();

        if (!$printer) {
            return response()->json(['error' => 'Printer not found'], 404);
        }

        $job = PrintJob::where('restaurant_id', $printer->restaurant_id)
            ->where('status', 'printing')
            ->oldest()
            ->first();

        if ($job) {
            $job->update([
                'status'     => 'done',
                'printed_at' => now(),
            ]);
        }

        return response()->json(['result' => 'ok']);
    }
}
