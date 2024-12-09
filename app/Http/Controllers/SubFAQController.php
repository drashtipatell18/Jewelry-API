<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubFAQ;
use Illuminate\Support\Facades\Validator;

class SubFAQController extends Controller
{
    public function createSubFAQ(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faq_id' => 'required|exists:f_a_q_s,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $subfaq = SubFAQ::create([
            'faq_id' => $request->input('faq_id'),
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub FAQ created successfully',
            'subfaq' => $subfaq
        ], 200);
    }

    public function getAllSubFAQ()
    {
        $subfaqs = SubFAQ::all();
        return response()->json([
            'success' => true,
            'subfaqs' => $subfaqs
        ], 200);
    }

    public function getSubFAQById($id)
    {
        $subfaq = SubFAQ::find($id);
        return response()->json([
            'success' => true,
            'subfaq' => $subfaq
        ], 200);
    }

    public function updateSubFAQ($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faq_id' => 'required|exists:f_a_q_s,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $subfaq = SubFAQ::find($id);
        $subfaq->update([
            'faq_id' => $request->input('faq_id'),
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Sub FAQ updated successfully',
            'subfaq' => $subfaq
        ], 200);
    }

    public function deleteSubFAQ($id)
    {
        $subfaq = SubFAQ::find($id);
        $subfaq->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sub FAQ deleted successfully'
        ], 200);
    }

    public function AllDeleteSubFAQ()
    {
        $subfaqs = SubFAQ::all();
        $subfaqs->delete();
        return response()->json([
            'success' => true,
            'message' => 'All Sub FAQ deleted successfully'
        ], 200);
    }
}
