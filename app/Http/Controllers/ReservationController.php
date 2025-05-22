<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Reservation;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    /**
     * Créer une nouvelle réservation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     public function create(Request $request)
{
    // Vérifier si l'utilisateur est connecté
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'Vous devez être connecté pour effectuer une réservation'
        ], 401);
    }

    // Récupérer le panier de l'utilisateur
    $cart = Cart::where('user_id', Auth::id())->first();

    // Vérifier si le panier existe
    if (!$cart) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun panier trouvé'
        ], 404);
    }

    // Vérifier si le panier a des formations
    if (empty($cart->training_ids)) {
        return response()->json([
            'success' => false,
            'message' => 'Votre panier est vide'
        ], 400);
    }

    try {
        // Récupérer les données complètes des formations pour les stocker
        $trainingsData = null;
        if (!empty($cart->training_ids)) {
            $trainings = Training::whereIn('id', $cart->training_ids)->get();

            // Stocker toutes les informations importantes des formations
            $trainingsData = $trainings->map(function($training) {
                // Calculer le prix final avec la remise
                $finalPrice = $training->discount > 0
                    ? $training->price * (1 - $training->discount / 100)
                    : $training->price;

                return [
                    'id' => (int)$training->id,  // Convertir explicitement en entier
                    'title' => $training->title,
                    'description' => $training->description,
                    'duration' => $training->duration,
                    'type' => $training->type,
                    'status' => $training->status,
                    'start_date' => $training->start_date,
                    'end_date' => $training->end_date,
                    'price' => $training->price,
                    'discount' => $training->discount ?? 0,
                    'final_price' => $finalPrice,
                    'image' => $training->image,
                    'user_id' => (int)$training->user_id,  // Également converti en entier
                    'instructor_name' => $training->user ? $training->user->name : null,
                    'total_seats' => (int)$training->total_seats,  // Convertir en entier
                    'remaining_seats' => (int)$training->remaining_seats,  // Convertir en entier
                    'created_at' => $training->created_at,
                    'updated_at' => $training->updated_at
                ];
            })->toArray();
        }

        // Créer la réservation
        $reservation = new Reservation();
        $reservation->cart_id = $cart->id;
        $reservation->user_id = Auth::id();
        $reservation->training_data = $trainingsData; // Stocker toutes les données des formations
        $reservation->reservation_date = $request->input('reservation_date', now()->toDateString());
        $reservation->reservation_time = $request->input('reservation_time', now()->toTimeString());
        $reservation->status = false; // Non payé par défaut
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => 'Réservation effectuée avec succès',
            'reservation_id' => $reservation->id,
            'clearCart' => true
        ]);
    } catch (\Exception $e) {
        // Log l'erreur pour le débogage
        Log::error('Erreur lors de la création de la réservation: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création de la réservation: ' . $e->getMessage()
        ], 500);
    }
}

//       public function create(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Vous devez être connecté pour effectuer une réservation'
//         ], 401);
//     }

//     // Récupérer le panier de l'utilisateur
//     $cart = Cart::where('user_id', Auth::id())->first();

//     // Vérifier si le panier existe
//     if (!$cart) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Aucun panier trouvé'
//         ], 404);
//     }

//     // Vérifier si le panier a des formations
//     // Attention : training_ids est déjà un tableau grâce au cast, pas besoin de json_decode
//     if (empty($cart->training_ids)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Votre panier est vide'
//         ], 400);
//     }

//       try {
//         // Récupérer les données des formations pour les stocker
//         $trainingsData = null;
//         if (!empty($cart->training_ids)) {
//             $trainings = Training::whereIn('id', $cart->training_ids)->get();
//             // Stocker les informations essentielles des formations
//             $trainingsData = $trainings->map(function($training) {
//                 return [
//                     'id' => $training->id,
//                     'title' => $training->title,
//                     'price' => $training->price,
//                     'discount' => $training->discount ?? 0,
//                     'image' =>$training->image,
//                     'total_seats'=>$training->total_seats,
//                 ];
//             })->toArray();
//         }

//         // Créer la réservation
//         $reservation = new Reservation();
//         $reservation->cart_id = $cart->id;
//         $reservation->user_id = Auth::id();
//         $reservation->training_data = $trainingsData; // Ajouter les données des formations
//         $reservation->reservation_date = $request->input('reservation_date', Carbon::now()->toDateString());
//         $reservation->reservation_time = $request->input('reservation_time', Carbon::now()->toTimeString());
//         $reservation->status = false; // Non payé par défaut
//         $reservation->save();

//         return response()->json([
//             'success' => true,
//             'message' => 'Réservation effectuée avec succès',
//             'reservation_id' => $reservation->id,
//             'clearCart' => true
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la création de la réservation: ' . $e->getMessage()
//         ], 500);
//     }
// }



//     public function create(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Vous devez être connecté pour effectuer une réservation'
//         ], 401);
//     }

//     // Récupérer le panier de l'utilisateur
//     $cart = Cart::where('user_id', Auth::id())->first();

//     // Vérifier si le panier existe
//     if (!$cart) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Aucun panier trouvé'
//         ], 404);
//     }

//     // Vérifier si le panier a des formations
//     // Attention : training_ids est déjà un tableau grâce au cast, pas besoin de json_decode
//     if (empty($cart->training_ids)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Votre panier est vide'
//         ], 400);
//     }

//     try {
//         // Créer la réservation
//         $reservation = new Reservation();
//         $reservation->cart_id = $cart->id;
//         $reservation->user_id = Auth::id();
//         $reservation->reservation_date = $request->input('reservation_date', Carbon::now()->toDateString());
//         $reservation->reservation_time = $request->input('reservation_time', Carbon::now()->toTimeString());
//         $reservation->status = false; // Non payé par défaut
//         $reservation->save();
//         return response()->json([
//             'success' => true,
//             'message' => 'Réservation effectuée avec succès',
//             'reservation_id' => $reservation->id,
//             'clearCart' => true
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de la création de la réservation: ' . $e->getMessage()
//         ], 500);
//     }
// }
    /**
     * Vérifier si l'utilisateur est authentifié
     *
     * @return \Illuminate\Http\Response
     */
    public function checkAuth()
    {
        return response()->json([
            'authenticated' => Auth::check()
        ]);
    }

    public function getDetails()
{
    $user = Auth::user();
    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['trainings' => [], 'discount' => 0]);
    }

    $trainings = $cart->getFormations();
    $discount = 0; // À remplacer par votre logique de remise

    return response()->json([
        'trainings' => $trainings->map(function($training) {
            return [
                'id' => $training->id,
                'title' => $training->title,
                'price' => $training->price
            ];
        }),
        'discount' => $discount
    ]);
}

public function checkReservation()
{
    $userId = Auth::id();

    // Récupérer le panier actuel
    $cart = Cart::where('user_id', $userId)->first();
    $hasItemsInCart = $cart && !empty($cart->training_ids);

    // Vérifier s'il y a une réservation confirmée (status = 1)
    $confirmedReservation = Reservation::where('user_id', $userId)
                                ->where('status', 1)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Vérifier s'il y a une réservation en attente (status = 0)
    $pendingReservation = Reservation::where('user_id', $userId)
                                ->where('status', 0)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Si nous avons des articles dans le panier et une réservation confirmée,
    // il faut créer une nouvelle réservation
    if ($hasItemsInCart && $confirmedReservation) {
        return response()->json([
            'hasReservation' => false,
            'reservation_id' => null,
            'shouldCreateNewReservation' => true
        ]);
    }

    // Utiliser la réservation en attente s'il y en a une
    if ($pendingReservation) {
        return response()->json([
            'hasReservation' => true,
            'reservation_id' => $pendingReservation->id
        ]);
    }

    // Si une réservation est confirmée mais pas d'articles dans le panier
    if ($confirmedReservation && !$hasItemsInCart) {
        return response()->json([
            'hasReservation' => true,
            'reservation_id' => $confirmedReservation->id
        ]);
    }

    return response()->json([
        'hasReservation' => false,
        'reservation_id' => null
    ]);
}
// public function checkReservation()
// {
//     $userId = Auth::id();
//     // Vérifier les réservations avec status = 0 OU status = 1
//     $reservation = Reservation::where('user_id', $userId)
//                             ->whereIn('status', [0, 1]) // Inclure les deux statuts
//                             ->orderBy('created_at', 'desc')
//                             ->first();

//     return response()->json([
//         'hasReservation' => $reservation ? true : false,
//         'reservation_id' => $reservation ? $reservation->id : null
//     ]);
// }

/**
 * Annule une réservation existante
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
// public function cancelReservation(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Vous devez être connecté pour annuler une réservation'
//         ], 401);
//     }

//     $reservationId = $request->input('reservation_id');

//     // Rechercher la réservation de l'utilisateur
//     $reservation = Reservation::where('id', $reservationId)
//         ->where('user_id', Auth::id())
//         ->first();

//     if (!$reservation) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Réservation non trouvée'
//         ], 404);
//     }

//     try {
//         // Supprimer la réservation
//         $reservation->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Réservation annulée avec succès'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Erreur lors de l\'annulation de la réservation: ' . $e->getMessage()
//         ], 500);
//     }
// }

//originale
// public function listStudentsWithReservations()
// {
//     // Récupérer toutes les réservations avec les relations utilisateur et panier
//     $reservations = Reservation::with(['user', 'cart'])->get();

//     $studentsWithReservations = [];

//     foreach ($reservations as $reservation) {
//         $user = $reservation->user;
//         $cart = $reservation->cart;

//         // Vérifier si l'utilisateur et le panier existent
//         if ($user && $cart) {
//             // Récupérer les formations du panier
//             $trainings = [];
//             $totalOriginal = 0;
//             $totalDiscount = 0;
//             $totalAfterDiscount = 0;

//             if (!empty($cart->training_ids)) {
//                 $trainings = Training::whereIn('id', $cart->training_ids)->get();

//                 // Calculer les totaux
//                 foreach ($trainings as $training) {
//                     $originalPrice = $training->price;
//                     $totalOriginal += $originalPrice;

//                     // Vérifier si la formation a une remise
//                     if ($training->discount > 0) {
//                         $discountAmount = ($originalPrice * $training->discount) / 100;
//                         $priceAfterDiscount = $originalPrice - $discountAmount;

//                         $totalDiscount += $discountAmount;
//                         $totalAfterDiscount += $priceAfterDiscount;
//                     } else {
//                         $totalAfterDiscount += $originalPrice;
//                     }
//                 }
//             }

//             // Formater le statut de la réservation
//             $statusText = '';
//             switch ($reservation->status) {
//                 case 0:
//                     $statusText = 'En attente';
//                     break;
//                 case 1:
//                     $statusText = 'Confirmée';
//                     break;
//                 default:
//                     $statusText = 'Inconnu';
//             }

//             // Ajouter les informations de l'étudiant et ses réservations
//             $studentInfo = [
//                 'id' => $user->id,
//                 'reservation_id' => $reservation->id,
//                 'nom' => $user->lastname ?? 'N/A',
//                 'prenom' => $user->name ?? 'N/A',
//                 'telephone' => $user->phone ?? 'N/A',
//                 'email' => $user->email ?? 'N/A',
//                 'reservation_date' => $reservation->reservation_date,
//                 'reservation_time' => $reservation->reservation_time,

//                 'status' => $reservation->status,
//                 'status_text' => $statusText,
//                 'formations' => $trainings->map(function($training) {
//                     return [
//                         'id' => $training->id,
//                         'title' => $training->title,
//                         'price' => $training->price,
//                         'discount' => $training->discount ?? 0,
//                         'discount_amount' => $training->discount > 0 ? ($training->price * $training->discount) / 100 : 0,
//                         'price_after_discount' => $training->discount > 0 ?
//                             $training->price - (($training->price * $training->discount) / 100) :
//                             $training->price
//                     ];
//                 }),
//                 'total_original' => $totalOriginal,
//                 'total_discount' => $totalDiscount,
//                 'total_after_discount' => $totalAfterDiscount,
//                 'payment_date' => $reservation->payment_date
//             ];

//             $studentsWithReservations[] = $studentInfo;
//         }
//     }

//     // Retourner la vue existante avec les données
//     return view('admin.apps.reservations.reservations-list', compact('studentsWithReservations'));
// }

// public function listStudentsWithReservations()
// {
//     // Récupérer toutes les réservations avec les relations utilisateur et panier
//     $reservations = Reservation::with(['user'])->get();
//     $studentsWithReservations = [];
//     if ($request->filled('status') && $request->status != '') {
//                 $query->where('status', $request->status);
//     }
//     foreach ($reservations as $reservation) {
//         $user = $reservation->user;

//         // Vérifier si l'utilisateur et le panier existent
//        if ($user) {
//             // Utiliser les formations stockées dans training_data
//             $trainings = [];
//             $totalOriginal = 0;
//             $totalDiscount = 0;
//             $totalAfterDiscount = 0;

//             if (!empty($reservation->training_data)) {
//                 $trainings = collect($reservation->training_data)->map(function($trainingData) {
//                     // Convertir les données stockées en objet
//                     $training = new \stdClass();
//                     $training->id = $trainingData['id'];
//                     $training->title = $trainingData['title'];
//                     $training->price = $trainingData['price'];
//                     $training->discount = $trainingData['discount'] ?? 0;

//                     // Calculer le prix après remise
//                     if ($training->discount > 0) {
//                         $discountAmount = ($training->price * $training->discount) / 100;
//                         $training->discount_amount = $discountAmount;
//                         $training->price_after_discount = $training->price - $discountAmount;
//                     } else {
//                         $training->discount_amount = 0;
//                         $training->price_after_discount = $training->price;
//                     }

//                     return $training;
//                 });

//                 // Calculer les totaux
//                 foreach ($trainings as $training) {
//                     $originalPrice = $training->price;
//                     $totalOriginal += $originalPrice;

//                     // Vérifier si la formation a une remise
//                     if ($training->discount > 0) {
//                         $discountAmount = ($originalPrice * $training->discount) / 100;
//                         $priceAfterDiscount = $originalPrice - $discountAmount;

//                         $totalDiscount += $discountAmount;
//                         $totalAfterDiscount += $priceAfterDiscount;
//                     } else {
//                         $totalAfterDiscount += $originalPrice;
//                     }
//                 }
//             }
//             // Formater le statut de la réservation
//             $statusText = '';
//             switch ($reservation->status) {
//                 case 0:
//                     $statusText = 'En attente';
//                     break;
//                 case 1:
//                     $statusText = 'Confirmée';
//                     break;
//                 default:
//                     $statusText = 'Inconnu';
//             }

//             // Ajouter les informations de l'étudiant et ses réservations
//             $studentInfo = [
//                 'id' => $user->id,
//                 'reservation_id' => $reservation->id,
//                 'nom' => $user->lastname ?? 'N/A',
//                 'prenom' => $user->name ?? 'N/A',
//                 'telephone' => $user->phone ?? 'N/A',
//                 'email' => $user->email ?? 'N/A',
//                 'reservation_date' => $reservation->reservation_date,
//                 'reservation_time' => $reservation->reservation_time,

//                 'status' => $reservation->status,
//                 'status_text' => $statusText,
//                 'formations' => $trainings->map(function($training) {
//                     return [
//                         'id' => $training->id,
//                         'title' => $training->title,
//                         'price' => $training->price,
//                         'discount' => $training->discount ?? 0,
//                         'discount_amount' => $training->discount > 0 ? ($training->price * $training->discount) / 100 : 0,
//                         'price_after_discount' => $training->discount > 0 ?
//                             $training->price - (($training->price * $training->discount) / 100) :
//                             $training->price
//                     ];
//                 }),
//                 'total_original' => $totalOriginal,
//                 'total_discount' => $totalDiscount,
//                 'total_after_discount' => $totalAfterDiscount,
//                 'payment_date' => $reservation->payment_date
//             ];

//             // $studentsWithReservations[] = $studentInfo;
//             $studentsWithReservations[] = [
//                 $studentInfo,
//                 //filtrage par status
//                 'statusReservation' => [
//                     'status' => $reservation->status,
//                     'status_text' => $statusText,
//                 ],
//             ];
//         }
//     }

//     // Retourner la vue existante avec les données
//     return view('admin.apps.reservations.reservations-list', compact('studentsWithReservations'));
// }
public function listStudentsWithReservations(Request $request)
{
    // Initialiser la requête de base pour les réservations
    $query = Reservation::with(['user']);

    // Appliquer le filtre de statut si fourni
    if ($request->filled('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    //  if ($request->filled('search')) {
    //     $searchTerm = $request->search;

    //     $query->where(function($q) use ($searchTerm) {
    //         // Recherche par ID de réservation (clé primaire de la table reservations)
    //         $q->where('reservations.id', '=', $searchTerm)

    //           // Ou recherche dans les infos utilisateur
    //           ->orWhereHas('user', function($userQuery) use ($searchTerm) {
    //               $userQuery->Where('users.phone', 'LIKE', "%{$searchTerm}%") // Téléphone
    //                        ->orWhere('users.name', 'LIKE', "%{$searchTerm}%") // Prénom
    //                        ->orWhere('users.lastname', 'LIKE', "%{$searchTerm}%"); // Nom
    //           });
    //     });
    // }
    if ($request->filled('search') && trim($request->search) !== '') {
        $searchTerm = trim($request->search);
        $query->where(function($q) use ($searchTerm) {
            // Recherche exacte par ID si c'est numérique
            if (is_numeric($searchTerm)) {
                $q->orWhere('reservations.id', '=', (int)$searchTerm);
            }

            // Recherche pour les champs texte (téléphone, nom, prénom)
            $q->orWhereHas('user', function($userQuery) use ($searchTerm) {
                // Pour le téléphone, on utilise LIKE mais avec un traitement spécial
                if (is_numeric($searchTerm)) {
                    // Si c'est numérique, on cherche les numéros qui se terminent par le terme de recherche
                    // ou qui contiennent exactement ce terme
                    $userQuery->where('users.phone', 'LIKE', "%{$searchTerm}")
                            ->orWhere('users.phone', '=', $searchTerm);
                } else {
                    // Si non numérique, recherche standard
                    $userQuery->where('users.phone', 'LIKE', "%{$searchTerm}%");
                }

                // Recherche normale pour nom et prénom
                $userQuery->orWhere('users.name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('users.lastname', 'LIKE', "%{$searchTerm}%");
            });
        });
    }

    // Paginer les résultats - 10 réservations par page
    $reservations = $query->paginate(2)->appends(request()->query());

    $studentsWithReservations = [];

    foreach ($reservations as $reservation) {
        $user = $reservation->user;

        // Vérifier si l'utilisateur existe
        if ($user) {
            // Utiliser les formations stockées dans training_data
            $trainings = [];
            $totalOriginal = 0;
            $totalDiscount = 0;
            $totalAfterDiscount = 0;

            if (!empty($reservation->training_data)) {
                $trainings = collect($reservation->training_data)->map(function($trainingData) {
                    // Convertir les données stockées en objet
                    $training = new \stdClass();
                    $training->id = $trainingData['id'];
                    $training->title = $trainingData['title'];
                    $training->price = $trainingData['price'];
                    $training->discount = $trainingData['discount'] ?? 0;

                    // Calculer le prix après remise
                    if ($training->discount > 0) {
                        $discountAmount = ($training->price * $training->discount) / 100;
                        $training->discount_amount = $discountAmount;
                        $training->price_after_discount = $training->price - $discountAmount;
                    } else {
                        $training->discount_amount = 0;
                        $training->price_after_discount = $training->price;
                    }

                    return $training;
                });

                // Calculer les totaux
                foreach ($trainings as $training) {
                    $originalPrice = $training->price;
                    $totalOriginal += $originalPrice;

                    // Vérifier si la formation a une remise
                    if ($training->discount > 0) {
                        $discountAmount = ($originalPrice * $training->discount) / 100;
                        $priceAfterDiscount = $originalPrice - $discountAmount;

                        $totalDiscount += $discountAmount;
                        $totalAfterDiscount += $priceAfterDiscount;
                    } else {
                        $totalAfterDiscount += $originalPrice;
                    }
                }
            }

            // Formater le statut de la réservation
            $statusText = '';
            switch ($reservation->status) {
                case 0:
                    $statusText = 'En attente';
                    break;
                case 1:
                    $statusText = 'Confirmée';
                    break;
                default:
                    $statusText = 'Inconnu';
            }

            // Ajouter les informations de l'étudiant et ses réservations
            $studentInfo = [
                'id' => $user->id,
                'reservation_id' => $reservation->id,
                'nom' => $user->lastname ?? 'N/A',
                'prenom' => $user->name ?? 'N/A',
                'telephone' => $user->phone ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'reservation_date' => $reservation->reservation_date,
                'reservation_time' => $reservation->reservation_time,
                'status' => $reservation->status,
                'status_text' => $statusText,
                'formations' => $trainings->map(function($training) {
                    return [
                        'id' => $training->id,
                        'title' => $training->title,
                        'price' => $training->price,
                        'discount' => $training->discount ?? 0,
                        'discount_amount' => $training->discount > 0 ? ($training->price * $training->discount) / 100 : 0,
                        'price_after_discount' => $training->discount > 0 ?
                            $training->price - (($training->price * $training->discount) / 100) :
                            $training->price
                    ];
                }),
                'total_original' => $totalOriginal,
                'total_discount' => $totalDiscount,
                'total_after_discount' => $totalAfterDiscount,
                'payment_date' => $reservation->payment_date
            ];

            // Ajouter à la liste des étudiants avec réservations
            $studentsWithReservations[] = $studentInfo;
        }
    }

    // Retourner la vue avec les données paginées
    return view('admin.apps.reservations.reservations-list', [
        'studentsWithReservations' => $studentsWithReservations,
        'reservations' => $reservations
    ]);
}

//originale
// public function showUserReservations() {
//     $userId = Auth::id();

//     // Récupérer toutes les réservations de l'utilisateur
//     $reservations = Reservation::where('user_id', $userId)
//                             ->orderBy('created_at', 'desc')
//                             ->get();

//     // Pour chaque réservation, récupérer les détails des formations
//     foreach ($reservations as $reservation) {
//         // Récupérer le panier associé à la réservation
//         $cart = Cart::find($reservation->cart_id);

//         if ($cart && !empty($cart->training_ids)) {
//             // Récupérer les formations du panier avec toutes les colonnes, y compris l'image
//             $trainings = Training::whereIn('id', $cart->training_ids)->get();

//             // Calculer le prix total et les remises
//             $totalPrice = 0;
//             $totalDiscount = 0;

//             foreach ($trainings as $training) {
//                 $originalPrice = $training->price;
//                 // Vérifier si la formation a une remise
//                 if ($training->discount > 0) {
//                     $discountAmount = ($training->price * $training->discount) / 100;
//                     $training->discount_amount = $discountAmount;
//                     $training->price_after_discount = $training->price - $discountAmount;

//                     $totalPrice += $training->price_after_discount;
//                     $totalDiscount += $discountAmount;
//                 } else {
//                     $training->discount_amount = 0;
//                     $training->price_after_discount = $training->price;
//                     $totalPrice += $training->price;
//                 }
//             }

//             // Ajouter les formations et les infos de prix à la réservation
//             $reservation->trainings = $trainings;
//             $reservation->total_price = $totalPrice;
//             $reservation->total_discount = $totalDiscount;
//             $reservation->original_total = $totalPrice + $totalDiscount;
//         } else {
//             $reservation->trainings = collect();
//             $reservation->total_price = 0;
//             $reservation->total_discount = 0;
//             $reservation->original_total = 0;
//         }
//     }

//     return view('admin.apps.reservations.mes-reservations', compact('reservations'));
// }

public function showUserReservations() {
    // Récupérer toutes les réservations (sans filtrer par utilisateur)
    $reservations = Reservation::orderBy('created_at', 'desc')->get();

    // Pour chaque réservation, utiliser les données stockées
    foreach ($reservations as $reservation) {
        if (!empty($reservation->training_data)) {
            // Convertir les données stockées en collection
            $trainings = collect($reservation->training_data)->map(function($trainingData) {
                $training = new \stdClass();
                $training->id = $trainingData['id'];
                $training->title = $trainingData['title'];
                $training->price = $trainingData['price'];
                $training->discount = $trainingData['discount'] ?? 0;
                // Ajouter l'image à l'objet
                $training->image = $trainingData['image'] ?? null;

                // Récupérer les données du professeur si disponible
                $actualTraining = Training::find($training->id);
                if ($actualTraining && $actualTraining->user) {
                    $training->user = $actualTraining->user;
                }

                // Calculer les prix avec remise
                if ($training->discount > 0) {
                    $discountAmount = ($training->price * $training->discount) / 100;
                    $training->discount_amount = $discountAmount;
                    $training->price_after_discount = $training->price - $discountAmount;
                } else {
                    $training->discount_amount = 0;
                    $training->price_after_discount = $training->price;
                }

                return $training;
            });

            // Calculer le prix total et les remises
            $totalPrice = 0;
            $totalDiscount = 0;

            foreach ($trainings as $training) {
                if ($training->discount > 0) {
                    $totalPrice += $training->price_after_discount;
                    $totalDiscount += $training->discount_amount;
                } else {
                    $totalPrice += $training->price;
                }
            }

            // Ajouter les formations et les infos de prix à la réservation
            $reservation->trainings = $trainings;
            $reservation->total_price = $totalPrice;
            $reservation->total_discount = $totalDiscount;
            $reservation->original_total = $totalPrice + $totalDiscount;
        } else {
            $reservation->trainings = collect();
            $reservation->total_price = 0;
            $reservation->total_discount = 0;
            $reservation->original_total = 0;
        }
    }

    return view('admin.apps.reservations.mes-reservations', compact('reservations'));
}

// public function showUserReservations() {
//     $userId = Auth::id();

//     // Récupérer toutes les réservations de l'utilisateur
//     $reservations = Reservation::where('user_id', $userId)
//                         ->orderBy('created_at', 'desc')
//                         ->get();

//     // Pour chaque réservation, utiliser les données stockées
//     foreach ($reservations as $reservation) {
//         if (!empty($reservation->training_data)) {
//             // Convertir les données stockées en collection
//             $trainings = collect($reservation->training_data)->map(function($trainingData) {
//                 $training = new \stdClass();
//                 $training->id = $trainingData['id'];
//                 $training->title = $trainingData['title'];
//                 $training->price = $trainingData['price'];
//                 $training->discount = $trainingData['discount'] ?? 0;
//                 // Ajouter l'image à l'objet
//                 $training->image = $trainingData['image'] ?? null;

//                 // Calculer les prix avec remise
//                 if ($training->discount > 0) {
//                     $discountAmount = ($training->price * $training->discount) / 100;
//                     $training->discount_amount = $discountAmount;
//                     $training->price_after_discount = $training->price - $discountAmount;
//                 } else {
//                     $training->discount_amount = 0;
//                     $training->price_after_discount = $training->price;
//                 }

//                 return $training;
//             });

//             // Calculer le prix total et les remises
//             $totalPrice = 0;
//             $totalDiscount = 0;

//             foreach ($trainings as $training) {
//                 if ($training->discount > 0) {
//                     $totalPrice += $training->price_after_discount;
//                     $totalDiscount += $training->discount_amount;
//                 } else {
//                     $totalPrice += $training->price;
//                 }
//             }

//             // Ajouter les formations et les infos de prix à la réservation
//             $reservation->trainings = $trainings;
//             $reservation->total_price = $totalPrice;
//             $reservation->total_discount = $totalDiscount;
//             $reservation->original_total = $totalPrice + $totalDiscount;
//         } else {
//             $reservation->trainings = collect();
//             $reservation->total_price = 0;
//             $reservation->total_discount = 0;
//             $reservation->original_total = 0;
//         }
//     }

//     return view('admin.apps.reservations.mes-reservations', compact('reservations'));
// }

/**
 * Supprime une réservation (version administrateur)
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function cancelReservation(Request $request)
{
    Log::info('==== DÉBUT cancelReservation ====');
    Log::info('Données de la requête: ' . print_r($request->all(), true));

    // Vérifier si l'utilisateur est connecté
    if (!Auth::check()) {
        Log::warning('Tentative de suppression sans authentification');
        return response()->json([
            'success' => false,
            'message' => 'Vous devez être connecté pour supprimer une réservation'
        ], 401);
    }

    $reservationId = $request->input('reservation_id');

    if (empty($reservationId)) {
        Log::error('ID de réservation manquant');
        return response()->json([
            'success' => false,
            'message' => 'ID de réservation manquant'
        ], 400);
    }

    try {
        // Rechercher la réservation
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            Log::warning("Réservation non trouvée: {$reservationId}");
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }

        // Supprimer la réservation
        $reservation->delete();

        Log::info("Réservation supprimée avec succès - ID: {$reservationId}");

        return response()->json([
            'success' => true,
            'message' => 'Réservation supprimée avec succès'
        ]);
    } catch (\Exception $e) {
        Log::error("Exception lors de la suppression: " . $e->getMessage());
        Log::error($e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression de la réservation: ' . $e->getMessage()
        ], 500);
    }
}
    // public function updateStatus(Request $request)
    // {
    //     // Enregistrement des informations de débogage
    //     Log::info('==== DÉBUT updateStatus ====');
    //     Log::info('Données de la requête: ' . print_r($request->all(), true));

    //     // Vérifier si l'utilisateur est connecté
    //     if (!Auth::check()) {
    //         Log::warning('Tentative de mise à jour sans authentification');
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Vous devez être connecté pour mettre à jour une réservation'
    //         ], 401);
    //     }

    //     // Récupérer les données de la requête
    //     $reservationId = $request->input('reservation_id');
    //     $newStatus = $request->input('status');

    //     if (!$reservationId || !in_array($newStatus, [0, 1])) {
    //         Log::error('Données de requête invalides: ID=' . $reservationId . ', status=' . $newStatus);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Données de requête invalides'
    //         ], 400);
    //     }

    //     Log::info("Mise à jour de la réservation ID: {$reservationId} vers statut: {$newStatus}");

    //     try {
    //         // Trouver la réservation
    //         $reservation = Reservation::find($reservationId);

    //         if (!$reservation) {
    //             Log::warning("Réservation non trouvée: {$reservationId}");
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Réservation non trouvée'
    //             ], 404);
    //         }

    //         // Vérifier si l'utilisateur a les droits (admin)
    //         // Si nécessaire, ajoutez ici une vérification des permissions

    //         // Mettre à jour directement avec le modèle (déclenche les observers)
    //         $reservation->status = $newStatus;

    //         // Si on passe à payé, définir la date de paiement
    //         if ($newStatus == 1) {
    //             $reservation->payment_date = now();
    //         } else {
    //             $reservation->payment_date = null;
    //         }

    //         $reservation->save();

    //         Log::info("Réservation mise à jour avec succès - ID: {$reservationId}, Nouveau statut: {$newStatus}");
    //         Log::info("Date de paiement: " . ($reservation->payment_date ?? 'null'));

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Statut de la réservation mis à jour avec succès',
    //             'payment_date' => $reservation->payment_date,
    //             'new_status' => $reservation->status
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error("Exception lors de la mise à jour: " . $e->getMessage());
    //         Log::error($e->getTraceAsString());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Affiche la liste des étudiants ayant effectué une réservation
     *
     * @return \Illuminate\Http\Response
     */
    // public function listStudentsWithReservations()
    // {
    //     // Récupérer toutes les réservations avec les relations utilisateur et panier
    //     $reservations = Reservation::with(['user', 'cart'])->get();

    //     $studentsWithReservations = [];

    //     foreach ($reservations as $reservation) {
    //         $user = $reservation->user;
    //         $cart = $reservation->cart;

    //         // Vérifier si l'utilisateur et le panier existent
    //         if ($user && $cart) {
    //             // Récupérer les formations du panier
    //             $trainings = [];
    //             if (!empty($cart->training_ids)) {
    //                 $trainings = Training::whereIn('id', $cart->training_ids)->get();
    //             }

    //             // Formater le statut de la réservation
    //             $statusText = '';
    //             switch ($reservation->status) {
    //                 case 0:
    //                     $statusText = 'Non payé';
    //                     break;
    //                 case 1:
    //                     $statusText = 'Payé';
    //                     break;
    //                 default:
    //                     $statusText = 'Inconnu';
    //             }

    //             // Ajouter les informations de l'étudiant et ses réservations
    //             // Avec l'ID de réservation bien mis en évidence
    //             $studentInfo = [
    //                 'id' => $user->id,
    //                 'reservation_id' => $reservation->id,
    //                 'nom' => $user->lastname ?? 'N/A',
    //                 'prenom' => $user->name ?? 'N/A',
    //                 'telephone' => $user->phone ?? 'N/A',
    //                 'email' => $user->email ?? 'N/A',
    //                 'reservation_date' => $reservation->reservation_date,
    //                 'status' => $reservation->status,
    //                 'status_text' => $statusText,
    //                 'formations' => $trainings->map(function($training) {
    //                     return [
    //                         'id' => $training->id,
    //                         'title' => $training->title,
    //                         'price' => $training->price
    //                     ];
    //                 }),
    //                 'payment_date' => $reservation->payment_date
    //             ];

    //             $studentsWithReservations[] = $studentInfo;
    //         }
    //     }

    //     // Retourner la vue existante avec les données
    //     return view('admin.apps.reservations.reservations-list', compact('studentsWithReservations'));
    // }
    // public function updateStatus(Request $request)
    // {
    //     // Vérifier si l'utilisateur est connecté
    //     if (!Auth::check()) {
    //         return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
    //     }

    //     // Récupérer les données de la requête
    //     $reservationId = $request->input('reservation_id');
    //     $newStatus = $request->input('status');

    //     if (!$reservationId || !in_array($newStatus, [0, 1])) {
    //         return redirect()->back()->with('error', 'Données de requête invalides');
    //     }

    //     try {
    //         // Trouver la réservation
    //         $reservation = Reservation::find($reservationId);

    //         if (!$reservation) {
    //             return redirect()->back()->with('error', 'Réservation non trouvée');
    //         }


    //         // Mettre à jour directement avec le modèle (déclenche les observers)
    //         $reservation->status = $newStatus;

    //         // Si on passe à payé, définir la date de paiement
    //         if ($newStatus == 1) {
    //             $reservation->payment_date = now();
    //         } else {
    //             $reservation->payment_date = null;
    //         }

    //         $reservation->save();

    //         return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');

    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
    //     }
    // }

//     public function updateStatus(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
//     }

//     // Récupérer les données de la requête
//     $reservationId = $request->input('reservation_id');
//     $newStatus = $request->input('status');

//     if (!$reservationId || !in_array($newStatus, [0, 1])) {
//         return redirect()->back()->with('error', 'Données de requête invalides');
//     }

//     try {
//         // Trouver la réservation
//         $reservation = Reservation::with('user')->find($reservationId);

//         if (!$reservation) {
//             return redirect()->back()->with('error', 'Réservation non trouvée');
//         }

//         // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
//         $oldStatus = $reservation->status;

//         // Mettre à jour directement avec le modèle (déclenche les observers)
//         $reservation->status = $newStatus;

//         // Si on passe à payé, définir la date de paiement
//         if ($newStatus == 1) {
//             $reservation->payment_date = now();
//         } else {
//             $reservation->payment_date = null;
//         }

//         $reservation->save();

//         // Envoyer l'email uniquement si le statut passe de 0 à 1
//         if ($oldStatus == 0 && $newStatus == 1 && $reservation->user) {
//             try {
//                 // Envoyer l'email de confirmation
//                 Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation));

//                 // Ajouter un message de succès pour l'email
//                 return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
//             } catch (\Exception $emailError) {
//                 // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
//                 Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
//                 return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé.');
//             }
//         }

//         return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');

//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
//         return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//     }
// }


// public function updateStatus(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
//     }

//     // Récupérer les données de la requête
//     $reservationId = $request->input('reservation_id');
//     $newStatus = $request->input('status');

//     Log::info("Tentative de mise à jour du statut de la réservation ID: {$reservationId} vers statut: {$newStatus}");

//     if (!$reservationId || !in_array($newStatus, [0, 1])) {
//         Log::error("Données invalides: reservation_id={$reservationId}, status={$newStatus}");
//         return redirect()->back()->with('error', 'Données de requête invalides');
//     }

//     try {
//         // Trouver la réservation avec l'utilisateur associé
//         $reservation = Reservation::with('user')->find($reservationId);

//         if (!$reservation) {
//             Log::error("Réservation non trouvée: {$reservationId}");
//             return redirect()->back()->with('error', 'Réservation non trouvée');
//         }

//         // Vérifier si l'utilisateur associé existe
//         if (!$reservation->user) {
//             Log::error("Aucun utilisateur associé à la réservation ID: {$reservationId}");
//             return redirect()->back()->with('error', 'Aucun utilisateur associé à cette réservation');
//         }

//         // Vérifier si l'email de l'utilisateur est défini
//         if (empty($reservation->user->email)) {
//             Log::error("Email utilisateur non défini pour la réservation ID: {$reservationId}, user ID: {$reservation->user_id}");
//             return redirect()->back()->with('error', 'Email utilisateur non défini');
//         }

//         Log::info("Email de l'utilisateur: " . $reservation->user->email);

//         // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
//         $oldStatus = $reservation->status;

//         // Mettre à jour directement avec le modèle
//         $reservation->status = $newStatus;

//         // Si on passe à payé, définir la date de paiement
//         if ($newStatus == 1) {
//             $reservation->payment_date = now();
//             Log::info("Date de paiement définie: " . $reservation->payment_date);
//         } else {
//             $reservation->payment_date = null;
//         }

//         $reservation->save();
//         Log::info("Réservation mise à jour avec succès, ancien statut: {$oldStatus}, nouveau statut: {$newStatus}");

//         // Envoyer l'email uniquement si le statut passe de 0 à 1
//       if ($oldStatus == 0 && $newStatus == 1) {
//         try {
//             Log::info("Tentative d'envoi d'email à: " . $reservation->user->email);

//             // Récupérer le panier associé
//             $cart = $reservation->cart;
//             $totalPrice = 0;

//             // Calculer le prix total si le panier existe
//             if ($cart && !empty($cart->training_ids)) {
//                 $trainings = Training::whereIn('id', $cart->training_ids)->get();

//                 foreach ($trainings as $training) {
//                     if ($training->discount > 0) {
//                         $totalPrice += $training->price - ($training->price * $training->discount / 100);
//                     } else {
//                         $totalPrice += $training->price;
//                     }
//                 }
//             }

//             // Envoyer l'email de confirmation avec le total
//             Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation, $totalPrice));

//             Log::info("Email envoyé avec succès");

//             return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
//             } catch (\Exception $emailError) {
//                 // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
//                 Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
//                 Log::error($emailError->getTraceAsString());
//                 return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé: ' . $emailError->getMessage());
//             }
//         }

//         return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');

//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
//         Log::error($e->getTraceAsString());
//         return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//     }
// }

//   public function updateStatus(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
//     }

//     // Récupérer les données de la requête
//     $reservationId = $request->input('reservation_id');
//     $newStatus = $request->input('status');

//     Log::info("Tentative de mise à jour du statut de la réservation ID: {$reservationId} vers statut: {$newStatus}");

//     if (!$reservationId || !in_array($newStatus, [0, 1])) {
//         Log::error("Données invalides: reservation_id={$reservationId}, status={$newStatus}");
//         return redirect()->back()->with('error', 'Données de requête invalides');
//     }

//     try {
//         // Trouver la réservation avec l'utilisateur associé
//         $reservation = Reservation::with('user')->find($reservationId);

//         if (!$reservation) {
//             Log::error("Réservation non trouvée: {$reservationId}");
//             return redirect()->back()->with('error', 'Réservation non trouvée');
//         }

//         // Vérifier si l'utilisateur associé existe
//         if (!$reservation->user) {
//             Log::error("Aucun utilisateur associé à la réservation ID: {$reservationId}");
//             return redirect()->back()->with('error', 'Aucun utilisateur associé à cette réservation');
//         }

//         // Vérifier si l'email de l'utilisateur est défini
//         if (empty($reservation->user->email)) {
//             Log::error("Email utilisateur non défini pour la réservation ID: {$reservationId}, user ID: {$reservation->user_id}");
//             return redirect()->back()->with('error', 'Email utilisateur non défini');
//         }

//         Log::info("Email de l'utilisateur: " . $reservation->user->email);

//         // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
//         $oldStatus = $reservation->status;

//         // Mettre à jour directement avec le modèle
//         $reservation->status = $newStatus;

//         // Si on passe à payé, définir la date de paiement
//         if ($newStatus == 1) {
//             $reservation->payment_date = now();
//             Log::info("Date de paiement définie: " . $reservation->payment_date);
//             $cart = Cart::find($reservation->cart_id);
//             if ($cart) {
//                 // Vider le panier (pas le supprimer)
//                 $cart->training_ids = [];
//                 $cart->save();
//                 Log::info("Panier vidé (ID: {$cart->id}) après confirmation du paiement");
//             }
//         } else {
//             $reservation->payment_date = null;
//         }

//         $reservation->save();
//         Log::info("Réservation mise à jour avec succès, ancien statut: {$oldStatus}, nouveau statut: {$newStatus}");

//         // Envoyer l'email uniquement si le statut passe de 0 à 1
//       if ($oldStatus == 0 && $newStatus == 1) {
//         try {
//             Log::info("Tentative d'envoi d'email à: " . $reservation->user->email);

//             // Récupérer le panier associé
//             $cart = $reservation->cart;
//             $totalPrice = 0;

//             // Calculer le prix total si le panier existe
//             if ($cart && !empty($cart->training_ids)) {
//                 $trainings = Training::whereIn('id', $cart->training_ids)->get();

//                 foreach ($trainings as $training) {
//                     if ($training->discount > 0) {
//                         $totalPrice += $training->price - ($training->price * $training->discount / 100);
//                     } else {
//                         $totalPrice += $training->price;
//                     }
//                 }
//             }

//             // Envoyer l'email de confirmation avec le total
//             Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation, $totalPrice));

//             Log::info("Email envoyé avec succès");

//             return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
//             } catch (\Exception $emailError) {
//                 // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
//                 Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
//                 Log::error($emailError->getTraceAsString());
//                 return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé: ' . $emailError->getMessage());
//             }
//         }

//         return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');

//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
//         Log::error($e->getTraceAsString());
//         return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//     }
// }

// public function updateStatus(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
//     }

//     // Récupérer les données de la requête
//     $reservationId = $request->input('reservation_id');
//     $newStatus = $request->input('status');

//     Log::info("Tentative de mise à jour du statut de la réservation ID: {$reservationId} vers statut: {$newStatus}");

//     if (!$reservationId || !in_array($newStatus, [0, 1])) {
//         Log::error("Données invalides: reservation_id={$reservationId}, status={$newStatus}");
//         return redirect()->back()->with('error', 'Données de requête invalides');
//     }

//     try {
//         // Trouver la réservation avec l'utilisateur associé
//         $reservation = Reservation::with('user')->find($reservationId);

//         if (!$reservation) {
//             Log::error("Réservation non trouvée: {$reservationId}");
//             return redirect()->back()->with('error', 'Réservation non trouvée');
//         }

//         // Vérifier si l'utilisateur associé existe
//         if (!$reservation->user) {
//             Log::error("Aucun utilisateur associé à la réservation ID: {$reservationId}");
//             return redirect()->back()->with('error', 'Aucun utilisateur associé à cette réservation');
//         }

//         // Vérifier si l'email de l'utilisateur est défini
//         if (empty($reservation->user->email)) {
//             Log::error("Email utilisateur non défini pour la réservation ID: {$reservationId}, user ID: {$reservation->user_id}");
//             return redirect()->back()->with('error', 'Email utilisateur non défini');
//         }

//         Log::info("Email de l'utilisateur: " . $reservation->user->email);

//         // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
//         $oldStatus = $reservation->status;

//         // Mettre à jour directement avec le modèle
//         $reservation->status = $newStatus;

//         // Si on passe à payé, définir la date de paiement
//         if ($newStatus == 1) {
//             $reservation->payment_date = now();
//             Log::info("Date de paiement définie: " . $reservation->payment_date);

//             // S'assurer que les données des formations sont bien sauvegardées dans training_data
//             // avant de vider le panier
//             if (empty($reservation->training_data) && $reservation->cart_id) {
//                 // Récupérer le panier
//                 $cart = Cart::find($reservation->cart_id);
//                 if ($cart && !empty($cart->training_ids)) {
//                     // Récupérer les formations et les stocker
//                     $trainings = Training::whereIn('id', $cart->training_ids)->get();
//                     // Stocker les informations essentielles des formations
//                     $trainingsData = $trainings->map(function($training) {
//                         return [
//                             'id' => $training->id,
//                             'title' => $training->title,
//                             'price' => $training->price,
//                             'discount' => $training->discount ?? 0,
//                             'image' => $training->image,
//                         ];
//                     })->toArray();

//                     $reservation->training_data = $trainingsData;
//                     $reservation->save();
//                     Log::info("Données des formations copiées dans la réservation avant de vider le panier");
//                 }
//             }

//             // Vider le panier après avoir sauvegardé les données
//             $cart = Cart::find($reservation->cart_id);
//             if ($cart) {
//                 // Vider le panier (pas le supprimer)
//                 $cart->training_ids = [];
//                 $cart->save();
//                 Log::info("Panier vidé (ID: {$cart->id}) après confirmation du paiement");
//             }
//         } else {
//             $reservation->payment_date = null;
//         }

//         $reservation->save();
//         Log::info("Réservation mise à jour avec succès, ancien statut: {$oldStatus}, nouveau statut: {$newStatus}");

//         // Envoyer l'email uniquement si le statut passe de 0 à 1
//         if ($oldStatus == 0 && $newStatus == 1) {
//             try {
//                 Log::info("Tentative d'envoi d'email à: " . $reservation->user->email);

//                 // Calculer le prix total à partir des données stockées
//                 $totalPrice = 0;

//                 if (!empty($reservation->training_data)) {
//                     foreach ($reservation->training_data as $trainingData) {
//                         $price = $trainingData['price'];
//                         $discount = $trainingData['discount'] ?? 0;

//                         if ($discount > 0) {
//                             $totalPrice += $price - ($price * $discount / 100);
//                         } else {
//                             $totalPrice += $price;
//                         }
//                     }
//                 }

//                 // Envoyer l'email de confirmation avec le total
//                 Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation, $totalPrice));

//                 Log::info("Email envoyé avec succès");

//                 return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
//             } catch (\Exception $emailError) {
//                 // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
//                 Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
//                 Log::error($emailError->getTraceAsString());
//                 return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé: ' . $emailError->getMessage());
//             }
//         }

//         return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');

//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
//         Log::error($e->getTraceAsString());
//         return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//     }
// }

/**
 * Met à jour le statut d'une réservation
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
// public function updateStatus(Request $request)
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
//     }

//     // Récupérer les données de la requête
//     $reservationId = $request->input('reservation_id');
//     $newStatus = $request->input('status');

//     Log::info("Tentative de mise à jour du statut de la réservation ID: {$reservationId} vers statut: {$newStatus}");

//     if (!$reservationId || !in_array($newStatus, [0, 1])) {
//         Log::error("Données invalides: reservation_id={$reservationId}, status={$newStatus}");
//         return redirect()->back()->with('error', 'Données de requête invalides');
//     }

//     try {
//         // Trouver la réservation avec l'utilisateur associé
//         $reservation = Reservation::with('user')->find($reservationId);

//         if (!$reservation) {
//             Log::error("Réservation non trouvée: {$reservationId}");
//             return redirect()->back()->with('error', 'Réservation non trouvée');
//         }

//         // Vérifier si l'utilisateur associé existe
//         if (!$reservation->user) {
//             Log::error("Aucun utilisateur associé à la réservation ID: {$reservationId}");
//             return redirect()->back()->with('error', 'Aucun utilisateur associé à cette réservation');
//         }

//         // Vérifier si l'email de l'utilisateur est défini
//         if (empty($reservation->user->email)) {
//             Log::error("Email utilisateur non défini pour la réservation ID: {$reservationId}, user ID: {$reservation->user_id}");
//             return redirect()->back()->with('error', 'Email utilisateur non défini');
//         }

//         Log::info("Email de l'utilisateur: " . $reservation->user->email);

//         // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
//         $oldStatus = $reservation->status;

//         // Si le statut passe à 1 (payé)
//         if ($newStatus == 1 && $oldStatus != $newStatus) {
//             // Définir la date de paiement
//             $reservation->payment_date = now();
//             Log::info("Date de paiement définie: " . $reservation->payment_date);

//             // S'assurer que les données des formations sont bien sauvegardées
//             if (empty($reservation->training_data) && $reservation->cart_id) {
//                 // Récupérer le panier
//                 $cart = Cart::find($reservation->cart_id);
//                 if ($cart && !empty($cart->training_ids)) {
//                     // Récupérer les formations et les stocker
//                     $trainings = Training::whereIn('id', $cart->training_ids)->get();
//                     // Stocker les informations essentielles des formations
//                     $trainingsData = $trainings->map(function($training) {
//                         return [
//                             'id' => $training->id,
//                             'title' => $training->title,
//                             'price' => $training->price,
//                             'discount' => $training->discount ?? 0,
//                             'image' => $training->image,
//                         ];
//                     })->toArray();

//                     $reservation->training_data = $trainingsData;
//                     Log::info("Données des formations copiées dans la réservation avant de vider le panier");
//                 }
//             }

//             // Mise à jour du statut
//             $reservation->status = $newStatus;
//             $reservation->save();

//             // Vider le panier APRÈS avoir sauvegardé la réservation
//             if ($reservation->cart_id) {
//                 $cart = Cart::find($reservation->cart_id);
//                 if ($cart) {
//                     // Vider complètement le panier
//                     $cart->training_ids = [];
//                     $cart->save();
//                     Log::info("Panier vidé (ID: {$cart->id}) après confirmation du paiement");
//                 } else {
//                     Log::warning("Panier introuvable (ID: {$reservation->cart_id})");
//                 }
//             } else {
//                 Log::warning("Pas de cart_id associé à la réservation");
//             }

//             // Envoyer l'email de confirmation
//             try {
//                 Log::info("Tentative d'envoi d'email à: " . $reservation->user->email);

//                 // Calculer le prix total à partir des données stockées
//                 $totalPrice = 0;

//                 if (!empty($reservation->training_data)) {
//                     foreach ($reservation->training_data as $trainingData) {
//                         $price = $trainingData['price'];
//                         $discount = $trainingData['discount'] ?? 0;

//                         if ($discount > 0) {
//                             $totalPrice += $price - ($price * $discount / 100);
//                         } else {
//                             $totalPrice += $price;
//                         }
//                     }
//                 }

//                 // Envoyer l'email de confirmation avec le total
//                 Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation, $totalPrice));

//                 Log::info("Email envoyé avec succès");

//                 return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
//             } catch (\Exception $emailError) {
//                 // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
//                 Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
//                 Log::error($emailError->getTraceAsString());
//                 return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé: ' . $emailError->getMessage());
//             }
//         } else {
//             // Si on ne passe pas à payé, simplement mettre à jour le statut
//             $reservation->status = $newStatus;
//             if ($newStatus != 1) {
//                 $reservation->payment_date = null;
//             }
//             $reservation->save();

//             Log::info("Réservation mise à jour avec succès, ancien statut: {$oldStatus}, nouveau statut: {$newStatus}");
//             return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');
//         }

//     } catch (\Exception $e) {
//         Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
//         Log::error($e->getTraceAsString());
//         return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
//     }
// }

/**
 * Met à jour le statut d'une réservation
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function updateStatus(Request $request) {
    // Vérifier si l'utilisateur est connecté
    if (!Auth::check()) {
        return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
    }

    // Récupérer les données de la requête
    $reservationId = $request->input('reservation_id');
    $newStatus = $request->input('status');

    Log::info("Tentative de mise à jour du statut de la réservation ID: {$reservationId} vers statut: {$newStatus}");

    if (!$reservationId || !in_array($newStatus, [0, 1])) {
        Log::error("Données invalides: reservation_id={$reservationId}, status={$newStatus}");
        return redirect()->back()->with('error', 'Données de requête invalides');
    }

    try {
        // Trouver la réservation avec l'utilisateur associé
        $reservation = Reservation::with('user')->find($reservationId);

        if (!$reservation) {
            Log::error("Réservation non trouvée: {$reservationId}");
            return redirect()->back()->with('error', 'Réservation non trouvée');
        }

        // Vérifier si l'utilisateur associé existe
        if (!$reservation->user) {
            Log::error("Aucun utilisateur associé à la réservation ID: {$reservationId}");
            return redirect()->back()->with('error', 'Aucun utilisateur associé à cette réservation');
        }

        // Vérifier si l'email de l'utilisateur est défini
        if (empty($reservation->user->email)) {
            Log::error("Email utilisateur non défini pour la réservation ID: {$reservationId}, user ID: {$reservation->user_id}");
            return redirect()->back()->with('error', 'Email utilisateur non défini');
        }

        Log::info("Email de l'utilisateur: " . $reservation->user->email);

        // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
        $oldStatus = $reservation->status;

        // Récupérer le panier associé à la réservation
        $cart = Cart::find($reservation->cart_id);

        // Copier les données des formations du panier
        if ($cart && !empty($cart->training_ids)) {
            // Récupérer les formations du panier
            $trainings = Training::whereIn('id', $cart->training_ids)->get();

            // Préparer les données des formations à stocker
            $trainingsData = $trainings->map(function($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'price' => $training->price,
                    'discount' => $training->discount ?? 0,
                    'image' => $training->image,
                ];
            })->toArray();

            // Stocker les données des formations dans la réservation
            $reservation->training_data = $trainingsData;
        }

        // Si le statut passe à 1 (payé)
        if ($newStatus == 1 && $oldStatus != $newStatus) {
            // Définir la date de paiement
            $reservation->payment_date = now();
            Log::info("Date de paiement définie: " . $reservation->payment_date);

            // Mettre à jour le statut de la réservation
            $reservation->status = $newStatus;
            $reservation->save();

            // Vider les training_ids du panier
            if ($cart) {
                $cart->training_ids = [];
                $cart->save();
                Log::info("Panier vidé (ID: {$cart->id}) après confirmation du paiement");
            }

            // Envoyer l'email de confirmation
            try {
                Log::info("Tentative d'envoi d'email à: " . $reservation->user->email);

                // Calculer le prix total à partir des données stockées
                $totalPrice = 0;

                if (!empty($reservation->training_data)) {
                    foreach ($reservation->training_data as $trainingData) {
                        $price = $trainingData['price'];
                        $discount = $trainingData['discount'] ?? 0;

                        if ($discount > 0) {
                            $totalPrice += $price - ($price * $discount / 100);
                        } else {
                            $totalPrice += $price;
                        }
                    }
                }

                // Envoyer l'email de confirmation avec le total
                Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation, $totalPrice));

                Log::info("Email envoyé avec succès");

                return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
            } catch (\Exception $emailError) {
                // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
                Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
                Log::error($emailError->getTraceAsString());
                return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé: ' . $emailError->getMessage());
            }
        } else {
            // Mettre à jour le statut de la réservation
            $reservation->status = $newStatus;

            // Réinitialiser la date de paiement si le statut n'est pas payé
            if ($newStatus != 1) {
                $reservation->payment_date = null;
            }

            $reservation->save();

            Log::info("Réservation mise à jour avec succès, ancien statut: {$oldStatus}, nouveau statut: {$newStatus}");
            return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');
        }

    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
    }

}

/**
 * Télécharger la facture de la réservation au format PDF
 *
 * @param  \App\Models\Reservation  $reservation
 * @return \Illuminate\Http\Response
 */
public function downloadInvoice(Reservation $reservation)
{
    // Vérification que l'utilisateur connecté est bien le propriétaire de la réservation


    // Génération du PDF
    $pdf = $reservation->generateInvoicePdf();

    // Construction du nom de fichier
    $filename = 'facture_reservation_' . $reservation->id . '_' . date('Y-m-d') . '.pdf';

    // Retourne le PDF en téléchargement
    return $pdf->download($filename);
}
// public function checkPendingReservation()
// {
//     // Vérifier si l'utilisateur est connecté
//     if (!Auth::check()) {
//         return response()->json([
//             'hasPendingReservation' => false
//         ]);
//     }

//     $userId = Auth::id();

//     // Vérifier si l'utilisateur a une réservation avec status = 0 (en attente de paiement)
//     $pendingReservation = Reservation::where('user_id', $userId)
//                             ->where('status', 0)
//                             ->first();

//     return response()->json([
//         'hasPendingReservation' => $pendingReservation ? true : false
//     ]);
// }

/**
 * Vérifie si l'utilisateur a une réservation confirmée et si le panier contient de nouvelles formations
 *
 * @return \Illuminate\Http\Response
 */
public function checkNewCartItems()
{
    if (!Auth::check()) {
        return response()->json([
            'hasConfirmedReservation' => false
        ]);
    }

    $userId = Auth::id();

    // Vérifier si l'utilisateur a une réservation confirmée (status = 1)
    $confirmedReservation = Reservation::where('user_id', $userId)
                                ->where('status', 1)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Si l'utilisateur a une réservation confirmée
    if ($confirmedReservation) {
        // Vérifier si le panier contient des formations
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart && !empty($cart->training_ids)) {
            return response()->json([
                'hasConfirmedReservation' => true,
                'shouldCreateNewReservation' => true
            ]);
        }
    }

    return response()->json([
        'hasConfirmedReservation' => false,
        'shouldCreateNewReservation' => false
    ]);
}
}
