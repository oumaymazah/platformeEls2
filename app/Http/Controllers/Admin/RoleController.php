<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //affichier la page de roles
    public function index()
    {

        $roles = Role::query();
        if(auth()->user()->hasRole('admin')){
            $roles=$roles->whereNotIn('name',['super-admin','admin']);
        }
        elseif(auth()->user()->hasRole('super-admin')){
            $roles=$roles->whereNotIn('name',['super-admin']);
        }
        $roles = $roles->get();
        return view('admin.role.index', compact('roles'));
    }
    public function create()
    {
        return view('admin.role.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $role = Role::where('name', $request->name)->first();
        if ($role) {
            return response()->json(['errors' => [
                'name' => 'Ce rôle existe déjà. Veuillez choisir un autre nom.'
            ]]);
        }

        $role=Role::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Role ajouté avec succès',
        ]);
    }


    public function edit(Role $role)
    {

        return view('admin.role.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        // Valider le nom du rôle
        $validate = $request->validate([
            'name' => 'required'
        ]);

        // Vérifier si le nom du rôle existe déjà
        $existingRole = Role::where('name', $validate['name'])->first();
        if ($existingRole && $existingRole->id !== $role->id) {
            return response()->json([
                'errors' => [
                    'name' => 'Ce rôle existe déjà. Veuillez choisir un autre nom.'
                ]
            ], 422);
        }


        $role->update($validate);




        $response = [
            'success' => true,
            'message'=> 'Rôle  modifié avec succès'];



        return response()->json($response);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['success' => 'Le rôle a été supprimé avec succès']);
    }





}
