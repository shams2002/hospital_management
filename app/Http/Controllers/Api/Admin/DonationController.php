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
        $donations = Donation::with(['disease.patient', 'donor'])->get();

        return response()->json([
            'status' => 200,
            'message' => 'Donations retrieved successfully.',
            'data' => $donations
        ], 200);
    }
    public function show($id)
    {
        $donation = Donation::with(['disease.patient', 'donor'])->find($id);

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
            'status' => 'string|in:pending,accepted,rejected',
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

    public function acceptDonation(Request $request,  $donation)
    {

        $donation = Donation::find($donation);

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found.'
            ], 404);
        }

        // /admin/donations/{donation}/accept
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $disease = $donation->disease;
        // زيادة القيمة بشكل واضح
        $disease->collected_amount += $validated['amount'];

        // تحقق من اكتمال التبرع
        if ($disease->collected_amount >= $disease->needed_amount) {
            $disease->donation_status = "completed";

            // حساب الفائض
            $overflow = $disease->collected_amount - $disease->needed_amount;

            if ($overflow > 0 && $disease->user) {
                // تحديث extra_money للمستخدم
                $disease->user->extra_money += $overflow;
                $disease->user->save();

                // خصم الفائض من collected_amount حتى لا يظهر أكثر من المطلوب
                $disease->collected_amount = $disease->needed_amount;
            }
        }

        $disease->save();

        // تحديث حالة التبرع و قيمة amount
        $donation->update([
            'status' => 'accepted',
            'amount' => $validated['amount'],  // تحديث amount هنا
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Donation accepted successfully.',
            'data' => $donation
        ], 201);
    }
    public function rejectDonation(Request $request,  $donation)
    {

        $donation = Donation::find($donation);

        if (!$donation) {
            return response()->json([
                'status' => 404,
                'message' => 'Donation not found.'
            ], 404);
        }
        $donation->update(["status" => "rejected"]);

        return response()->json([
            'status' => 201,
            'message' => 'Donation rejected successfully.',
            'data' => $donation
        ], 201);
    }
}
