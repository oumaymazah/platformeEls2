<?php
namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\Reservation;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
     class PanierController extends Controller    {
        public function index()
{
    $userId = Auth::id() ?? session()->getId();
    $carts = Cart::where('user_id', $userId)->get();
    $panierItems = collect();
    $totalItemsCount = 0;

    foreach ($carts as $cart) {
        $trainingIds = $cart->training_ids ?: [];
        $totalItemsCount += count($trainingIds);
        $trainings = Training::whereIn('id', $trainingIds)->get();
        foreach ($trainings as $training) {
            $item = new \stdClass();
            $item->cart_id = $cart->id;
            $item->Training = $training;
            $panierItems->push($item);
        }
    }

    $totalPrice = 0;
    $totalWithoutDiscount = 0;
    $discountedItemsOriginalPrice = 0;
    $discountedItemsFinalPrice = 0;
    $hasDiscount = false;

    // Ajouter des variables de débogage
    $debugItems = [];

    foreach ($panierItems as $item) {
        if ($item->Training && $item->Training->price !== null) {
            $originalPrice = (float) $item->Training->price;
            $trainingId = $item->Training->id;
            $discountPercent = $item->Training->discount;

            // Ajouter au total sans remise
            $totalWithoutDiscount += $originalPrice;

            // Créer un objet de débogage pour chaque formation
            $debugInfo = [
                'id' => $trainingId,
                'title' => $item->Training->title ?? 'Sans titre',
                'originalPrice' => $originalPrice,
                'discount' => $discountPercent,
            ];

            // Si la formation a une remise
            if ($discountPercent > 0) {
                $hasDiscount = true;

                // Calcul explicite avec étapes intermédiaires
                $discountMultiplier = (100 - $discountPercent) / 100;
                $discountedPrice = $originalPrice * $discountMultiplier;

                // Vérification alternative du calcul
                $discountAmount = ($originalPrice * $discountPercent) / 100;
                $verifiedPrice = $originalPrice - $discountAmount;

                // Débogage des calculs
                $debugInfo['calculMethod1'] = "$originalPrice * (1 - $discountPercent/100) = $discountedPrice";
                $debugInfo['calculMethod2'] = "$originalPrice - ($originalPrice * $discountPercent/100) = $verifiedPrice";

                // Accumuler les prix pour les éléments avec remise
                $discountedItemsOriginalPrice += $originalPrice;
                $discountedItemsFinalPrice += $discountedPrice;

                // Ajouter le prix avec remise au total
                $totalPrice += $discountedPrice;

                // Stocker le prix final dans l'item pour l'affichage
                $item->Training->final_price = $discountedPrice;
            } else {
                // Ajouter le prix original au total
                $totalPrice += $originalPrice;
                $item->Training->final_price = $originalPrice;
                $debugInfo['finalPrice'] = $originalPrice;
            }

            // Ajouter l'info de débogage
            $debugItems[] = $debugInfo;
        }

        // Calcul des feedbacks
        if ($item->Training) {
            $item->Training->total_feedbacks = $item->Training && $item->Training->feedbacks ? $item->Training->feedbacks->count() : 0;
            $item->Training->average_rating = $item->Training && $item->Training->total_feedbacks > 0
                ? round($item->Training->feedbacks->sum('rating_count') / $item->Training->total_feedbacks, 1)
                : 0;
        }
    }

    // Calcul du pourcentage de remise global
    $discountPercentage = 0;
    if ($totalWithoutDiscount > 0 && $hasDiscount) {
        $discountPercentage = round(100 - ($totalPrice / $totalWithoutDiscount * 100));
    }

    // Vérifiez si la requête est AJAX
    if (request()->ajax() || request()->wantsJson()) {
        return response()->json([
            'panierItems' => $panierItems,
            'totalPrice' => $totalPrice,
            'totalWithoutDiscount' => $totalWithoutDiscount,
            'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
            'discountedItemsFinalPrice' => $discountedItemsFinalPrice,
            'discountPercentage' => $discountPercentage,
            'hasDiscount' => $hasDiscount,
            'cartCount' => $totalItemsCount,
            'debug' => $debugItems // Ajouter les informations de débogage
        ]);
    }

    // Sinon, retournez la vue HTML
    return view('admin.apps.formation.panier', [
        'panierItems' => $panierItems,
        'totalPrice' => $totalPrice,
        'totalWithoutDiscount' => $totalWithoutDiscount,
        'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
        'discountedItemsFinalPrice' => $discountedItemsFinalPrice,
        'discountPercentage' => $discountPercentage,
        'hasDiscount' => $hasDiscount,
        'cartCount' => $totalItemsCount,
        'debug' => $debugItems // Ajouter les informations de débogage pour la vue
    ]);
}

//          public function index()
//         {
//             $userId = Auth::id() ?? session()->getId();
//             $carts = Cart::where('user_id', $userId)->get();
//             $panierItems = collect();
//             $totalItemsCount = 0; // Variable pour compter les formations
//             foreach ($carts as $cart) {
//                 $trainingIds = $cart->training_ids ?: [];
//                 $totalItemsCount += count($trainingIds); // Compter les formations
//                 $trainings = Training::whereIn('id', $trainingIds)->get();
//                 foreach ($trainings as $training) {
//                     $item = new \stdClass();
//                     $item->cart_id = $cart->id;
//                     $item->Training = $training;
//                     $panierItems->push($item);
//                 }
//             }
//           // Dans votre méthode index(), remplacez la section de calcul par ceci :

// $totalPrice = 0;
// $totalWithoutDiscount = 0;
// $discountedItemsOriginalPrice = 0;
// $discountedItemsFinalPrice = 0;
// $hasDiscount = false;

// foreach ($panierItems as $item) {
//     if ($item->Training && $item->Training->price !== null) {
//         $originalPrice = (float) $item->Training->price;

//         // Ajouter au total sans remise (même pour les formations gratuites)
//         $totalWithoutDiscount += $originalPrice;

//         // Si la formation a une remise
//         if ($item->Training->discount > 0) {
//             $hasDiscount = true;
//             $discountedPrice = $originalPrice * (1 - $item->Training->discount / 100);

//             // Accumuler les prix pour les éléments avec remise
//             $discountedItemsOriginalPrice += $originalPrice;
//             $discountedItemsFinalPrice += $discountedPrice;

//             // Ajouter le prix avec remise au total
//             $totalPrice += $discountedPrice;
//         } else {
//             // Ajouter le prix original au total (y compris 0 pour les formations gratuites)
//             $totalPrice += $originalPrice;
//         }
//     }

//     // Calcul des feedbacks (garder votre code existant)
//     if ($item->Training) {
//         $item->Training->total_feedbacks = $item->Training && $item->Training->feedbacks ? $item->Training->feedbacks->count() : 0;
//         $item->Training->average_rating = $item->Training && $item->Training->total_feedbacks > 0
//             ? round($item->Training->feedbacks->sum('rating_count') / $item->Training->total_feedbacks, 1)
//             : 0;
//     }
// }

// // Calcul du pourcentage de remise global
// $discountPercentage = 0;
// if ($totalWithoutDiscount > 0 && $hasDiscount) {
//     $discountPercentage = round(100 - ($totalPrice / $totalWithoutDiscount * 100));
// }

// // Alternative: calcul du pourcentage uniquement sur les éléments avec remise
// $individualDiscountPercentage = 0;
// if ($discountedItemsOriginalPrice > 0 && $hasDiscount) {
//     $individualDiscountPercentage = round(100 - ($discountedItemsFinalPrice / $discountedItemsOriginalPrice * 100));
// }
//             // Vérifiez si la requête est AJAX
//             if (request()->ajax() || request()->wantsJson()) {
//                 return response()->json([
//                     'panierItems' => $panierItems,
//                     'totalPrice' => $totalPrice,
//                     'totalWithoutDiscount' => $totalWithoutDiscount,
//                     'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
//                     'discountedItemsFinalPrice' => $discountedItemsFinalPrice,
//                     'discountPercentage' => $discountPercentage,
//                     'hasDiscount' => $hasDiscount,
//                     'cartCount' => $totalItemsCount
//                 ]);
//             }
//             // Sinon, retournez la vue HTML
//             return view('admin.apps.formation.panier', [
//                 'panierItems' => $panierItems,
//                 'totalPrice' => $totalPrice,
//                 'totalWithoutDiscount' => $totalWithoutDiscount,
//                 'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
//                 'discountedItemsFinalPrice' => $discountedItemsFinalPrice,
//                 'discountPercentage' => $discountPercentage,
//                 'hasDiscount' => $hasDiscount,
//                 'cartCount' => $totalItemsCount
//             ]);
//         }
        public function getItemsCount()
        {
            $userId = Auth::id() ?? session()->getId();
            $cart = Cart::where('user_id', $userId)->first();

            $count = 0;
            if ($cart && is_array($cart->training_ids)) {
                $count = count($cart->training_ids);
            }

            return response()->json(['count' => $count]);
        }
        public function ajouter(Request $request)
{
    try {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
        ]);
        $userId = Auth::id() ?? session()->getId();
        $formationId = $request->training_id;

        // Récupérer le panier de l'utilisateur ou en créer un nouveau
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->training_ids = [$formationId]; // Le cast s'occupera de la conversion en JSON
            $cart->save();
        } else {
            // Récupérer les formations déjà dans le panier (déjà converti en array par le cast)
            $trainingIds = $cart->training_ids ?: [];
            // Vérifier si la formation existe déjà dans le panier
            if (in_array($formationId, $trainingIds)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cette formation est déjà dans votre panier',
                    'cartCount' => count($trainingIds)
                ]);
            }
            // Ajouter la nouvelle formation
            $trainingIds[] = $formationId;
            $cart->training_ids = $trainingIds; // Le cast s'occupera de la conversion en JSON
            $cart->save();
        }
        // NOUVEAU CODE: Synchroniser avec les réservations en attente
        if (Auth::check()) {
            $pendingReservations = Reservation::where('user_id', $userId)
                ->where('status', 0) // Réservations en attente uniquement
                ->get();

            if ($pendingReservations->isNotEmpty()) {
                // Récupérer les informations de la formation à ajouter
                $training = Training::find($formationId);

                if ($training) {
                    // Ajouter simplement l'ID de la formation comme string
                    $trainingData = (string)$training->id;

                    foreach ($pendingReservations as $reservation) {
                        // S'assurer que training_data est toujours un tableau
                        $existingTrainingData = [];

                        // Vérifier si training_data est déjà défini
                        if ($reservation->training_data !== null) {
                            if (is_array($reservation->training_data)) {
                                $existingTrainingData = $reservation->training_data;
                            } elseif (is_string($reservation->training_data)) {
                                // Si c'est une chaîne JSON, essayer de la décoder
                                $decoded = json_decode($reservation->training_data, true);
                                if (is_array($decoded)) {
                                    $existingTrainingData = $decoded;
                                }
                            }
                        }
                        // Vérifier si l'ID de formation existe déjà dans les données
                        if (!in_array((string)$formationId, $existingTrainingData)) {
                            $existingTrainingData[] = $trainingData;  // Ajouter seulement l'ID
                            $reservation->training_data = $existingTrainingData;
                            $reservation->save();

                            Log::info("ID de Formation {$formationId} ajouté à la réservation {$reservation->id}");
                        }
                    }
                }
            }
        }
        // Compter le nombre de formations dans le panier
        $trainingIds = $cart->training_ids ?: [];
        $cartCount = count($trainingIds);

        return response()->json([
            'success' => true,
            'message' => 'Formation ajoutée au panier avec succès',
            'cartCount' => $cartCount
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'ajout au panier: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
}
        public function getFormationInfo($id)
        {
            $formation = Training::find($id);

            if (!$formation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formation non trouvée'
                ], 404);
            }

            $userId = Auth::id() ?? session()->getId();
            $cart = Cart::where('user_id', $userId)->first();
            $panierFormationIds = [];

            if ($cart) {
                $panierFormationIds = $cart->training_ids ?: [];
            }

            $recommendations = Training::where('category_id', $formation->category_id)
                ->where('id', '!=', $formation->id)
                ->whereNotIn('id', $panierFormationIds)
                ->take(3)
                ->get();

            return response()->json([
                'success' => true,
                'formation' => $formation,
                'recommendations' => $recommendations
            ]);
        }

public function supprimer(Request $request)
{
    Log::info('Received request to supprimer', ['formation_id' => $request->formation_id]);
    $userId = Auth::id() ?? session()->getId();
    $formationId = $request->formation_id;

    $cart = Cart::where('user_id', $userId)->first();

    if (!$cart) {
        return response()->json([
            'success' => false,
            'message' => 'Panier introuvable'
        ], 404);
    }

    $trainingIds = $cart->training_ids ?: [];

    // Vérifier si la formation est dans le panier
    $key = array_search($formationId, $trainingIds);
    if ($key === false) {
        return response()->json([
            'success' => false,
            'message' => 'Formation introuvable dans votre panier'
        ], 404);
    }

    // Supprimer la formation du panier
    array_splice($trainingIds, $key, 1);
    $cart->training_ids = array_values($trainingIds); // Réindexer le tableau
    $cart->save();

    // Synchroniser avec les réservations en attente (status=0)
    if (Auth::check()) {
        // Convertir l'ID de formation en entier et en chaîne pour les comparaisons
        $formationIdInt = (int)$formationId;
        $formationIdStr = (string)$formationId;

        Log::info("Suppression en cours pour formation: ID int={$formationIdInt}, ID string={$formationIdStr}");

        $pendingReservations = Reservation::where('user_id', $userId)
            ->where('status', 0) // Réservations en attente uniquement
            ->get();

        foreach ($pendingReservations as $reservation) {
            Log::info("Traitement de la réservation: " . $reservation->id, [
                'training_data_type' => gettype($reservation->training_data),
                'training_data' => $reservation->training_data
            ]);

            // Assurons-nous que training_data est bien un tableau
            $trainingData = $reservation->training_data;
            if (is_string($trainingData)) {
                $trainingData = json_decode($trainingData, true) ?: [];
            }

            if (empty($trainingData)) {
                continue; // Passer à la réservation suivante si pas de données
            }

            // Cas 1: Si training_data est un simple tableau d'IDs (comme [4,8,6])
            if (isset($trainingData[0]) && !is_array($trainingData[0])) {
                Log::info("Format détecté: Tableau simple d'IDs");
                $keyToRemove = array_search($formationIdInt, $trainingData);
                if ($keyToRemove === false) {
                    $keyToRemove = array_search($formationIdStr, $trainingData);
                }

                if ($keyToRemove !== false) {
                    array_splice($trainingData, $keyToRemove, 1);
                    $reservation->training_data = array_values($trainingData);
                    $reservation->save();
                    Log::info("Formation {$formationId} supprimée de la réservation {$reservation->id} (tableau simple)");
                }
            }
            // Cas 2: Si training_data est un tableau d'objets (comme [{id: 4, ...}, {id: 8, ...}])
            else {
                Log::info("Format détecté: Tableau d'objets");
                $updatedTrainingData = [];
                $removed = false;

                foreach ($trainingData as $item) {
                    // Récupérer l'ID de l'élément de formation
                    $itemId = null;
                    if (is_array($item) && isset($item['id'])) {
                        $itemId = $item['id'];
                    } elseif (is_object($item) && isset($item->id)) {
                        $itemId = $item->id;
                    }

                    // Comparer avec les deux formats (entier et chaîne)
                    if ($itemId !== $formationIdInt && $itemId !== $formationIdStr) {
                        $updatedTrainingData[] = $item;
                    } else {
                        $removed = true;
                        Log::info("Formation trouvée et supprimée du training_data: ID={$itemId}");
                    }
                }

                if ($removed) {
                    $reservation->training_data = $updatedTrainingData;
                    $reservation->save();
                    Log::info("Formation {$formationId} supprimée de la réservation {$reservation->id} (tableau d'objets)", [
                        'ancien_count' => count($trainingData),
                        'nouveau_count' => count($updatedTrainingData)
                    ]);
                }
            }
        }
    }

    // Ajoutez une log pour déboguer
    Log::debug("Formation supprimée du panier: ID={$formationId}, Panier après suppression:", [
        'training_ids' => $cart->training_ids,
        'count' => count($cart->training_ids)
    ]);

    // Recalculer les totaux
    $trainings = Training::whereIn('id', $trainingIds)->get();

    $totalPrice = 0;
    $totalWithoutDiscount = 0;
    $discountedItemsOriginalPrice = 0;
    $discountedItemsFinalPrice = 0;
    $hasDiscount = false;

    foreach ($trainings as $training) {
        if ($training && $training->price) {
            $originalPrice = $training->price;
            $totalWithoutDiscount += $originalPrice;

            if ($training->discount > 0) {
                $hasDiscount = true;
                $discountedPrice = $originalPrice * (1 - $training->discount / 100);

                $discountedItemsOriginalPrice += $originalPrice;
                $discountedItemsFinalPrice += $discountedPrice;
                $totalPrice += $discountedPrice;
            } else {
                $totalPrice += $originalPrice;
            }
        }
    }

    $globalDiscountPercentage = 0;
    if ($totalWithoutDiscount > 0 && $totalPrice < $totalWithoutDiscount) {
        $globalDiscountPercentage = round(100 - ($totalPrice / $totalWithoutDiscount * 100));
    }

    $discountPercentage = 0;
    if ($discountedItemsOriginalPrice > 0 && $hasDiscount) {
        $discountPercentage = round(100 - ($discountedItemsFinalPrice / $discountedItemsOriginalPrice * 100));
    }

    $formattedTotalPrice = number_format($totalPrice, 3);
    $formattedTotalWithoutDiscount = number_format($totalWithoutDiscount, 3);
    $formattedDiscountedItemsOriginalPrice = number_format($discountedItemsOriginalPrice, 3);
    return response()->json([
        'success' => true,
        'message' => 'Formation supprimée du panier',
        'cartCount' => count($trainingIds),
        'totalPrice' => $formattedTotalPrice,
        'totalWithoutDiscount' => $formattedTotalWithoutDiscount,
        'discountedItemsOriginalPrice' => $formattedDiscountedItemsOriginalPrice,
        'discountPercentage' => $globalDiscountPercentage,
        'individualDiscountPercentage' => $discountPercentage,
        'hasDiscount' => $hasDiscount
    ]);
}        public function getCartItems()
        {
            $userId = Auth::id() ?? session()->getId();
            $cart = Cart::where('user_id', $userId)->first();

            $items = [];
            if ($cart && is_array($cart->training_ids)) {
                $items = $cart->training_ids;
            }

            return response()->json(['items' => $items]);
        }

        public function checkInCart($formationId)
        {
            $userId = Auth::id() ?? session()->getId();
            $cart = Cart::where('user_id', $userId)->first();

            $inCart = false;
            if ($cart && is_array($cart->training_ids)) {
                // Log for debugging
                Log::debug("Checking if formation {$formationId} is in cart: ", [
                    'training_ids' => $cart->training_ids,
                    'formationId' => $formationId
                ]);

                $inCart = in_array($formationId, $cart->training_ids);
            }

            return response()->json(['in_cart' => $inCart, 'cart_items' => $cart ? $cart->training_ids : []]);
        }


// public function checkAvailability()
// {
//     try {
//         Log::info('Début de la vérification de disponibilité du panier');

//         // Vérifier si l'utilisateur est connecté
//         if (!Auth::check()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Utilisateur non authentifié'
//             ], 401);
//         }

//         $user = Auth::user();
//         Log::info('Utilisateur connecté: ' . $user->id);

//         // Récupérer le panier de l'utilisateur
//         $cart = Cart::where('user_id', $user->id)->first();

//         // Récupérer les IDs de formation à vérifier
//         $trainingIds = [];
//         if ($cart && !empty($cart->training_ids)) {
//             // Si training_ids est un JSON, le décoder
//             if (is_string($cart->training_ids)) {
//                 $trainingIds = json_decode($cart->training_ids, true) ?? [];
//             }
//             // Si training_ids est déjà un tableau
//             else if (is_array($cart->training_ids)) {
//                 $trainingIds = $cart->training_ids;
//             }
//         }

//         Log::info('IDs de formations trouvés dans le panier: ' . json_encode($trainingIds));

//         // Si le panier est vide, vérifier si l'utilisateur a une réservation en attente
//         if (empty($trainingIds)) {
//             $pendingReservation = Reservation::where('user_id', $user->id)
//                                         ->where('status', 0)
//                                         ->latest()
//                                         ->first();

//             if ($pendingReservation && !empty($pendingReservation->training_data)) {
//                 $trainingData = is_string($pendingReservation->training_data)
//                     ? json_decode($pendingReservation->training_data, true)
//                     : $pendingReservation->training_data;

//                 if (is_array($trainingData)) {
//                     foreach ($trainingData as $item) {
//                         if (isset($item['id'])) {
//                             $trainingIds[] = $item['id'];
//                         } elseif (is_numeric($item)) {
//                             $trainingIds[] = $item;
//                         }
//                     }
//                 }

//                 Log::info('IDs de formations trouvés dans la réservation en attente: ' . json_encode($trainingIds));
//             }
//         }

//         if (empty($trainingIds)) {
//             Log::info('Aucune formation à vérifier');
//             return response()->json([
//                 'success' => true,
//                 'formations' => [],
//                 'message' => 'Aucune formation à vérifier'
//             ]);
//         }

//         $formattedTrainings = [];
//         $removedItems = [];

//         foreach ($trainingIds as $trainingId) {
//             // Récupérer la formation
//             $training = Training::find($trainingId);
//             if (!$training) {
//                 Log::warning('Formation non trouvée: ' . $trainingId);
//                 $removedItems[] = $trainingId;
//                 continue;
//             }

//             // Compter toutes les réservations confirmées qui contiennent cette formation
//             $confirmedReservationsCount = Reservation::where('status', 1)
//                 ->where(function($query) use ($trainingId) {
//                     $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
//                           ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
//                           ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
//                           ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%');
//                 })
//                 ->count();

//             // Vérifier si l'utilisateur a des réservations en attente pour cette formation
//             $pendingReservation = Reservation::where('user_id', $user->id)
//                 ->where('status', 0)
//                 ->where(function($query) use ($trainingId) {
//                     $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
//                           ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
//                           ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
//                           ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%');
//                 })
//                 ->exists();

//             // S'assurer que total_seats est traité comme un entier
//             $totalSeats = (int)($training->total_seats ?? 0);

//             // Calculer les places restantes et déterminer si la formation est complète
//             $remainingSeats = max(0, $totalSeats - $confirmedReservationsCount);
//             $isFull = ($remainingSeats <= 0);

//             // Ajouter des logs pour le débogage
//             Log::info("Formation ID: $trainingId, Titre: {$training->title}, Sièges totaux: {$totalSeats}, Réservations confirmées: $confirmedReservationsCount, Places restantes: $remainingSeats, Complète: " . ($isFull ? 'Oui' : 'Non'));

//             $formattedTrainings[] = [
//                 'id' => $training->id,
//                 'title' => $training->title,
//                 'total_seats' => $totalSeats,
//                 'available_seats' => $remainingSeats,
//                 'is_full' => $isFull,
//                 'has_pending_reservation' => $pendingReservation
//             ];
//         }

//         Log::info("Résultats formatés: " . json_encode($formattedTrainings));
//         $cartCount = count($trainingIds) - count($removedItems);

//         return response()->json([
//             'success' => true,
//             'formations' => $formattedTrainings,
//             'removed_items' => $removedItems,
//             'cartCount' => $cartCount
//         ]);
//     } catch (\Exception $e) {
//         // Log complet pour le débogage
//         Log::error('Erreur lors de la vérification de la disponibilité: ' . $e->getMessage());
//         Log::error('Stack trace: ' . $e->getTraceAsString());

//         return response()->json([
//             'success' => false,
//             'message' => 'Une erreur est survenue lors de la vérification de la disponibilité.',
//             'error' => config('app.debug') ? $e->getMessage() : null,
//             'trace' => config('app.debug') ? $e->getTraceAsString() : null
//         ], 500);
//     }
// }
public function checkAvailability()
{
    try {
        Log::info('Début de la vérification de disponibilité du panier');

        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $user = Auth::user();
        Log::info('Utilisateur connecté: ' . $user->id);

        // Récupérer le panier de l'utilisateur
        $cart = Cart::where('user_id', $user->id)->first();

        // Récupérer les IDs de formation à vérifier
        $trainingIds = [];
        if ($cart && !empty($cart->training_ids)) {
            // Si training_ids est un JSON, le décoder
            if (is_string($cart->training_ids)) {
                $trainingIds = json_decode($cart->training_ids, true) ?? [];
            }
            // Si training_ids est déjà un tableau
            else if (is_array($cart->training_ids)) {
                $trainingIds = $cart->training_ids;
            }
        }

        Log::info('IDs de formations trouvés dans le panier: ' . json_encode($trainingIds));

        // Si le panier est vide, vérifier si l'utilisateur a une réservation en attente
        if (empty($trainingIds)) {
            $pendingReservation = Reservation::where('user_id', $user->id)
                                        ->where('status', 0)
                                        ->latest()
                                        ->first();

            if ($pendingReservation && !empty($pendingReservation->training_data)) {
                $trainingData = is_string($pendingReservation->training_data)
                    ? json_decode($pendingReservation->training_data, true)
                    : $pendingReservation->training_data;

                if (is_array($trainingData)) {
                    foreach ($trainingData as $item) {
                        if (isset($item['id'])) {
                            $trainingIds[] = $item['id'];
                        } elseif (is_numeric($item)) {
                            $trainingIds[] = $item;
                        }
                    }
                }

                Log::info('IDs de formations trouvés dans la réservation en attente: ' . json_encode($trainingIds));
            }
        }

        if (empty($trainingIds)) {
            Log::info('Aucune formation à vérifier');
            return response()->json([
                'success' => true,
                'formations' => [],
                'message' => 'Aucune formation à vérifier',
                'cartCount' => 0
            ]);
        }

        $formattedTrainings = [];
        $removedItems = [];

        foreach ($trainingIds as $trainingId) {
            // Récupérer la formation
            $training = Training::find($trainingId);
            if (!$training) {
                Log::warning('Formation non trouvée: ' . $trainingId);
                $removedItems[] = $trainingId;
                continue;
            }

            // Compter toutes les réservations confirmées qui contiennent cette formation
            // Amélioration de la requête pour être plus précise avec des patterns JSON
            $confirmedReservationsCount = Reservation::where('status', 1)
                ->where(function($query) use ($trainingId) {
                    // Amélioration des recherches JSON pour différents formats
                    $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
                          ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%')
                          ->orWhere('training_data', 'like', '%"id":' . $trainingId . ',%')
                          ->orWhere('training_data', 'like', '%"id":"' . $trainingId . ',%');
                })
                ->count();

            // Vérifier si l'utilisateur a des réservations en attente pour cette formation
            $pendingReservation = Reservation::where('user_id', '!=', $user->id) // Exclure l'utilisateur actuel
                ->where('status', 0)
                ->where(function($query) use ($trainingId) {
                    $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
                          ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%')
                          ->orWhere('training_data', 'like', '%"id":' . $trainingId . ',%')
                          ->orWhere('training_data', 'like', '%"id":"' . $trainingId . ',%');
                })
                ->count();

            // S'assurer que total_seats est traité comme un entier
            $totalSeats = (int)($training->total_seats ?? 0);

            // Calculer les places restantes et déterminer si la formation est complète
            $remainingSeats = max(0, $totalSeats - $confirmedReservationsCount - $pendingReservation);
            $isFull = ($remainingSeats <= 0);

            // Ajouter des logs détaillés pour le débogage
            Log::info("Formation ID: $trainingId, Titre: {$training->title}, Sièges totaux: {$totalSeats}, " .
                      "Réservations confirmées: $confirmedReservationsCount, " .
                      "Réservations en attente: $pendingReservation, " .
                      "Places restantes: $remainingSeats, " .
                      "Complète: " . ($isFull ? 'Oui' : 'Non'));

            $formattedTrainings[] = [
                'id' => $training->id,
                'title' => $training->title,
                'total_seats' => $totalSeats,
                'available_seats' => $remainingSeats,
                'is_full' => $isFull,
                'has_pending_reservation' => ($pendingReservation > 0)
            ];
        }

        Log::info("Résultats formatés: " . json_encode($formattedTrainings));
        $cartCount = count($trainingIds) - count($removedItems);

        return response()->json([
            'success' => true,
            'formations' => $formattedTrainings,
            'removed_items' => $removedItems,
            'cartCount' => $cartCount
        ]);
    } catch (\Exception $e) {
        // Log complet pour le débogage
        Log::error('Erreur lors de la vérification de la disponibilité: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la vérification de la disponibilité.',
            'error' => config('app.debug') ? $e->getMessage() : null,
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}


 public function getCartDetails(Request $request)
    {
        $userId = Auth::id() ?? session()->getId();
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart || empty($cart->training_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Panier vide',
                'trainings' => [],
                'totalPrice' => 0
            ]);
        }
        // Récupérer toutes les formations dans le panier
        $trainings = Training::whereIn('id', $cart->training_ids)->get();
        // Calculer le prix total
        $totalPrice = $trainings->sum(function($training) {
            return $training->final_price ? $training->final_price : $training->price;
        });
        // Formater les données pour le front-end
        $formattedTrainings = $trainings->map(function($training) {
            return [
                'id' => $training->id,
                'title' => $training->title,
                'start_date' => $training->start_date,
                'end_date' => $training->end_date,
                'price' => (float) $training->price,
                'discount' => (float) $training->discount,
                'final_price' => (float) ($training->final_price ? $training->final_price : $training->price),
                'duration' => $training->formatted_duration
            ];
        });

        return response()->json([
            'success' => true,
            'trainings' => $formattedTrainings,
            'totalPrice' => $totalPrice
        ]);
    }
/**Vérifie si des articles du panier existent toujours dans la base de données*/
public function verifyCartItemsExistence(Request $request){
    $userId = Auth::id() ?? session()->getId();
    $cart = Cart::where('user_id', $userId)->first();
    if (!$cart || empty($cart->training_ids)) {
        return response()->json([
            'success' => true,
            'cartCount' => 0,
            'removed_items' => []
        ]);
    }
    $cartItems = $cart->training_ids;
    $existingTrainings = Training::whereIn('id', $cartItems)->pluck('id')->toArray();
    // Convertir en chaînes pour une comparaison cohérente
    $existingTrainings = array_map('strval', $existingTrainings);
    $cartItems = array_map('strval', $cartItems);
    // Trouver les articles qui n'existent plus
    $removedItems = array_diff($cartItems, $existingTrainings);
    // Si des articles ont été supprimés, mettre à jour le panier
    if (!empty($removedItems)) {
        $updatedItems = array_diff($cartItems, $removedItems);
        $cart->training_ids = array_values($updatedItems);
        $cart->save();
        // Recalculer les totaux
        $trainings = Training::whereIn('id', $updatedItems)->get();
        $totalPrice = $this->calculateCartTotals($trainings);
        return response()->json([
            'success' => true,
            'cartCount' => count($updatedItems),
            'removed_items' => array_values($removedItems),
            'totalPrice' => number_format($totalPrice['totalPrice'], 3),
            'discountedItemsOriginalPrice' => number_format($totalPrice['discountedItemsOriginalPrice'], 3),
            'discountPercentage' => $totalPrice['discountPercentage'],
            'hasDiscount' => $totalPrice['hasDiscount']
        ]);
    }
    return response()->json([
        'success' => true,
        'cartCount' => count($cartItems),
        'removed_items' => []
    ]);
}
private function calculateCartTotals($trainings)
{
    $totalPrice = 0;
    $totalWithoutDiscount = 0;
    $discountedItemsOriginalPrice = 0;
    $discountedItemsFinalPrice = 0;
    $hasDiscount = false;
    foreach ($trainings as $training) {
        if ($training && $training->price) {
            $originalPrice = $training->price;
            $totalWithoutDiscount += $originalPrice;

            if ($training->discount > 0) {
                $hasDiscount = true;
                $discountedPrice = $originalPrice * (1 - $training->discount / 100);

                $discountedItemsOriginalPrice += $originalPrice;
                $discountedItemsFinalPrice += $discountedPrice;
                $totalPrice += $discountedPrice;
            } else {
                $totalPrice += $originalPrice;
            }
        }
    }
    $globalDiscountPercentage = 0;
    if ($totalWithoutDiscount > 0 && $totalPrice < $totalWithoutDiscount) {
        $globalDiscountPercentage = round(100 - ($totalPrice / $totalWithoutDiscount * 100));
    }
    $discountPercentage = 0;
    if ($discountedItemsOriginalPrice > 0 && $hasDiscount) {
        $discountPercentage = round(100 - ($discountedItemsFinalPrice / $discountedItemsOriginalPrice * 100));
    }
    return [
        'totalPrice' => $totalPrice,
        'totalWithoutDiscount' => $totalWithoutDiscount,
        'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
        'discountPercentage' => $globalDiscountPercentage,
        'individualDiscountPercentage' => $discountPercentage,
        'hasDiscount' => $hasDiscount
    ];
}
/** Méthode pour obtenir le nombre d'articles dans le panier*/
public function getCount()
{
    $userId = Auth::id() ?? session()->getId();
    $cart = Cart::where('user_id', $userId)->first();
    $count = 0;
    if ($cart && is_array($cart->training_ids)) {
        $count = count($cart->training_ids);
    }
    return response()->json(['count' => $count]);
}


//zedthmm tw

/**
 * Enrichit les formations avec l'état du bouton selon les 6 cas
 */
public function getFormationsWithButtonStates()
{
    $userId = Auth::id() ?? session()->getId();
    $formations = Training::all(); // ou votre logique de récupération

    // Récupérer le panier de l'utilisateur
    $cart = Cart::where('user_id', $userId)->first();
    $cartTrainingIds = $cart ? ($cart->training_ids ?: []) : [];

    // Récupérer les réservations de l'utilisateur
    $userReservations = [];
    if (Auth::check()) {
        $reservations = Reservation::where('user_id', $userId)->get();
        foreach ($reservations as $reservation) {
            if ($reservation->training_data) {
                // Extraire les IDs de formation du JSON training_data
                if (is_array($reservation->training_data)) {
                    foreach ($reservation->training_data as $data) {
                        if (isset($data['id'])) {
                            $userReservations[$data['id']] = $reservation->status;
                        }
                    }
                }
            }
        }
    }

    // Enrichir chaque formation avec l'état du bouton
    $enrichedFormations = $formations->map(function($formation) use ($cartTrainingIds, $userReservations) {
        $formationId = $formation->id;

        // Calculer si la formation est complète
        $confirmedReservationsCount = Reservation::where('status', 1)
            ->where(function($query) use ($formationId) {
                $query->where('training_data', 'like', '%"id":"' . $formationId . '"%')
                      ->orWhere('training_data', 'like', '%"id":' . $formationId . '%')
                      ->orWhere('training_data', 'like', '%"id": ' . $formationId . '%')
                      ->orWhere('training_data', 'like', '%"id": "' . $formationId . '"%');
            })
            ->count();

        $isFull = ($formation->total_seats <= $confirmedReservationsCount);

        // Vérifier si la date est passée
        $isDatePassed = Carbon::parse($formation->start_date)->isPast();

        // Déterminer l'état du bouton selon les 6 cas
        $buttonState = $this->determineButtonState(
            $formationId,
            $cartTrainingIds,
            $userReservations,
            $isFull,
            $isDatePassed
        );

        // Ajouter les informations d'état à la formation
        $formation->button_state = $buttonState;
        $formation->is_full = $isFull;
        $formation->is_date_passed = $isDatePassed;
        $formation->available_seats = max(0, $formation->total_seats - $confirmedReservationsCount);

        return $formation;
    });

    return response()->json([
        'success' => true,
        'formations' => $enrichedFormations
    ]);
}

/**
 * Détermine l'état du bouton selon les 6 cas définis
 */
private function determineButtonState($formationId, $cartTrainingIds, $userReservations, $isFull, $isDatePassed)
{
    // Cas 5 & 6 : Formation complète ou date passée (priorité)
    if ($isFull) {
        return [
            'type' => 'disabled',
            'text' => 'Ajouter au panier',
            'disabled' => true,
            'reason' => 'no_places',
            'class' => 'btn-disabled'
        ];
    }

    if ($isDatePassed) {
        return [
            'type' => 'disabled',
            'text' => 'Ajouter au panier',
            'disabled' => true,
            'reason' => 'date_passed',
            'class' => 'btn-disabled'
        ];
    }

    // Cas 3 : Formation dans réservation avec status=1
    if (isset($userReservations[$formationId]) && $userReservations[$formationId] == 1) {
        return [
            'type' => 'consulter',
            'text' => 'Consulter contenu',
            'disabled' => false,
            'reason' => null,
            'class' => 'btn-success'
        ];
    }

    // Cas 1 & 2 : Formation dans le panier
    if (in_array($formationId, $cartTrainingIds)) {
        return [
            'type' => 'panier',
            'text' => 'Accéder au panier',
            'disabled' => false,
            'reason' => null,
            'class' => 'btn-warning'
        ];
    }

    // Cas 4 : Formation ni dans panier ni dans réservation
    return [
        'type' => 'ajouter',
        'text' => 'Ajouter au panier',
        'disabled' => false,
        'reason' => null,
        'class' => 'btn-primary'
    ];
}
 private function getButtonState($trainingId, $userId)
    {
        $training = Training::find($trainingId);
        if (!$training) {
            return [
                'text' => 'Ajouter au panier',
                'disabled' => true,
                'action' => '#',
                'class' => 'btn btn-secondary'
            ];
        }

        // Vérifier si la formation est dans le panier
        $cart = Cart::where('user_id', $userId)->first();
        $inCart = $cart && is_array($cart->training_ids) && in_array($trainingId, $cart->training_ids);

        // Cas 1 & 2: Formation dans le panier (avec ou sans réservation)
        if ($inCart) {
            return [
                'text' => 'Accéder au panier',
                'disabled' => false,
                'action' => route('panier.index'),
                'class' => 'btn btn-primary'
            ];
        }

        // Vérifier les réservations
        $hasConfirmedReservation = false;
        $hasPendingReservation = false;
        if (Auth::check()) {
            $hasConfirmedReservation = Reservation::where('user_id', $userId)
                ->where('status', 1)
                ->where(function ($query) use ($trainingId) {
                    $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
                          ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%');
                })
                ->exists();

            $hasPendingReservation = Reservation::where('user_id', $userId)
                ->where('status', 0)
                ->where(function ($query) use ($trainingId) {
                    $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
                          ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
                          ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%');
                })
                ->exists();
        }

        // Cas 3: Formation réservée avec status=1
        if ($hasConfirmedReservation) {
            return [
                'text' => 'Consulter contenu',
                'disabled' => false,
                'action' => route('formation.contenu', ['id' => $trainingId]),
                'class' => 'btn btn-info'
            ];
        }

        // Vérifier la disponibilité des places
        $confirmedReservationsCount = Reservation::where('status', 1)
            ->where(function ($query) use ($trainingId) {
                $query->where('training_data', 'like', '%"id":"' . $trainingId . '"%')
                      ->orWhere('training_data', 'like', '%"id":' . $trainingId . '%')
                      ->orWhere('training_data', 'like', '%"id": ' . $trainingId . '%')
                      ->orWhere('training_data', 'like', '%"id": "' . $trainingId . '"%');
            })
            ->count();
        $remainingSeats = max(0, $training->total_seats - $confirmedReservationsCount);
        $isFull = $remainingSeats <= 0;

        // Cas 5: Plus de places disponibles
        if ($isFull && !$hasPendingReservation && !$hasConfirmedReservation) {
            return [
                'text' => 'Ajouter au panier',
                'disabled' => true,
                'action' => '#',
                'class' => 'btn btn-secondary'
            ];
        }

        // Cas 6: Date dépassée
        if (Carbon::now()->gt($training->end_date)) {
            return [
                'text' => 'Ajouter au panier',
                'disabled' => true,
                'action' => '#',
                'class' => 'btn btn-secondary'
            ];
        }

        // Cas 4: Formation ni dans le panier ni réservée
        return [
            'text' => 'Ajouter au panier',
            'disabled' => false,
            'action' => route('panier.ajouter', ['training_id' => $trainingId]),
            'class' => 'btn btn-success'
        ];
    }

    /**
     * Liste les formations avec leurs états de bouton.
     */
    public function listTrainings()
    {
        $userId = Auth::id() ?? session()->getId();
        $trainings = Training::all();

        // Ajouter l'état du bouton pour chaque formation
        foreach ($trainings as $training) {
            $training->button = $this->getButtonState($training->id, $userId);
        }

        return view('admin.apps.formation.index', [
            'trainings' => $trainings
        ]);
    }


}
