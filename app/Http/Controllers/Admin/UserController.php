<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{


    public function index(Request $request)
    {
        $user = auth()->user();
        $query = User::query();

        // Récupération des rôles disponibles selon les permissions
        if ($user->hasRole('admin')) {
            $allRoles = Role::whereNotIn('name', ['super-admin', 'admin'])->get();
            $query->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['super-admin', 'admin']);
            });
        } elseif ($user->hasRole('super-admin')) {
            $allRoles = Role::where('name', '!=', 'super-admin')->get();
            $query->whereDoesntHave('roles', function($q) {
                $q->where('name', 'super-admin');
            });
        } else {
            $allRoles = Role::all();
        }

        // Filtre par rôle si spécifié
        if ($request->filled('role') && $request->role != '') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filtre par statut si spécifié
        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $users = $query->get();
        $selectedRole = $request->role;
        $selectedStatus = $request->status;

        return view('admin.user.index', compact('users', 'allRoles', 'selectedRole', 'selectedStatus'));
    }
    public function create()
    {
        $roles = Role::query();
        if(auth()->user()->hasRole('admin')){
            $roles=$roles->whereIn('name',['professeur']);
        }
        elseif(auth()->user()->hasRole('super-admin')){
            $roles=$roles->whereIn('name',['professeur','admin']);
        }
        $roles = $roles->get();
        return view('admin.user.create',compact('roles'));
    }





public function store(Request $request)
{
    $request->flash();

    // Validation standard des autres champs
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'required',
        'roles' => 'required',
    ], [
        'name.required' => 'Le nom est obligatoire.',
        'lastname.required' => 'Le prénom est obligatoire.',
        'email.required' => 'L\'email est obligatoire.',
        'email.email' => 'Veuillez entrer une adresse email valide.',
        'email.unique' => 'Cet email est déjà utilisé par un autre utilisateur.',
        'phone.required' => 'Le numéro de téléphone est obligatoire.',
    ]);

    // Collection d'erreurs commune pour toutes les validations
    $errors = $validator->errors();

    // Initialiser la variable pour la portée
    $fullPhoneNumber = '';


        $countryCode = '+216';
        $localNumber = $request->input('phone');

        if (!empty($localNumber)) {
            // Formater le numéro
            $fullPhoneNumber = $countryCode . $localNumber;

            // Vérifier si le format correspond à un numéro tunisien (8 chiffres)
            if (preg_match('/^\+216(\d{8})$/', $fullPhoneNumber, $matches)) {

                // Vérifier si le préfixe est valide

                    // Vérifier si le numéro existe déjà dans la base de données
                $existingUser = User::where('phone', $fullPhoneNumber)->first();
                if ($existingUser) {
                    $errors->add('phone', 'Ce numéro de téléphone est déjà associé à un compte existant.');
                }

            } else {
                // Si le format n'est pas valide
                $errors->add('phone', 'Le numéro de téléphone doit être valide pour la Tunisie (8 chiffres).');
            }
        }


    // Si des erreurs sont présentes, rediriger
    if ($validator->fails() || $errors->isNotEmpty()) {
        return response()->json([
            'success' => false,
            'errors' => $errors
        ], 422);
    }

    $validationCode = Str::random(6);
    $password = Str::random(8);

    // Commencer une transaction DB
    DB::beginTransaction();

    try {
        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'phone' => $fullPhoneNumber,
            'email' => $request->email,
            'password' => bcrypt($password),
            'status' => 'inactive',
            'first_login' => true,
            'validation_code' => $validationCode,
        ]);

        $user->assignRole($request->roles);

        // Essayer d'envoyer l'e-mail
        Mail::to($user->email)->send(new UserCreatedMail($user, $password, $validationCode));

        // Si tout va bien jusqu'ici, on valide la transaction
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
        ]);

    } catch (\Exception $e) {
        // En cas d'erreur, annuler toutes les modifications
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' =>  'Erreur lors de la création de l\'utilisateur en raison d\'un problème de connexion',
        ], 500);
    }
}






    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => 'User a été supprimé avec succès']);
    }

    public function show(User $user)
    {

        return view('admin.user.roles', compact('user'));
    }



    

    public function toggleStatus(User $user)
    {
        $oldStatus = $user->status;
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        if ($oldStatus === 'inactive' && $user->status === 'active' && $user->validation_code !== null ) {
            $user->validation_code = null;
            $user->save();

        }

        // Si l'utilisateur passe de 'inactive' à 'active', réinitialiser le compteur de tentatives
        if ($oldStatus === 'inactive' && $user->status === 'active') {
            $sessionKey = 'password_attempts_' . $user->id;
            $sessionTimerKey = 'password_attempts_timer_' . $user->id;

            session([$sessionKey => 0]);
            session([$sessionTimerKey => null]);

        }

        return response()->json([
            'message' => 'Statut modifié avec succès.',
            'success' => true,
            'status' => $user->status
        ]);
    }


}
