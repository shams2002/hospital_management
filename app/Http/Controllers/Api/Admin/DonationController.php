<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{

    public function index()
    {
        $donations = Donation::with(['disease', 'donor'])->get();

        return response()->json([
            'status' => 200,
            'message' => 'Donations retrieved successfully.',
            'data' => $donations
        ], 200);
    }
    public function show($id)
    {
        $donation = Donation::with(['disease', 'donor'])->find($id);

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Donation retrieved successfully.',
            'data' => $donation
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'numeric|min:0',
            'status' => 'string|in:pending,approved,rejected',
        ]);

        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found.'
            ], 404);
        }

        $donation->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Donation updated successfully.',
            'data' => $donation
        ], 200);
    }
    public function destroy($id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found.'
            ], 404);
        }

        $donation->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Donation deleted successfully.'
        ], 200);
    }

    public function acceptDonation(Request $request, Donation $donation)
    {
        // /admin/donations/{donation}/accept
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $disease = $donation->disease;
        $collected_amount = $disease->collected_amount += $validated['amount'];
        if ($disease->needed_amount <= $collected_amount) {
            $disease->donation_status = "completed";
        }
        $disease->save();

        $donation->update(["status" => "accepted"]);

        return response()->json([
            'status' => 201,
            'message' => 'Donation accepted successfully.',
            'data' => $donation
        ], 201);
    }
    public function rejectDonation(Request $request, Donation $donation)
    {
        $donation->update(["status" => "reject"]);

        return response()->json([
            'status' => 201,
            'message' => 'Donation rejected successfully.',
            'data' => $donation
        ], 201);
    }
}
