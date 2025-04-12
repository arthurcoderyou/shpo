<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index()
    {
        // Code to display a list of discussions
        return view('admin.discussion.index');
    }

    public function create()
    {
        // Code to show the form for creating a new discussion
        return view('admin.discussion.create');



    }
 
    public function edit($id)
    {
        // Code to show the form for editing a specific discussion

        $discussion = Discussion::findOrFail($id);
        return view('admin.discussion.edit', compact('discussion'));

    }

    
}
