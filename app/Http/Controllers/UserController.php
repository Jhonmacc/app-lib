<?php

namespace App\Http\Controllers;

use App\Models\LibraryUser;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
{
    $pageSize = $request->get('pageSize', 10);

    $users = LibraryUser::paginate($pageSize);

    return Inertia::render('ManageUsers', [
        'users' => $users->items(),
        'total' => $users->total(),
        'currentPage' => $users->currentPage(),
        'lastPage' => $users->lastPage()
    ]);
}

    public function listAll()
    {
        return LibraryUser::all();
    }

    public function show($id)
    {
        $user = LibraryUser::findOrFail($id);
        return response()->json($user);
    }



    public function store(Request $request)
    {
        $request->merge([
            'cpf' => preg_replace('/\D/', '', $request->cpf),
            'contact' => preg_replace('/\D/', '', $request->contact),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:users_library,cpf',
            'email' => 'required|email|max:255|unique:users_library,email',
            'contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ], [
            'cpf.unique' => 'O CPF informado já está cadastrado.',
            'email.unique' => 'O e-mail informado já está cadastrado.',
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'contact.required' => 'O campo contato é obrigatório.',
            'address.required' => 'O campo endereço é obrigatório.',
        ]);

        LibraryUser::create($request->all());

        return response()->json(['message' => 'Usuário cadastrado com sucesso!'], 201);
    }


    public function update(Request $request, $id)
    {
        $request->merge([
            'cpf' => preg_replace('/\D/', '', $request->cpf),
            'contact' => preg_replace('/\D/', '', $request->contact),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:users_library,cpf,' . $id,
            'email' => 'required|email|max:255|unique:users_library,email,' . $id,
            'contact' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ], [
            'cpf.unique' => 'O CPF informado já está cadastrado.',
            'email.unique' => 'O e-mail informado já está cadastrado.',
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'contact.required' => 'O campo contato é obrigatório.',
            'address.required' => 'O campo endereço é obrigatório.',
        ]);

        $user = LibraryUser::findOrFail($id);
        $user->update($request->all());

        return response()->json(['message' => 'Usuário atualizado com sucesso!'], 200);
    }




    public function destroy($id)
    {
        LibraryUser::findOrFail($id)->delete();
        return response()->json(['message' => 'Usuário deletado com sucesso!']);
    }
}
