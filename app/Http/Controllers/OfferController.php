<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class OfferController extends Controller
{
    // Create Offer
    public function createOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'discount' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'description' => 'required',
            'button_text' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/offers'), $imageName);
        }
        $offer = Offer::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'discount' => $request->input('discount'),
            'start_date' => Carbon::parse($request->input('start_date'))->format('Y-m-d'),
            'end_date' => Carbon::parse($request->input('end_date'))->format('Y-m-d'),
            'status' => $request->input('status'),
            'description' => $request->input('description'),
            'button_text' => $request->input('button_text'),
            'image' => $imageName,
        ]);
        return response()->json(
            [
                'success' => true,
                'message' => 'Offer created successfully',
                'offer' => [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'type' => $offer->type,
                    'discount' => $offer->discount,
                    'start_date' => $offer->start_date,
                    'end_date' => $offer->end_date,
                    'status' => $offer->status,
                    'description' => $offer->description,
                    'button_text' => $offer->button_text,
                    'image' => url('images/offers/' . $offer->image),
                ]
            ], 200);
    }

    // Get All Offers
    public function getAllOffers()
    {
        $offers = Offer::all();
        return response()->json(
            [
                'success' => true,
                'message' => 'Offers fetched successfully',
                'offers' => $offers->map(function ($offer) {
                    return [
                        'id' => $offer->id,
                        'name' => $offer->name,
                        'type' => $offer->type,
                        'discount' => $offer->discount,
                        'start_date' => $offer->start_date,
                        'end_date' => $offer->end_date,
                        'status' => $offer->status,
                        'description' => $offer->description,
                        'button_text' => $offer->button_text,
                        'image' => url('images/offers/' . $offer->image),
                    ];
                })
            ], 200);
    }

    // Get All Active Offers
    public function getAllActiveOffer()
    {
        $offers = Offer::where('status', 'active')->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Active offers fetched successfully',
                'offers' => $offers
            ], 200);
    }

    // Get All Inactive Offers
    public function getAllInactiveOffer()
    {
        $offers = Offer::where('status', 'inactive')->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Inactive offers fetched successfully',
                'offers' => $offers
            ], 200);
    }

    // Update Status Offer
    public function updateStatusOffer($id, Request $request )
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $offer = Offer::find($id);
        $offer->update(['status' => $request->input('status')]);
        return response()->json(
            [
                'success' => true,
                'message' => 'Offer status updated successfully',
                'offer' => [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'type' => $offer->type,
                    'discount' => $offer->discount,
                    'start_date' => $offer->start_date,
                    'end_date' => $offer->end_date,
                    'status' => $offer->status,
                    'description' => $offer->description,
                    'button_text' => $offer->button_text,
                    'image' => url('images/offers/' . $offer->image),
                ]
            ], 200);
    }

    // Filter Offers
    public function filterOffers(Request $request)
    {
        $query = Offer::query();

        // Filter by offer type
        if ($request->input('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by offer name
        if ($request->input('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by start date
        if ($request->input('start_date')) {
            $query->where('start_date', '>=', Carbon::parse($request->input('start_date'))->format('Y-m-d'));
        }

        // Filter by end date
        if ($request->input('end_date')) {
            $query->where('end_date', '<=', Carbon::parse($request->input('end_date'))->format('Y-m-d'));
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by discount range
        if ($request->input('discount_min') && $request->input('discount_max')) {
            $query->whereBetween('discount', [$request->input('discount_min'), $request->input('discount_max')]);
        }

        $offers = $query->get();

        return response()->json(
            [
                'success' => true,
                'message' => 'Offers filtered successfully',
                'offers' => $offers
            ], 200);
    }

    // Get Offer By Id
    public function getOfferById($id)
    {
        $offer = Offer::find($id);
        return response()->json(
            [
                'success' => true,
                'message' => 'Offer fetched successfully',
                'offer' => [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'type' => $offer->type,
                    'discount' => $offer->discount,
                    'start_date' => $offer->start_date,
                    'end_date' => $offer->end_date,
                    'status' => $offer->status,
                    'description' => $offer->description,
                    'button_text' => $offer->button_text,
                    'image' => url('images/offers/' . $offer->image),
                ]
            ], 200);
    }

    // Update Offer
    public function updateOffer(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'discount' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'description' => 'required',
            'button_text' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $offer = Offer::find($id);
        $imageName = $offer->image;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/offers'), $imageName);
        }
        $offer->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'discount' => $request->input('discount'),
            'start_date' => Carbon::parse($request->input('start_date'))->format('Y-m-d'),
            'end_date' => Carbon::parse($request->input('end_date'))->format('Y-m-d'),
            'status' => $request->input('status'),
            'description' => $request->input('description'),
            'button_text' => $request->input('button_text'),
            'image' => $imageName,
        ]);
        return response()->json(
            [
                'success' => true,
                'message' => 'Offer updated successfully',
                'offer' => [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'type' => $offer->type,
                    'discount' => $offer->discount,
                    'start_date' => $offer->start_date,
                    'end_date' => $offer->end_date,
                    'status' => $offer->status,
                    'description' => $offer->description,
                    'button_text' => $offer->button_text,
                    'image' => url('images/offers/' . $offer->image),
                ]
            ], 200);
    }

    // Delete Offer
    public function deleteOffer($id)
    {
        $offer = Offer::find($id);
        $offer->delete();
        return response()->json(
            [
                'success' => true,
                'message' => 'Offer deleted successfully',
            'offers' => $offer
            ], 200);
    }
    
}
