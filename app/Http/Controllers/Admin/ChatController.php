<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $query = Chat::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name');
                }
            ]
        )
            ->orderBy('created_at', 'DESC');

        if ($request->input('content')) {
            $query->where('content', 'LIKE', '%' . $request->input('content') . '%');
        }

        if ($request->input('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->input('user_name') . '%');
            });
        }

        $chats = $query->paginate(50);
        return view('admin.chat.index', compact('chats'));
    }

    public function delete($id)
    {
        $result = Chat::where('id', $id)->delete();
        if ($result) {
            return response()->json(
                [
                    'success' => true
                ]
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'Đã xãy ra lỗi trong quá trình xoá chat.'
            ]
        );
    }
}
