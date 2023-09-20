<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $company)
    {
        try {
            $validated = validator(['company_id' => $company], ['company_id' => ['required', 'integer', 'exists:companies,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }
        
        $comments = Comment::query()->get()->where('company_id', '=', $validated['company_id']);

        return ['data' => $comments];
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create(string $company)
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $company, Request $request)
    {
        try {

            $validated_request = $request->validate([
                'user_id'       => ['required', 'integer', 'exists:users,id'],
                'content'       => ['required', 'string', 'min:150', 'max:550'],
                'rating'        => ['required', 'integer', 'min:1', 'max:10'],
            ]);
            $validated_company_id = validator(['company_id' => $company], ['company_id' => ['required', 'integer', 'exists:companies,id']])->validate();
            $validated = array_merge($validated_request, $validated_company_id);

        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $comment = new Comment();
        $comment->user_id = $validated['user_id'];
        $comment->company_id = $validated['company_id'];
        $comment->content = $validated['content'];
        $comment->rating = $validated['rating'];

        $request_result = $comment->save();
        
        return ['request_result' => $request_result];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $company, string $id)
    {
        try {
            $validated = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:comments,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }
        
        $comment = Comment::find($validated['id']);

        if ($comment['company_id'] == $company)
            return ['data' => $comment];
        else
            return ['error' => 'This comment doesn\'t belong to this company'];
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $company, string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $company, Request $request, string $id)
    {
        try {

            $validated_request = $request->validate([
                'user_id'       => ['required', 'integer', 'exists:users,id'],
                'content'       => ['required', 'string', 'min:150', 'max:550'],
                'rating'        => ['required', 'integer', 'min:1', 'max:10'],
            ]);

            $validated_company_id = validator(['company_id' => $company], ['company_id' => ['required', 'integer', 'exists:companies,id']])->validate();
            $validated_comment_id = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:comments,id']])->validate();

            $validated = array_merge($validated_request, $validated_company_id, $validated_comment_id);

        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $comment = Comment::find($validated['id']);

        if ($comment['company_id'] == $company) {

            $comment->user_id = $validated['user_id'];
            $comment->company_id = $validated['company_id'];
            $comment->content = $validated['content'];
            $comment->rating = $validated['rating'];

            $request_result = $comment->save();
            
            return ['request_result' => $request_result];
        }
        else
            return ['error' => 'This comment doesn\'t belong to this company'];

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company, string $id)
    {
        try {
            $validated = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:comments,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }
        
        $comment = Comment::find($validated['id']);

        if ($comment['company_id'] == $company)
        {
            $request_result = $comment->delete();

            return ['request_result' => $request_result];
        }
        else
            return ['error' => 'This comment doesn\'t belong to this company'];
    }

     /**
     * Get the average rating of the company
     */
    public function getAverageRating(string $company)
    {
        try {
            $validated = validator(['company_id' => $company], ['company_id' => ['required', 'integer', 'exists:companies,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }
        
        $rating = Comment::query()->get()->where('company_id', '=', $validated['company_id'])->avg('rating');

        return ['data' => [ 'average_rating' => $rating, 'company_id' => $validated['company_id']]];
    }
    
}
