<?php

namespace App\Http\Controllers\Api\Donor;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{

    /**
     * Get all donations for the authenticated donor.
     */
    public function index()
    {
        $donations = Donation::with('disease')
            ->where('donor_id', Auth::user()->donor->id)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Your donations retrieved successfully.',
            'data' => $donations
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'disease_id' => 'required|exists:diseases,id',
            'image' => 'required|image',
            'amount' => 'required|integer|min:1',
        ]);

        $imagePath = $request->file('image')->store('donation_images', 'public');

        $donation = Donation::create([
            'disease_id' => $validated['disease_id'],
            'donor_id' => Auth::user()->donor->id,
            'image' => $imagePath,
            'amount' => $validated['amount'],
            'status' => "pending"
        ]);


        return response()->json([
            'status' => 201,
            'message' => 'Donation created successfully.',
            'data' => $donation
        ], 201);
    }
    /**
     * Show specific donation if it belongs to the donor.
     */
    public function show($id)
    {
        $donation = Donation::with('disease')
            ->where('id', $id)
            ->where('donor_id', Auth::user()->donor->id)
            ->first();

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found or access denied.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Donation retrieved successfully.',
            'data' => $donation
        ], 200);
    }

    /**
     * Update donation if it belongs to the donor and still pending.
     */

    public function update(Request $request, $id)
    {
        $donation = Donation::where('id', $id)
            ->where('donor_id', Auth::user()->donor->id)
            ->first();

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found or access denied.'
            ], 404);
        }

        if ($donation->status !== 'pending') {
            return response()->json([
                'status' => 403,
                'message' => 'Only pending donations can be updated.'
            ], 403);
        }

        $validated = $request->validate([
            'image' => 'required|image'
        ]);

        // حذف الصورة القديمة إن وُجدت
        if ($donation->image && Storage::disk('public')->exists($donation->image)) {
            Storage::disk('public')->delete($donation->image);
        }

        // تخزين الصورة الجديدة
        $imagePath = $request->file('image')->store('donation_images', 'public');
        $donation->image = $imagePath;
        $donation->save();

        return response()->json([
            'status' => 200,
            'message' => 'Donation image updated successfully.',
            'data' => $donation
        ], 200);
    }

    /**
     * Delete a donation if it belongs to the donor and still pending.
     */
    public function destroy($id)
    {
        $donation = Donation::where('id', $id)
            ->where('donor_id', Auth::user()->donor->id)
            ->first();

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found or access denied.'
            ], 404);
        }

        if ($donation->status !== 'pending') {
            return response()->json([
                'status' => 403,
                'message' => 'Only pending donations can be deleted.'
            ], 403);
        }

        //delete the donation image

        $donation->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Donation deleted successfully.'
        ], 200);
    }
}
