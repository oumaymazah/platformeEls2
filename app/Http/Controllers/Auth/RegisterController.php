<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\AccountValidationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\DB;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //tkhali l'utilisateur eli mahouch connecté yhezou l'inscription et l'utilisateur connecté yhezou l paage par defaut
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showRegistrationForm()
    {
        return view('admin.authentication.sign-up');
    }




    public function register(Request $request)
    {
        // Stocker les données du formulaire dans la session pour les réafficher en cas d'erreur
        $request->flash();

        // Validation standard des autres champs
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required',
            'password' => 'required|string|min:8',
            'privacy_policy' => 'required',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'lastname.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé par un autre utilisateur.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'La longueur du mot de passe doit être d\'au moins 8 caractères.',
            'privacy_policy.required' => 'Vous devez accepter la politique de confidentialité.',
        ]);

        // Collection d'erreurs commune pour toutes les validations
        // Important: On récupère et clone la collection d'erreurs existante
        $errors = $validator->errors();

        // Validation du téléphone avec vérification des préfixes tunisiens
        try {
            $countryCode = '+216';
            $localNumber = $request->input('phone');

            if (!empty($localNumber)) {
                // Formater le numéro
                $fullPhoneNumber = $countryCode . $localNumber;

                // Vérifier si le format correspond à un numéro tunisien (8 chiffres)
                if (preg_match('/^\+216(\d{8})$/', $fullPhoneNumber, $matches)) {
                    $numberWithoutPrefix = $matches[1];
                    $firstTwoDigits = substr($numberWithoutPrefix, 0, 2);

                    // Liste des préfixes valides des opérateurs tunisiens
                    $validPrefixes = [
                        // Tunisie Telecom (fixe et mobile)
                        '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', // Mobile
                        '70', '71', '72', '73', '74', '75', '76', '77', '78', '79',
                        '90', '91', '92', '93', '94', '95', '96', '97', '98', '99',

                        // Ooredoo (anciennement Tunisiana)
                        '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',

                        // Orange Tunisie
                        '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',

                        // Lycamobile
                        '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
                    ];
                    $existingUser = User::where('phone', $fullPhoneNumber)->first();

                    if ($existingUser) {
                        $errors->add('phone', 'Ce numéro de téléphone est déjà associé à un compte existant.');
                    }

                    // Vérifier si le préfixe est valide
                    if (!in_array($firstTwoDigits, $validPrefixes)) {
                        $errors->add('phone', 'Le numéro de téléphone ne correspond pas à un opérateur tunisien valide.');
                    }
                        // Vérifier si le numéro existe déjà dans la base de données


                } else {
                    // Si le format n'est pas valide
                    $errors->add('phone', 'Le numéro de téléphone doit être valide pour la Tunisie (8 chiffres).');
                }
            }
        } catch (\Exception $e) {
            $errors->add('phone', 'Le numéro de téléphone doit être valide pour la Tunisie.');
        }

        // Si des erreurs sont présentes, rediriger
        if ($validator->fails() || $errors->isNotEmpty()) {
            return redirect()->back()
                    ->withErrors($errors)
                    ->withInput();
        }

        // Si la validation réussit, procéder à l'enregistrement
        try {
            $validationCode = Str::random(6);

            // Utiliser une transaction pour s'assurer que l'utilisateur n'est créé que si l'email est envoyé
            DB::beginTransaction();

            try {
                // Créer l'utilisateur (avec le numéro de téléphone formaté)
                $user = User::create([
                    'name' => $request->name,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'phone' => $fullPhoneNumber, // Utiliser le numéro complet avec préfixe pays
                    'password' => Hash::make($request->password),
                    'validation_code' => $validationCode,
                    'status' => 'inactive',
                    'first_login' => false,
                ]);

                $user->assignRole('etudiant');

                // Essayer d'envoyer l'email avec gestion des exceptions de connexion
                try {
                    Mail::to($user->email)->send(new AccountValidationMail($user, $validationCode));


                } catch (\Swift_TransportException $e) {
                    // Erreur spécifique de connexion SMTP
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Impossible d\'envoyer l\'email de validation. Problème de connexion au serveur de messagerie.')
                        ->withInput();
                } catch (\Exception $e) {
                    // Autres erreurs d'envoi d'email
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Impossible d\'envoyer l\'email de validation. Veuillez vérifier votre connexion internet ou réessayer plus tard.')
                        ->withInput();
                }

                // Si tout va bien, valider la transaction
                DB::commit();

                // Enregistrer l'ID utilisateur dans la session
                $request->session()->put('user_id', $user->id);

                return redirect()->route('validation.form')->with('success', 'Un code de validation a été envoyé à votre email.');

            } catch (\Exception $innerException) {
                // En cas d'erreur dans le bloc de transaction, annuler et renvoyer l'erreur
                DB::rollBack();
                throw $innerException;
            }
        } catch (\Swift_TransportException $e) {
            // Catch spécifique pour les erreurs de connexion SMTP
            return redirect()->back()
                    ->with('error', 'Impossible d\'envoyer l\'email de validation. Problème de connexion au serveur de messagerie.')
                    ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                    ->with('error', 'Une erreur est survenue lors de l\'inscription: ' . $e->getMessage())
                    ->withInput();
        }
    }
    public function showValidationForm()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous connecter à nouveau.');
        }
        return view('auth.verify');
    }
    public function validateAccount(Request $request)
    {
        $request->validate([
            'validation_code' => 'required|string',
        ]);

        $userId = session('user_id');


        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Votre session a expiré. Veuillez vous connecter à nouveau.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('sign-up')
                ->with('error', 'Utilisateur non trouvé. Veuillez vous connecter à nouveau.');
        }

        if ($user->validation_code !== $request->validation_code) {
            return back()->with('error', 'Code de validation incorrect.');
        }


        $user->status = 'active';
        $user->validation_code = null;
        $user->email_verified_at = now();
        $user->save();

        // Supprimer l'ID de la session après validation

         session()->forget('user_id');


        auth()->login($user);
        if ($user->first_login) {
            return redirect()->route('password.change.form');
        }
        return redirect()->route('index')
            ->with('success', 'Votre compte a été activé avec succès.');
    }
    public function resendCode(Request $request)
    {
        // Récupérer l'ID utilisateur de la session
        $userId = $request->session()->get('user_id');

        if (!$userId) {
            return redirect()->route('sign-up')
                ->with('error', 'Votre session a expiré. Veuillez vous inscrire à nouveau.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('sign-up')
                ->with('error', 'Utilisateur non trouvé. Veuillez vous inscrire à nouveau.');
        }


        $validationCode = Str::random(6);
        $user->validation_code = $validationCode;
        $user->save();


        try {
            Mail::to($user->email)->send(new AccountValidationMail($user,$validationCode));
        } catch (\Exception $e) {
            \Log::error('Erreur d\'envoi d\'email: ' . $e->getMessage());
            return back()->with('warning', 'Problème lors de l\'envoi de l\'email. Votre nouveau code est: ' . $validationCode);
        }

        return back()->with('success', 'Un nouveau code de validation a été envoyé à votre email.');
    }

}

