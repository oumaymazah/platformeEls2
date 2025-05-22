<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Reservation;
use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FormationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
   public function show($id)
    {
        $formation = Training::with([
            'user',
            'category',
            'feedbacks',
            'courses',
            'quizzes' => function($query) {
                $query->where('is_published', true);
            }
        ])->findOrFail($id);

        $formation->total_feedbacks = $formation->feedbacks->count();
        $formation->average_rating = $formation->feedbacks->avg('rating_count');
        $formation->sum_ratings = $formation->feedbacks->sum('rating_count');

        return view('admin.apps.formation.formationshow', compact('formation'));
    }

public function index(Request $request) {
    // Détermine le rôle de l'utilisateur
    $userIsAdmin = auth()->user() && (
        auth()->user()->hasRole('admin') || 
        auth()->user()->hasRole('super-admin')
    );
    $userIsProf = auth()->user() && auth()->user()->hasRole('professeur');
    
    // Récupère les catégories avec le nombre de formations approprié selon le rôle
    if ($userIsProf) {
        // Pour les professeurs, ne compter que leurs propres formations
        $categories = Category::withCount(['trainings' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->get();
    } elseif (!$userIsAdmin) {
        // Pour les étudiants, ne compter que les formations publiées
        $categories = Category::withCount(['trainings' => function ($query) {
            $query->where('status', 1); // Uniquement les formations publiées
        }])->get();
    } else {
        // Pour admin, compter toutes les formations
        $categories = Category::withCount('trainings')->get();
    }
    
    $query = Training::with(['user', 'category', 'feedbacks', 'courses']);
    
    // Le reste du code reste identique...
    // Filtrage par catégorie
    if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
        $query->where('category_id', $request->category_id);
    }
    
    // Filtrage par statut et rôle utilisateur
    if ($userIsProf) {
        $query->where('user_id', auth()->id());
        
        // Appliquer également le filtre de statut si fourni
        if (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
    } 
    elseif (!$userIsAdmin) {
        $query->where('status', 1);
    } 
    elseif (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
        $query->where('status', $request->status);
    }
    
    // Recherche par terme
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%");
        });
    }
    
    $formations = $query->get();
    
    
    // Récupérer toutes les réservations confirmées une seule fois pour optimiser
    $confirmedReservations = Reservation::where('status', 1)->with('cart')->get();
    
    $occupiedSeatsCount = [];
    
    foreach ($confirmedReservations as $reservation) {
        $cart = $reservation->cart;
        
        if ($cart && is_array($cart->training_ids)) {
            foreach ($cart->training_ids as $trainingId) {
                if (!isset($occupiedSeatsCount[$trainingId])) {
                    $occupiedSeatsCount[$trainingId] = 0;
                }
                $occupiedSeatsCount[$trainingId]++;
            }
        }
    }
    
    $formations->each(function ($formation) use ($occupiedSeatsCount) {
        $formation->final_price = $formation->discount > 0
            ? $formation->price * (1 - $formation->discount / 100)
            : $formation->price;
        
        $formation->total_feedbacks = $formation->feedbacks->count();
        $formation->average_rating = $formation->total_feedbacks > 0
            ? round($formation->feedbacks->sum('rating_count') / $formation->total_feedbacks, 1)
            : null;
        $formation->cours_count = $formation->courses->count();
        
        $occupiedSeats = isset($occupiedSeatsCount[$formation->id]) ? $occupiedSeatsCount[$formation->id] : 0;
        $formation->remaining_seats = max(0, $formation->total_seats - $occupiedSeats);
        $formation->occupied_seats = $occupiedSeats;
    });
    
    $totalFeedbacks = $formations->sum('total_feedbacks');
    
    if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
        $title = Category::find($request->category_id)->title;
    } else {
        $title = $userIsProf ? 'Mes formations' : 'Toutes les formations';
    }
    
    if ($request->has('format') && $request->format === 'html') {
        return view('admin.apps.formation.formations', compact('formations', 'categories', 'title', 'totalFeedbacks', 'userIsAdmin', 'userIsProf'));
    }
   
    $isAjaxRequest = $request->ajax() && 
                 $request->header('X-Requested-With') === 'XMLHttpRequest' && 
                 $request->wantsJson();
    if ($isAjaxRequest) {
        return response()->json([
            'formations' => $formations,
            'title' => $title,
            'totalFeedbacks' => $totalFeedbacks,
            'userIsAdmin' => $userIsAdmin,
            'userIsProf' => $userIsProf
        ]);
    }
    
    return view('admin.apps.formation.formations', compact('formations', 'categories', 'title', 'totalFeedbacks', 'userIsAdmin', 'userIsProf'));
}


//originale
// public function index(Request $request) {
//     $categories = Category::withCount('trainings')->get();
    
//     $query = Training::with(['user', 'category', 'feedbacks', 'courses']);
    
//     // Filtrage par catégorie - UNIQUEMENT si une valeur est explicitement fournie
//     if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
//         $query->where('category_id', $request->category_id);
//     }
    
//     // Gérer le filtrage par statut en fonction du rôle de l'utilisateur
//     $userIsAdmin = auth()->user() && (
//         auth()->user()->hasRole('admin') || 
//         auth()->user()->hasRole('super-admin')
//     );

//     $userIsProf = auth()->user() && auth()->user()->hasRole('professeur');
  
//     // Si l'utilisateur est un professeur, filtrer par user_id ET appliquer le filtre de statut si présent
//     if ($userIsProf) {
//         $query->where('user_id', auth()->id());
        
//         // Appliquer également le filtre de statut si fourni
//         if (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//             $query->where('status', $request->status);
//         }
//     }
//     // Si l'utilisateur n'est pas admin ou prof, montrer seulement les formations publiées (status=1)
//     elseif (!$userIsAdmin) {
//         $query->where('status', 1);
//     } 
//     // Sinon (pour les admins), appliquer les filtres de statut seulement si explicitement fournis
//     elseif (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//         $query->where('status', $request->status);
//     }
    
//     // Recherche par terme
//     if ($request->filled('search')) {
//         $searchTerm = $request->search;
//         $query->where(function($q) use ($searchTerm) {
//             $q->where('title', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('description', 'LIKE', "%{$searchTerm}%");
//         });
//     }
    
//     $formations = $query->get();
    
//     // Récupérer toutes les réservations confirmées une seule fois pour optimiser
//     $confirmedReservations = Reservation::where('status', 1)->with('cart')->get();
    
//     $occupiedSeatsCount = [];
    
//     foreach ($confirmedReservations as $reservation) {
//         $cart = $reservation->cart;
        
//         if ($cart && is_array($cart->training_ids)) {
//             foreach ($cart->training_ids as $trainingId) {
//                 if (!isset($occupiedSeatsCount[$trainingId])) {
//                     $occupiedSeatsCount[$trainingId] = 0;
//                 }
//                 $occupiedSeatsCount[$trainingId]++;
//             }
//         }
//     }
    
//     $formations->each(function ($formation) use ($occupiedSeatsCount) {
//         $formation->final_price = $formation->discount > 0
//             ? $formation->price * (1 - $formation->discount / 100)
//             : $formation->price;
        
//         $formation->total_feedbacks = $formation->feedbacks->count();
//         $formation->average_rating = $formation->total_feedbacks > 0
//             ? round($formation->feedbacks->sum('rating_count') / $formation->total_feedbacks, 1)
//             : null;
//         $formation->cours_count = $formation->courses->count();
        
//         $occupiedSeats = isset($occupiedSeatsCount[$formation->id]) ? $occupiedSeatsCount[$formation->id] : 0;
//         $formation->remaining_seats = max(0, $formation->total_seats - $occupiedSeats);
//         $formation->occupied_seats = $occupiedSeats;
//     });
    
//     $totalFeedbacks = $formations->sum('total_feedbacks');
    
//     if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
//         $title = Category::find($request->category_id)->title;
//     } else {
//         $title = $userIsProf ? 'Mes formations' : 'Toutes les formations';
//     }
// if ($request->has('format') && $request->format === 'html') {
//     return view('admin.apps.formation.formations', compact('formations', 'categories', 'title', 'totalFeedbacks', 'userIsAdmin', 'userIsProf'));
// }
   
//     $isAjaxRequest = $request->ajax() && 
//                  $request->header('X-Requested-With') === 'XMLHttpRequest' && 
//                  $request->wantsJson();
//     if ($isAjaxRequest) {
//         return response()->json([
//             'formations' => $formations,
//             'title' => $title,
//             'totalFeedbacks' => $totalFeedbacks,
//             'userIsAdmin' => $userIsAdmin,
//             'userIsProf' => $userIsProf
//         ]);
//     }
    
//     return view('admin.apps.formation.formations', compact('formations', 'categories', 'title', 'totalFeedbacks', 'userIsAdmin', 'userIsProf'));
// }





public function create()
{
    $professeurs = User::whereHas('roles', function($query) {
        $query->where('name', 'professeur');
    })
    ->where('status', 'active') // Ajouter cette condition pour filtrer par statut actif
    ->get(['id', 'name', 'lastname']);

    $categories = Category::all();

    return view('admin.apps.formation.formationcreate', compact('professeurs', 'categories'));
}

public function edit($id)
{
    $formation = Training::findOrFail($id);

    $professeurs = User::whereHas('roles', function($query) {
        $query->where('name', 'professeur');
    })
    ->where('status', 'active') // Ajouter cette condition pour filtrer par statut actif
    ->get(['id', 'name', 'lastname']);

    $categories = Category::all();

    return view('admin.apps.formation.formationedit', compact('formation', 'professeurs', 'categories'));
}


public function store(Request $request)
{
    // Convertir les dates du format DD/MM/YYYY au format YYYY-MM-DD
    if ($request->has('start_date')) {
        $request->merge(['start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d')]);
    }
    
    if ($request->has('end_date')) {
        $request->merge(['end_date' => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d')]);
    }
    
    // Convertir également la date de publication si elle existe
    if ($request->has('publish_date') && $request->publish_date) {
        try {
            $request->merge(['publish_date' => Carbon::createFromFormat('d/m/Y', $request->publish_date)->format('Y-m-d')]);
        } catch (\Exception $e) {
            return back()->withErrors(['publish_date' => 'Format de date invalide. Utilisez le format JJ/MM/AAAA.'])->withInput();
        }
    }
    
    try {
        // Définir les règles de validation
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:payante,gratuite',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'publication_type' => 'required|in:now,later',
            'total_seats' => 'required|integer|min:1',
        ];

        // Ajouter validation conditionnelle pour publish_date
        if ($request->publication_type === 'later') {
            $rules['publish_date'] = 'required|date';
        } else {
            $rules['publish_date'] = 'nullable|date';
        }

        // Modification de la règle d'image pour prendre en compte l'option "keep_image"
        if ($request->has('keep_image') && $request->has('current_image')) {
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048';
        } else {
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png,gif|max:2048';
        }

        // Ajout conditionnel de règles pour le prix
        if ($request->type === 'payante') {
            $rules['price'] = 'required|numeric|min:0';
        }

        // Valider les données
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Assurez-vous que le répertoire existe
            if (!Storage::disk('public')->exists('formations')) {
                Storage::disk('public')->makeDirectory('formations');
            }

            $file = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('formations', $fileName, 'public');

            // Vérifier si l'image a été correctement enregistrée
            if (!Storage::disk('public')->exists($imagePath)) {
                throw new \Exception('Échec de l\'enregistrement de l\'image');
            }
        } elseif ($request->has('keep_image') && $request->has('current_image')) {
            // Utiliser l'image existante
            $imagePath = $request->current_image;
        } else {
            throw new \Exception('Image requise');
        }

        // Préparation des données pour la formation
        $formationData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'user_id' => $validated['user_id'],
            'image' => $imagePath,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_bestseller' => $request->has('is_bestseller') ? 1 : 0,
            'total_seats' => $validated['total_seats'],
        ];

        // Gestion du prix selon le type
        $formationData['price'] = ($validated['type'] === 'payante') ? $validated['price'] : 0;
        $formationData['discount'] = $request->has('discount') ? $request->discount : 0;
        $formationData['final_price'] = ($validated['type'] === 'payante')
            ? ($formationData['price'] * (1 - $formationData['discount'] / 100))
            : 0;

        // Gestion de la publication
        if ($validated['publication_type'] === 'later') {
            if (!$request->has('publish_date') || empty($request->publish_date)) {
                return back()->withErrors(['publish_date' => 'La date de publication est requise pour une publication ultérieure.'])->withInput();
            }
            
            try {
                $publishDate = Carbon::parse($validated['publish_date'])->startOfDay();
                
                // Vérifier si la date est égale ou postérieure à aujourd'hui
                if ($publishDate->greaterThanOrEqualTo(Carbon::today())) {
                    $formationData['publish_date'] = $publishDate->format('Y-m-d');
                    $formationData['status'] = 0; // Non publiée
                } else {
                    return back()->withErrors(['publish_date' => 'La date de publication doit être égale ou postérieure à aujourd\'hui.'])->withInput();
                }
            } catch (\Exception $e) {
                Log::error('Erreur de conversion de date de publication', [
                    'date' => $request->publish_date,
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors(['publish_date' => 'Format de date invalide. Utilisez le format JJ/MM/AAAA.'])->withInput();
            }
        } else {
            $formationData['status'] = 1; // Publiée immédiatement
            $formationData['publish_date'] = null;
        }

        // Log pour débogage
        Log::info('Données formation avant création', $formationData);

        DB::beginTransaction();

        // Vérifiez que le modèle Training inclut ces champs dans $fillable
        $formation = Training::create($formationData);

        if (!$formation || !$formation->exists) {
            throw new \Exception('La création de la formation a échoué');
        }

        DB::commit();

        Log::info('Formation créée avec succès', [
            'formation_id' => $formation->id,
            'title' => $formation->title,
            'user_id' => Auth::id()
        ]);
        
        // Utiliser la même clé 'formation_id' partout pour la cohérence
        session()->flash('formation_id', $formation->id);
        session()->flash('from_formation', true);
        
        // Vérification si c'est une requête AJAX ou JSON
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Formation créée avec succès',
                'formation_id' => $formation->id
            ]);
        }
        
        // Flasher les données pour SweetAlert2 et pour conserver les données du formulaire
        return redirect()->route('formationcreate')
            ->with('success', 'Formation créée avec succès')
            ->with('formation_id', $formation->id)
            ->with('form_data', $request->except(['image']))
            ->with('old_data', $formationData);   // Conserver également les données formatées
 
    } catch (\Exception $e) {
        // En cas d'erreur, annuler la transaction
        if (isset($formation) && DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        Log::error('Erreur lors de la création de la formation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Supprimer l'image si elle a été uploadée en cas d'échec
        if (isset($imagePath) && $imagePath && !$request->has('current_image')) {
            Storage::disk('public')->delete($imagePath);
        }

        // Vérification si c'est une requête AJAX ou JSON pour l'erreur aussi
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la formation: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Erreur lors de la création de la formation: ' . $e->getMessage())->withInput();
    }
}
//originale
// public function update(Request $request, $id)
// {
//    if ($request->has('start_date')) {
//     try {
//         $request->merge([
//             'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d')
//         ]);
//     } catch (\Exception $e) {
//         return back()->withErrors(['start_date' => 'Le format de date doit être JJ/MM/AAAA'])->withInput();
//     }
// }
//     if ($request->has('end_date')) {
//         $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
//         $request->merge(['end_date' => $endDate]);
//     }
//     $validated = $request->validate([
//         'title' => 'required|string|max:255',
//         'description' => 'required|string',
//         'type' => 'required|in:payante,gratuite',
//         'price' => $request->type === 'payante' ? 'required|numeric|min:0' : 'nullable',
//         'discount' => $request->type === 'payante' ? 'nullable|numeric|min:0|max:100' : 'nullable',
//         'final_price' => $request->type === 'payante' ? 'required|numeric|min:0' : 'nullable',
//         'category_id' => 'required|exists:categories,id',
//         'user_id' => 'required|exists:users,id',
//         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//         'delete_image' => 'nullable|boolean',
//         'publication_type' => 'required|in:now,later',
//         'publish_date' => 'required_if:publication_type,later|nullable|date',
//         'start_date' => 'required|date',
//         'end_date' => 'required|date|after_or_equal:start_date',
//         'total_seats' => 'required|integer|min:1', // Ajout de la validation pour le nombre de places
//     ]);

//     try {
//         DB::beginTransaction();

//         $formation = Training::findOrFail($id);

//         // Gestion de l'image
//         if ($request->hasFile('image')) {
//             // Si une nouvelle image est téléchargée, supprimer l'ancienne
//             if ($formation->image) {
//                 Storage::disk('public')->delete($formation->image);
//             }
//             // Stocker la nouvelle image
//             $imagePath = $request->file('image')->store('formations', 'public');
//             $formation->image = $imagePath;
//         } elseif (isset($request->delete_image) && $request->delete_image == 1 && $formation->image) {
//             // Si on demande de supprimer l'image et qu'une image existe
//             Storage::disk('public')->delete($formation->image);
//             // Image par défaut
//             $formation->image = 'formations/default.jpg';
//         }

//         // Mise à jour des champs
//         $formation->title = $validated['title'];
//         $formation->description = $validated['description'];
//         $formation->start_date = $validated['start_date'];
//         $formation->end_date = $validated['end_date'];
//         $formation->type = $validated['type'];
//         $formation->category_id = $validated['category_id'];
//         $formation->user_id = $validated['user_id'];
//         $formation->total_seats = $validated['total_seats']; // Ajout du nombre de places

//         // Gestion des prix pour les formations payantes
//         if ($validated['type'] === 'payante') {
//             $formation->price = $validated['price'];
//             $formation->discount = $validated['discount'] ?? 0;
//             $formation->final_price = $validated['final_price'];
//         } else {
//             $formation->price = 0;
//             $formation->discount = 0;
//             $formation->final_price = 0;
//         }

//         // Gestion de la publication
//         if ($validated['publication_type'] === 'now') {
//             $formation->status = true;
//             $formation->publish_date = null;
//         } else {
//             $formation->status = false;
//             $formation->publish_date = $validated['publish_date'];
//         }

//         $formation->save();
//         DB::commit();

//         return redirect()->route('formations')->with('success', 'Formation mise à jour avec succès');
//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('Erreur lors de la mise à jour de la formation', [
//             'id' => $id,
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);

//         return back()->withErrors('Erreur lors de la mise à jour: ' . $e->getMessage())->withInput();
//     }
// }

// public function update(Request $request, $id)
// {
//     if ($request->has('start_date')) {
//         try {
//             $request->merge([
//                 'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d')
//             ]);
//         } catch (\Exception $e) {
//             return back()->withErrors(['start_date' => 'Le format de date doit être JJ/MM/AAAA'])->withInput();
//         }
//     }
//     if ($request->has('end_date')) {
//         $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
//         $request->merge(['end_date' => $endDate]);
//     }
//     $validated = $request->validate([
//         'title' => 'required|string|max:255',
//         'description' => 'required|string',
//         'type' => 'required|in:payante,gratuite',
//         'price' => $request->type === 'payante' ? 'required|numeric|min:0' : 'nullable',
//         'discount' => $request->type === 'payante' ? 'nullable|numeric|min:0|max:100' : 'nullable',
//         'final_price' => $request->type === 'payante' ? 'required|numeric|min:0' : 'nullable',
//         'category_id' => 'required|exists:categories,id',
//         'user_id' => 'required|exists:users,id',
//         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//         'delete_image' => 'nullable|boolean',
//         'publication_type' => 'required|in:now,later',
//         'publish_date' => 'required_if:publication_type,later|nullable|date',
//         'start_date' => 'required|date',
//         'end_date' => 'required|date|after_or_equal:start_date',
//         'total_seats' => 'required|integer|min:1',
//     ]);

//     try {
//         DB::beginTransaction();

//         $formation = Training::findOrFail($id);

//         // Gestion de l'image
//         if ($request->hasFile('image')) {
//             // Si une nouvelle image est téléchargée, supprimer l'ancienne
//             if ($formation->image) {
//                 Storage::disk('public')->delete($formation->image);
//             }
//             // Stocker la nouvelle image
//             $imagePath = $request->file('image')->store('formations', 'public');
//             $formation->image = $imagePath;
//         } elseif (isset($request->delete_image) && $request->delete_image == 1 && $formation->image) {
//             // Si on demande de supprimer l'image et qu'une image existe
//             Storage::disk('public')->delete($formation->image);
//             // Image par défaut
//             $formation->image = 'formations/default.jpg';
//         }

//         // Mise à jour des champs
//         $formation->title = $validated['title'];
//         $formation->description = $validated['description'];
//         $formation->start_date = $validated['start_date'];
//         $formation->end_date = $validated['end_date'];
//         $formation->type = $validated['type'];
//         $formation->category_id = $validated['category_id'];
//         $formation->user_id = $validated['user_id'];
//         $formation->total_seats = $validated['total_seats'];

//         // Gestion des prix pour les formations payantes
//         if ($validated['type'] === 'payante') {
//             $formation->price = $validated['price'];
//             $formation->discount = $validated['discount'] ?? 0;
//             $formation->final_price = $validated['final_price'];
//         } else {
//             $formation->price = 0;
//             $formation->discount = 0;
//             $formation->final_price = 0;
//         }

//         // Gestion de la publication
//       if ($validated['publication_type'] === 'now') {
//     $formation->status = true;
//     $formation->publish_date = null;
// } else {
//     $formation->status = false;
//     try {
//         // Convertir du format DD/MM/YYYY en YYYY-MM-DD
//         $formation->publish_date = Carbon::createFromFormat('d/m/Y', $validated['publish_date'])
//             ->startOfDay()
//             ->format('Y-m-d');
//     } catch (\Exception $e) {
//         Log::error('Erreur de conversion de date de publication', [
//             'id' => $id,
//             'date' => $validated['publish_date'],
//             'error' => $e->getMessage()
//         ]);
//         // En cas d'erreur, on utilise la date actuelle
//         $formation->publish_date = Carbon::now()->startOfDay()->format('Y-m-d');
//     }
// }

//         $formation->save();
//         DB::commit();

//         return redirect()->route('formations')->with('success', 'Formation mise à jour avec succès');
//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('Erreur lors de la mise à jour de la formation', [
//             'id' => $id,
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);

//         return back()->withErrors('Erreur lors de la mise à jour: ' . $e->getMessage())->withInput();
//     }
// }
public function update(Request $request, $id)
{
    $formation = Training::findOrFail($id);
    
    // Validation rules
    $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required',
        'start_date' => 'required|date_format:d/m/Y',
        'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
        'type' => 'required|in:payante,gratuite',
        'category_id' => 'required|exists:categories,id',
        'user_id' => 'required|exists:users,id',
        'total_seats' => 'required|integer|min:1',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];
    
    // Add conditional rules for price and discount if type is 'payante'
    if ($request->type == 'payante') {
        $rules['price'] = 'required|numeric|min:0';
        $rules['discount'] = 'nullable|numeric|min:0|max:100';
    }
    
    // Handle publish date validation differently based on publication type
    if ($request->publication_type == 'later') {
        $rules['publish_date'] = 'required|date_format:d/m/Y';
    }
    
    // Validate the request data
    $validatedData = $request->validate($rules);
    
    // Format dates for database
    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['start_date'])->format('Y-m-d');
    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['end_date'])->format('Y-m-d');
    
    // Update formation with validated data
    $formation->title = $validatedData['title'];
    $formation->description = $validatedData['description'];
    $formation->start_date = $startDate;
    $formation->end_date = $endDate;
    $formation->type = $validatedData['type'];
    $formation->category_id = $validatedData['category_id'];
    $formation->user_id = $validatedData['user_id'];
    $formation->total_seats = $validatedData['total_seats'];
    
    // Handle price and discount if type is 'payante'
    if ($validatedData['type'] == 'payante') {
        $formation->price = $validatedData['price'];
        $formation->discount = $validatedData['discount'] ?? 0;
        $formation->final_price = $request->final_price;
    } else {
        $formation->price = 0;
        $formation->discount = 0;
        $formation->final_price = 0;
    }
    
    // Handle publication status and date
    if ($request->publication_type == 'now') {
        $formation->status = true;
        $formation->publish_date = now();
    } else {
        // Use createFromFormat to properly convert the date string to a Carbon date
        $formation->status = false;
        if ($request->publish_date) {
            $formation->publish_date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->publish_date)->format('Y-m-d');
        } else {
            $formation->publish_date = null;
        }
    }
    
    // Handle image upload
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($formation->image && Storage::disk('public')->exists($formation->image)) {
            Storage::disk('public')->delete($formation->image);
        }
        
        // Store the new image
        $imagePath = $request->file('image')->store('formations', 'public');
        $formation->image = $imagePath;
    } elseif ($request->delete_image == 1) {
        // Delete the image if requested
        if ($formation->image && Storage::disk('public')->exists($formation->image)) {
            Storage::disk('public')->delete($formation->image);
        }
        $formation->image = null;
    }
    
    // Save the formation
    $formation->save();
    
    // Redirect with success message
    return redirect()->route('formations')->with('success', 'Formation mise à jour avec succès.');
}



public function destroy($id)
{
    try {
        // Trouver la formation
        $training = Training::findOrFail($id);
        
        // Supprimer l'image si elle existe et n'est pas l'image par défaut
        if ($training->image && $training->image !== 'formations/default.jpg' && Storage::disk('public')->exists($training->image)) {
            Storage::disk('public')->delete($training->image);
        }
        
        // Nettoyer les paniers contenant cette formation AVANT de supprimer la formation
        $this->removeFromAllCarts($id);
        
        // Supprimer la formation
        $training->delete();
        
        // Vérifier si c'est une requête AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formation supprimée avec succès'
            ]);
        }
        
        return redirect()->route('formations')
            ->with('success', 'Formation supprimée avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur lors de la suppression de la formation: ' . $e->getMessage());
        
        // Vérifier si c'est une requête AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la formation.'
            ], 500);
        }
        
        return redirect()->back()
            ->with('error', 'Une erreur est survenue lors de la suppression de la formation.');
    }
}

/**
 * Retire la formation de tous les paniers.
 *
 * @param  int  $trainingId
 * @return void
 */
private function removeFromAllCarts($trainingId)
{
    try {
        // Convertir en string pour assurer la cohérence des types
        $trainingId = (string) $trainingId;
        
        // Récupérer tous les paniers
        $carts = Cart::all();
        
        foreach ($carts as $cart) {
            // S'assurer que training_ids est un tableau (peut être NULL)
            $trainingIds = $cart->training_ids ?: [];
            
            // Vérifier si cet ID de formation existe dans le panier
            if (in_array($trainingId, $trainingIds) || in_array((int)$trainingId, $trainingIds)) {
                // Filtrer l'ID de formation (en gérant à la fois string et int)
                $updatedIds = array_values(array_filter($trainingIds, function($id) use ($trainingId) {
                    return (string)$id !== (string)$trainingId;
                }));
                
                // Mettre à jour le panier avec les nouveaux IDs
                $cart->training_ids = $updatedIds;
                $cart->save();
                
                Log::info("Formation ID={$trainingId} retirée du panier ID={$cart->id}");
            }
        }
    } catch (\Exception $e) {
        Log::error("Erreur lors du nettoyage des paniers pour la formation ID={$trainingId}: " . $e->getMessage());
    }
}
/**
 * Retire la formation de tous les paniers.
 *
 * @param  int  $trainingId
 * @return void
 */
// private function removeFromAllCarts($trainingId)
// {
//     try {
//         // Convert to string to ensure type consistency
//         $trainingId = (string) $trainingId;
        
//         // Get all carts
//         $carts = Cart::all();
        
//         foreach ($carts as $cart) {
//             $trainingIds = $cart->training_ids ?: [];
            
//             // Check if this training ID exists in the cart
//             if (in_array($trainingId, $trainingIds) || in_array((int)$trainingId, $trainingIds)) {
//                 // Filter out the training ID (handling both string and int)
//                 $updatedIds = array_values(array_filter($trainingIds, function($id) use ($trainingId) {
//                     return (string)$id !== (string)$trainingId;
//                 }));
                
//                 // Update the cart
//                 $cart->training_ids = $updatedIds;
//                 $cart->save();
                
//                 Log::info("Formation ID={$trainingId} retirée du panier ID={$cart->id}");
//             }
//         }
//     } catch (\Exception $e) {
//         Log::error("Erreur lors du nettoyage des paniers pour la formation ID={$trainingId}: " . $e->getMessage());
//     }
// }

// public function getRemainingSeats($formationId) {
//     // Récupérer la formation
//     $formation = Training::findOrFail($formationId);
//     $totalSeats = $formation->total_seats;
    
//     // Récupérer toutes les réservations confirmées (status = 1)
//     $confirmedReservations = Reservation::where('status', 1)->get();
    
//     // Compter combien de fois cette formation apparaît dans les paniers des réservations confirmées
//     $occupiedSeats = 0;
    
//     foreach ($confirmedReservations as $reservation) {
//         $cart = Cart::find($reservation->cart_id);
        
//         if ($cart && is_array($cart->training_ids)) {
//             // Si la formation est dans ce panier réservé, incrémenter le compteur
//             if (in_array($formationId, $cart->training_ids)) {
//                 $occupiedSeats++;
//             }
//         }
//     }
    
//     // Calculer les places restantes
//     $remainingSeats = max(0, $totalSeats - $occupiedSeats); // Pour éviter un nombre négatif
    
//     // Retourner à la fois le nombre total et le nombre restant
//     return response()->json([
//         'total_seats' => $totalSeats,
//         'remaining_seats' => $remainingSeats,
//         'occupied_seats' => $occupiedSeats
//     ]);
// }
// public function checkAvailableSeats(Request $request)
//     {
//         try {
//             // Récupérer les formations du panier de l'utilisateur connecté
//             $user = Auth::user();
//             $cartItems = Cart::where('user_id', $user->id)->get();
            
//             // Si le panier est vide, retourner un résultat vide mais valide
//             if ($cartItems->isEmpty()) {
//                 return response()->json([
//                     'success' => true,
//                     'trainings' => [],
//                     'message' => 'Panier vide'
//                 ]);
//             }
            
//             // Récupérer les IDs des formations dans le panier
//             $trainingIds = $cartItems->pluck('training_id')->toArray();
            
//             // Récupérer les informations sur les places disponibles pour ces formations
//             $trainings = Training::whereIn('id', $trainingIds)
//                 ->select('id', 'title', 'available_seats', 'total_seats')
//                 ->get()
//                 ->map(function ($training) {
//                     return [
//                         'id' => $training->id,
//                         'title' => $training->title,
//                         'available_seats' => $training->available_seats,
//                         'total_seats' => $training->total_seats
//                     ];
//                 });
            
//             return response()->json([
//                 'success' => true,
//                 'trainings' => $trainings
//             ]);
//         } catch (\Exception $e) {
//             // Journaliser l'erreur pour le débogage
//             Log::error('Erreur lors de la vérification des places disponibles: ' . $e->getMessage());
            
//             // Retourner une réponse d'erreur plus informative
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Une erreur est survenue lors de la vérification des places disponibles.',
//                 'error' => config('app.debug') ? $e->getMessage() : null
//             ], 500);
//         }
//     }

// public function checkAvailableSeats(Request $request)
//     {
//         try {
//             // Vérifier si l'utilisateur est connecté
//             if (!Auth::check()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Utilisateur non authentifié'
//                 ], 401);
//             }

//             // Récupérer les formations du panier de l'utilisateur connecté
//             $user = Auth::user();
//             $cartItems = Cart::where('user_id', $user->id)->get();
            
//             // Si le panier est vide, retourner un résultat vide mais valide
//             if ($cartItems->isEmpty()) {
//                 return response()->json([
//                     'success' => true,
//                     'trainings' => [],
//                     'message' => 'Panier vide'
//                 ]);
//             }
            
//             // Récupérer les IDs des formations dans le panier
//             $trainingIds = $cartItems->pluck('training_id')->toArray();
            
//             // Récupérer les informations sur les places disponibles pour ces formations
//             $trainings = Training::whereIn('id', $trainingIds)
//                 ->select('id', 'title', 'available_seats', 'total_seats')
//                 ->get()
//                 ->map(function ($training) {
//                     return [
//                         'id' => $training->id,
//                         'title' => $training->title,
//                         'available_seats' => $training->available_seats,
//                         'total_seats' => $training->total_seats
//                     ];
//                 });
            
//             return response()->json([
//                 'success' => true,
//                 'trainings' => $trainings
//             ]);
//         } catch (\Exception $e) {
//             // Journaliser l'erreur pour le débogage
//             Log::error('Erreur lors de la vérification des places disponibles: ' . $e->getMessage());
            
//             // Retourner une réponse d'erreur plus informative
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Une erreur est survenue lors de la vérification des places disponibles.',
//                 'error' => config('app.debug') ? $e->getMessage() : null
//             ], 500);
//         }
//     }

// public function checkAvailableSeats(Request $request)
// {
//     try {
//         // Vérifier si l'utilisateur est connecté
//         if (!Auth::check()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Utilisateur non authentifié'
//             ], 401);
//         }

//         // Récupérer les formations du panier de l'utilisateur connecté
//         $user = Auth::user();
//         $cartItems = Cart::where('user_id', $user->id)->get();
        
//         // Si le panier est vide, retourner un résultat vide mais valide
//         if ($cartItems->isEmpty()) {
//             return response()->json([
//                 'success' => true,
//                 'trainings' => [],
//                 'message' => 'Panier vide'
//             ]);
//         }
        
//         // Récupérer les IDs des formations dans le panier
//         $trainingIds = $cartItems->pluck('training_id')->toArray();
        
//         // Récupérer les informations sur les places disponibles pour ces formations
//         // Assurez-vous que le modèle Training existe et a les bons attributs
//         $trainings = Training::whereIn('id', $trainingIds)
//             ->select('id', 'title', 'available_seats', 'total_seats')
//             ->get();
        
//         // Transformez les données pour éviter les problèmes de sérialisation
//         $formattedTrainings = $trainings->map(function ($training) {
//             return [
//                 'id' => $training->id,
//                 'title' => $training->title,
//                 'available_seats' => (int) $training->available_seats,
//                 'total_seats' => (int) $training->total_seats
//             ];
//         })->toArray();
        
//         return response()->json([
//             'success' => true,
//             'trainings' => $formattedTrainings
//         ]);
//     } catch (\Exception $e) {
//         // Journaliser l'erreur pour le débogage
//         Log::error('Erreur lors de la vérification des places disponibles: ' . $e->getMessage());
//         Log::error('Stack trace: ' . $e->getTraceAsString());
        
//         // Retourner une réponse d'erreur plus informative
//         return response()->json([
//             'success' => false,
//             'message' => 'Une erreur est survenue lors de la vérification des places disponibles.',
//             'error' => config('app.debug') ? $e->getMessage() : null
//         ], 500);
//     }
// }

public function checkAvailableSeats(Request $request)
{
    try {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $user = Auth::user();
        
        // Récupérer le panier de l'utilisateur
        $cart = Cart::where('user_id', $user->id)->first();
        
        // Récupérer les IDs de formation à vérifier
        $trainingIds = $cart ? $cart->training_ids : [];
        
        // Si aucun panier ou panier vide, chercher dans les réservations confirmées
        if (empty($trainingIds)) {
            // Trouver la dernière réservation confirmée
            $confirmedReservation = Reservation::where('user_id', $user->id)
                                        ->where('status', 1)
                                        ->first();
            
            // Si une réservation confirmée existe, utiliser ses formations
            if ($confirmedReservation && !empty($confirmedReservation->training_data)) {
                $trainingIds = array_column($confirmedReservation->training_data, 'id');
            }
        }
        
        // Si toujours aucune formation à vérifier
        if (empty($trainingIds)) {
            return response()->json([
                'success' => true,
                'trainings' => [],
                'message' => 'Aucune formation à vérifier'
            ]);
        }
        
        // Récupérer les informations sur les places pour ces formations
        $trainings = Training::whereIn('id', $trainingIds)->get();
        
        // Transformer les données des formations
        $formattedTrainings = $trainings->map(function ($training) use ($user) {
            // Compter les réservations confirmées pour cette formation
            $confirmedReservations = Reservation::where('status', 1)
                ->whereJsonContains('training_data', ['id' => $training->id])
                ->count();
            
            // Calculer les places restantes
            $remainingSeats = max(0, $training->total_seats - $confirmedReservations);
            
            return [
                'id' => $training->id,
                'title' => $training->title,
                'total_seats' => (int) $training->total_seats,
                'available_seats' => (int) $remainingSeats,
                'occupied_seats' => (int) $confirmedReservations
            ];
        })->toArray();
        
        return response()->json([
            'success' => true,
            'trainings' => $formattedTrainings
        ]);
    } catch (\Exception $e) {
        // Journaliser l'erreur pour le débogage
        Log::error('Erreur lors de la vérification des places disponibles: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        // Retourner une réponse d'erreur plus informative
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la vérification des places disponibles.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

/**
 * Extension pour le controller Formation - Méthode getRemainingSeats améliorée
 */
/**
 * Méthode améliorée pour getRemainingSeats avec plus de détails
 */
public function getRemainingSeats($id) {
    try {
        // Log initial avec l'ID de formation
        Log::info("Début de getRemainingSeats pour la formation ID: {$id}");
        
        // Récupérer la formation et vérifier qu'elle existe
        $training = Training::findOrFail($id);
        Log::info("Formation trouvée avec ID {$id}: " . json_encode([
            'title' => $training->title,
            'total_seats' => $training->total_seats
        ]));
        
        // Traiter le cas où total_seats est null ou vide
        $totalSeats = $training->total_seats ?? 0;
        $totalSeats = (int)$totalSeats; // Cast explicite en entier
        
        Log::info("Nombre total de places: {$totalSeats}");
        
        // Récupérer la liste complète des réservations pour cette formation
        // Utilisons une approche différente pour être sûr de capturer toutes les réservations
        $reservations = Reservation::where('status', 1)->get();
        
        // Compteur manuel des réservations qui contiennent cette formation
        $confirmedReservations = 0;
        $matchingReservations = [];
        
        // Utiliser à la fois l'ID comme entier et comme chaîne pour la comparaison
        $trainingId = (int)$id;
        $trainingIdStr = (string)$id;
        
        foreach ($reservations as $reservation) {
            $trainingData = $reservation->training_data ?? [];
            
            // Si training_data n'est pas un tableau, essayer de le décoder
            if (is_string($trainingData)) {
                $trainingData = json_decode($trainingData, true);
            }
            
            // Parcourir chaque formation dans les données de réservation
            foreach ($trainingData as $item) {
                // Vérifier si l'ID correspond à notre formation (comme entier ou chaîne)
                $itemId = $item['id'] ?? null;
                
                if ($itemId !== null && ($itemId == $trainingId || $itemId == $trainingIdStr)) {
                    $confirmedReservations++;
                    $matchingReservations[] = [
                        'reservation_id' => $reservation->id,
                        'user_id' => $reservation->user_id
                    ];
                    break; // Une seule réservation par utilisateur pour cette formation
                }
            }
        }
        
        Log::info("Nombre de réservations confirmées: {$confirmedReservations}");
        Log::info("Réservations correspondantes: " . json_encode($matchingReservations));
        
        // Calculer le nombre de places restantes
        $remainingSeats = max(0, $totalSeats - $confirmedReservations);
        
        // Déterminer si la formation est complète
        $isComplete = ($remainingSeats === 0 && $totalSeats > 0);
        
        Log::info("Places restantes: {$remainingSeats}, Formation complète: " . ($isComplete ? 'Oui' : 'Non'));
        
        // Préparer la réponse avec tous les détails
        $response = [
            'success' => true,
            'training_id' => $trainingId,
            'total_seats' => $totalSeats,
            'reservations_count' => $confirmedReservations,
            'remaining_seats' => $remainingSeats,
            'is_complete' => $isComplete, // true/false explicite
            'debug' => [
                'id_as_int' => $trainingId,
                'id_as_string' => $trainingIdStr,
                'matching_reservations_count' => count($matchingReservations)
            ]
        ];
        
        Log::info("Réponse finale: " . json_encode($response));
        
        return response()->json($response);
    } catch (\Exception $e) {
        Log::error("Exception dans getRemainingSeats: " . $e->getMessage());
        Log::error("Stack trace: " . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des places restantes',
            'error' => config('app.debug') ? $e->getMessage() : null,
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}

// public function getRemainingSeats($id) {
//     try {
//         // Log initial pour confirmer l'appel
//         Log::info("Appel à getRemainingSeats pour l'ID : " . $id);
        
//         // Récupérer la formation
//         $training = Training::findOrFail($id);
//         Log::info("Formation trouvée : ", $training->toArray());
        
//         // Convertir l'ID en entier et chaîne pour tester les deux cas
//         $trainingId = (int)$id;
//         $trainingIdStr = (string)$id;
        
//         // Loguer les réservations pour débogage
//         $reservations = Reservation::where('status', 1)->get();
//         Log::info("Réservations confirmées (status=1) : ", $reservations->toArray());
        
//         // Compter les réservations confirmées avec conversion en chaîne
//         $confirmedReservations = Reservation::where('status', 1)
//             ->whereJsonContains('training_data', ['id' => $trainingIdStr]) // Test avec chaîne
//             ->orWhereJsonContains('training_data', ['id' => $trainingId]) // Test avec entier
//             ->count();
        
//         // Loguer la requête SQL pour débogage
//         $query = Reservation::where('status', 1)
//             ->whereJsonContains('training_data', ['id' => $trainingIdStr])
//             ->orWhereJsonContains('training_data', ['id' => $trainingId])
//             ->toSql();
//         Log::info("Requête SQL générée : " . $query, ['trainingId' => $trainingId, 'trainingIdStr' => $trainingIdStr]);
        
//         // Calculer le nombre de places restantes
//         $totalSeats = (int)$training->total_seats;
//         $remainingSeats = max(0, $totalSeats - $confirmedReservations);
        
//         // Déterminer si la formation est complète
//         $isComplete = ($remainingSeats === 0 && $totalSeats > 0);
        
//         // Log final pour débogage
//         Log::info("Formation #{$id}: total={$totalSeats}, réservations={$confirmedReservations}, restantes={$remainingSeats}, complète=" . ($isComplete ? 'oui' : 'non'));
        
//         return response()->json([
//             'success' => true,
//             'training_id' => $trainingId,
//             'total_seats' => $totalSeats,
//             'reservations_count' => $confirmedReservations,
//             'remaining_seats' => $remainingSeats,
//             'is_complete' => $isComplete
//         ]);
//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la récupération des places restantes: ' . $e->getMessage());
//         Log::error('Stack trace: ' . $e->getTraceAsString());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la récupération des places restantes',
//             'error' => config('app.debug') ? $e->getMessage() : null
//         ], 500);
//     }
// }
// public function getRemainingSeats($id) {
//     try {
//         // Récupérer la formation
//         $training = Training::findOrFail($id);
        
//         // Convertir l'ID en entier pour assurer la cohérence des types
//         $trainingId = (int)$id;
        
//         // Compter les réservations confirmées qui incluent cette formation
//         // Attention: Utiliser une méthode de comptage plus précise pour les structures JSON
//         $confirmedReservations = 0;
        
//         // Récupérer toutes les réservations confirmées
//         $allReservations = Reservation::where('status', 1)->get();
        
//         // Parcourir chaque réservation pour vérifier si elle contient la formation
//         foreach ($allReservations as $reservation) {
//             $trainingData = $reservation->training_data;
            
//             // Vérifier si training_data est un tableau JSON valide
//             if (is_array($trainingData)) {
//                 foreach ($trainingData as $item) {
//                     // Vérifier si l'élément est un objet/tableau et contient un ID correspondant
//                     if (is_array($item) && isset($item['id'])) {
//                         // Comparer à la fois comme entier et comme chaîne
//                         $itemId = $item['id'];
//                         if ($itemId == $trainingId) { // Utilisation de == pour comparaison souple
//                             $confirmedReservations++;
//                             break; // Compter une seule fois par réservation
//                         }
//                     }
//                 }
//             }
//         }
        
//         // Calculer le nombre de places restantes
//         $totalSeats = (int)$training->total_seats;
//         $remainingSeats = max(0, $totalSeats - $confirmedReservations);
        
//         // Détermination explicite si la formation est complète
//         $isComplete = ($remainingSeats === 0 && $totalSeats > 0);
        
//         // Log pour débogage
//         Log::info("Formation #{$id}: total={$totalSeats}, réservations={$confirmedReservations}, restantes={$remainingSeats}, complète=".($isComplete ? 'oui' : 'non'));
        
//         return response()->json([
//             'success' => true,
//             'training_id' => $trainingId,
//             'total_seats' => $totalSeats,
//             'reservations_count' => $confirmedReservations,
//             'remaining_seats' => $remainingSeats,
//             'is_complete' => $isComplete
//         ]);
//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la récupération des places restantes: ' . $e->getMessage());
//         Log::error('Stack trace: ' . $e->getTraceAsString());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la récupération des places restantes',
//             'error' => config('app.debug') ? $e->getMessage() : null
//         ], 500);
//     }
// }



/**
 * Récupère le nombre de places restantes pour une formation spécifique
 *
 * @param  int  $id  ID de la formation
 * @return \Illuminate\Http\Response
 */
// public function getRemainingSeats($id)
// {
//     try {
//         // Récupérer la formation
//         $training = Training::findOrFail($id);
        
//         // Compter les réservations confirmées qui incluent cette formation
//         $confirmedReservations = Reservation::where('status', 1)
//             ->whereJsonContains('training_data', ['id' => (int)$id])
//             ->count();
        
//         // Calculer le nombre de places restantes
//         $totalSeats = (int)$training->total_seats;
//         $remainingSeats = max(0, $totalSeats - $confirmedReservations);
        
//         return response()->json([
//             'success' => true,
//             'training_id' => (int)$id,
//             'total_seats' => $totalSeats,
//             'remaining_seats' => $remainingSeats,
//             'is_complete' => ($remainingSeats === 0 && $totalSeats > 0)
//         ]);
//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la récupération des places restantes: ' . $e->getMessage());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la récupération des places restantes',
//             'error' => config('app.debug') ? $e->getMessage() : null
//         ], 500);
//     }
// }

}