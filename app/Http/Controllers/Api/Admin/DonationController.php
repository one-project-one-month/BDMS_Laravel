<?php

namespace App\Http\Controllers\Api\Admin;
use App\Models\User;
use App\Models\Donation;
use App\Enums\DonationStatus;
use App\Http\Resources\Api\Admin\DonationResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\DonationRequest;
use Illuminate\Http\Request;
use Psy\CodeCleaner\FunctionContextPass;
use Symfony\Component\CssSelector\Node\FunctionNode;

class DonationController extends Controller
{
    //

    private function getDonorOrFail($userId){
        $user = User::with('donor')->findOrFail($userId);
        if(!$user->donor) {
            abort(response()->json([
                'message' => 'Donor profile required before donation'
            ],400));
        }

        return $user->donor;
    }
    public function store($userId, DonationRequest $request)
    {
        $donor = $this->getDonorOrFail($userId);
        $donation = Donation::create([
         ...$request->validated(),
         'donor_id' => $donor->id,
         'created_by' => $userId,
         'status' =>DonationStatus::PENDING,
        ]);

        return new DonationResource(
            $donation->load(['donor', 'hospital'])
        );
    }

    public function index($userId)
    {
        $donor = $this->getDonorOrFail($userId);
        $donations = Donation::where('donor_id', $donor->id)->with(['hospital', 'creator',
         'approver'])->latest()->paginate(10);

         return
         DonationResource::collection($donations);
    }

    public Function cancel($userId, $id)
    {
        $donor = $this->getDonorOrFail($userId);

        $donation = Donation::where('donor_id', $donor->id)->where('id', $id)->firstOrFail();

        if($donation->status === DonationStatus::REJECTED) {
            return response()->json([
                'message' => 'Already cancelled'
            ], 400);
        }

        $donation->update([
            'status' => DonationStatus::REJECTED
        ]);

        return response()->json([
            'message' => 'Donation cancelled successfully'
        ]);

    }
}
