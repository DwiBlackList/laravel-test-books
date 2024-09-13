<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use DataTables;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('author.index');
    }

    public function getAuthors(Request $request)
    {
        if ($request->ajax()) {
            $data = Author::query();
            return datatables()::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-warning" onclick="editAuthor(' . $row->id . ')">Edit</button>   ';
                    $btn .= '<button class="btn btn-danger" onclick="deleteAuthor(' . $row->id . ')">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function show(Author $author)
    {
        return response()->json($author);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:authors',
        ]);

        $author = Author::create($request->all());
        return response()->json($author);
    }

    public function update(Request $request, Author $author)
    {
        $author->update($request->all());
        return response()->json($author);
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
