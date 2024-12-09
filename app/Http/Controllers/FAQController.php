<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FAQ;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    public function createFAQ(Request $request)
    {
        if($request->user()->role_id !== 1){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $faq = FAQ::create([
            'name' => $request->input('name'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'faq' => $faq
        ], 200);
    }

    public function getAllFAQ()
    {
        $faqs = FAQ::all();
        return response()->json([
            'success' => true,
            'faqs' => $faqs
        ], 200);
    }

    public function getFAQById($id)
    {
        $faq = FAQ::find($id);
        return response()->json([
            'success' => true,
            'faq' => $faq
        ], 200);
    }

    public function updateFAQ($id, Request $request)
    {
        if($request->user()->role_id !== 1){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $faq = FAQ::find($id);
        $faq->update([
            'name' => $request->input('name'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'faq' => $faq
        ], 200);
    }

    public function deleteFAQ($id)
    {
        $faq = FAQ::find($id);
        $faq->delete();
        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully'
        ], 200);
    }

    public function AllDeleteFAQ()
    {
        FAQ::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All FAQ deleted successfully'
        ], 200);
    }   
}
