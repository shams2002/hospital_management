<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Donation;
use Illuminate\Support\Facades\Storage;
use App\Models\Disease;

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
        ]);

        $disease = Disease::find($validated['disease_id']);
        $disease->collected_amount += $validated['amount'];
        //لما يوصل للحد المطلوب او يصير أكبر منو يخلي الحالة تبع الحالة المرصية انو مكتملة
        $disease->save();

        return response()->json([
            'status' => 201,
            'message' => 'Donation created successfully.',
            'data' => $donation
        ], 201);
    }

    public function show($id)
    {
        $donation = Donation::find($id);

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

    public function adminUpdate(Request $request, $id)
    {
        $donation = Donation::find($id);

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found.'
            ], 404);
        }

        $donation->update($request->only(['status']));

        return response()->json([
            'status' => 200,
            'message' => 'Donation status updated by admin.',
            'data' => $donation
        ], 200);
    }
    public function destroy($id)
    {
    // فقط الأدمن مسموح له يحذف
    if (!Auth::user()->is_admin) {
        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized. Only admins can delete donations.'
        ], 403);
    }

    $donation = Donation::find($id);

    if (!$donation) {
        return response()->json([
            'status' => 404,
            'message' => 'Donation not found.'
        ], 404);
    }

    $donation->delete();

    return response()->json([
        'status' => 204,
        'message' => 'Donation deleted successfully.'
    ], 204);
}
}
