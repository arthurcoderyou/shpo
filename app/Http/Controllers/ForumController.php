<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    
    public function index()
    {
        return view('admin.forum.index');
    }

    public function create(){

        return view('admin.forum.create');
    }


    public function edit($id){

        $forum = Forum::findOrFail($id);
        return view('admin.forum.edit',[
            'forum' => $forum,
        ]);


    }

 



}
