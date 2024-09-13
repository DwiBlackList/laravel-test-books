<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use DataTables;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $authors = Author::all();
        return view('book.index', compact('authors'));
    }

    public function getBooks(Request $request)
    {
        if ($request->ajax()) {
            $data = Book::with('author');
            return datatables()::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-warning" onclick="editBook(' . $row->id . ')">Edit</button>   ';
                    $btn .= '<button class="btn btn-danger" onclick="deleteBook(' . $row->id . ')">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'serial_number' => 'required|Numeric|unique:books',
            'published_at' => 'required|date',
        ]);

        $book = Book::create($request->all());
        return response()->json($book);
    }

    public function update(Request $request, Book $book)
    {
        $book->update($request->all());
        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
