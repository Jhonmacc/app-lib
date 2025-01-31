<?php

namespace App\Http\Controllers;

use App\Models\Copy;
use App\Models\Book;
use Illuminate\Http\Request;

class CopyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Book $book)
    {
        return $book->copies;
    }

    public function available() {
        return Copy::whereDoesntHave('loans', function($query) {
            $query->where('status', '!=', 'devolvido');
        })->with('book')->get();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'registration_numbers' => 'required|array',
            'registration_numbers.*' => 'required|string',
        ]);

        $registrationNumbers = $request->input('registration_numbers');
        $errors = [];

        foreach ($registrationNumbers as $number) {
            $existingCopy = Copy::where('registration_number', $number)->first();

            if ($existingCopy) {
                $errors[] = "O número de registro $number já pertence à cópia do livro '{$existingCopy->book->name}'.";
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }
        foreach ($registrationNumbers as $number) {
            Copy::create([
                'book_id' => $bookId,
                'registration_number' => $number,
            ]);
        }

        return response()->json(['message' => 'Cópias adicionadas com sucesso!']);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'copies.book'])->findOrFail($id);
        return response()->json($loan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
        public function destroy($id)
    {
        $copy = Copy::findOrFail($id);
        $copy->delete();
        return response()->json(null, 204);
    }
}
