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
            'answer' => 'required|string',
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
   $faqName = FAQ::find($request->input('faq_id'))->name;
        $subfaq->faq_name = $faqName;
        return response()->json([
            'success' => true,
            'message' => 'Sub FAQ created successfully',
            'subfaq' => $subfaq
        ], 200);
    }

     public function getAllSubFAQ()
    {
        $subfaqs = SubFAQ::with(['faq'=>function ($query) {
            $query->withTrashed(); // Include soft-deleted categories
        }])->get();
        return response()->json([
            'success' => true,
             'subfaqs' => $subfaqs->map(function($subfaq) {
                return [
                    'id' => $subfaq->id,
                    'faq_id' => $subfaq->faq_id,
                    'question' =>$subfaq->question,
                    'answer' =>$subfaq->answer,
                    'faq_name' => isset($subfaq->faq->name) ?$subfaq->faq->name:"",
                    'created_at' =>$subfaq->created_at,
                    'updated_at' =>$subfaq->updated_at,
                    'deleted_at' =>$subfaq->deleted_at,
                ];
            }),
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
            'answer' => 'required|string',
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
           $faqName = FAQ::find($request->input('faq_id'))->name;
        $subfaq->faq_name = $faqName;
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
