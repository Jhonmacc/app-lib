<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\LibraryUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::with(['user', 'copies.book'])
                    ->get()
                    ->append('status');

        return Inertia::render('LoanBooks', [
            'loans' => $loans,
            'books' => Book::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users_library,id',
            'copy_ids' => 'required|array',
            'copy_ids.*' => 'integer|exists:copies,id',
            'loan_date' => 'required|date',
            'return_date' => 'nullable|date',
            'observation' => 'nullable|string',
        ]);

        $loan = Loan::create($request->only([
            'user_id',
            'loan_date',
            'return_date',
            'observation'
        ]));

        $loan->copies()->attach($request->copy_ids);

        return response()->json($loan, 201);
    }

    public function show($id)
    {
        $loan = Loan::with(['user', 'copies.book'])->findOrFail($id);
        return response()->json($loan);
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $loan->update($request->only([
            'user_id',
            'loan_date',
            'return_date',
            'observation',
            'status'
        ]));

        $loan->copies()->sync($request->copy_ids);

        return response()->json($loan);
    }

    public function returnLoan($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['status' => 'devolvido']);
        $loan->copies()->update(['available' => true]);

        return response()->json($loan);
    }

    public function destroy($id)
{
    $loan = Loan::findOrFail($id);

    $loan->copies()->update(['available' => true]);

    $loan->delete();

    return response()->json(['message' => 'Empréstimo excluído com sucesso.']);
}
}
