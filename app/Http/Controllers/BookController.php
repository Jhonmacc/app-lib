<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = $request->get('pageSize', 10);

        $books = Book::withCount('copies')
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'name' => $book->name,
                    'author' => $book->author,
                    'description' => $book->description,
                    'genres' => $book->genres,
                    'available_copies' => $book->copies_count,
                ];
            });

        return Inertia::render('ManageBooks', [
            'books' => $books
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:png|max:2048',
            'genres' => 'required|json'
        ]);

        $book = Book::create([
            ...$validated,
            'genres' => json_decode($validated['genres'], true),
            'image' => $request->hasFile('image')
                ? $request->file('image')->store('books', 'public')
                : null
        ]);

        return response()->json($book, 201);
    }

    public function show($id)
    {
        $book = Book::with('copies')->findOrFail($id);
        return response()->json($book);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:png|max:2048',
            'genres' => 'required|array'
        ]);

        $updateData = [
            'name' => $validated['name'],
            'author' => $validated['author'],
            'description' => $validated['description'],
            'genres' => $validated['genres'],
            'image' => $request->hasFile('image')
                ? $request->file('image')->store('books', 'public')
                : $book->image
        ];

        $book->update($updateData);

        return response()->json($book->load('copies'));
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return response()->json(null, 204);
    }
}
