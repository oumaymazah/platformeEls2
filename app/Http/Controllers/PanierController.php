<?php
namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\Training;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Log;
    class PanierController extends Controller
    {
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
        public function index()
        {
            $userId = Auth::id() ?? session()->getId();
            $carts = Cart::where('user_id', $userId)->get();
            
            $panierItems = collect();
            $totalItemsCount = 0; // Variable pour compter les formations
            
            foreach ($carts as $cart) {
                $trainingIds = $cart->training_ids ?: [];
                $totalItemsCount += count($trainingIds); // Compter les formations
                
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
            
            foreach ($panierItems as $item) {
                if ($item->Training && $item->Training->price) {
                    $originalPrice = $item->Training->price;
                    
                    if ($item->Training->discount > 0) {
                        $hasDiscount = true;
                        $discountedPrice = $originalPrice * (1 - $item->Training->discount / 100);
                        
                        $discountedItemsOriginalPrice += $originalPrice;
                        $discountedItemsFinalPrice += $discountedPrice;
                        $totalPrice += $discountedPrice;
                    } else {
                        $totalPrice += $originalPrice;
                    }
                    
                    $totalWithoutDiscount += $originalPrice;
                }
                
                if ($item->Training) {
                    $item->Training->total_feedbacks = $item->Training && $item->Training->feedbacks ? $item->Training->feedbacks->count() : 0;
                    $item->Training->average_rating = $item->Training && $item->Training->total_feedbacks > 0
                        ? round($item->Training->feedbacks->sum('rating_count') / $item->Training->total_feedbacks, 1)
                        : 0;
                }
            }
            
            $discountPercentage = 0;
            if ($discountedItemsOriginalPrice > 0 && $hasDiscount) {
                $discountPercentage = round(100 - ($discountedItemsFinalPrice / $discountedItemsOriginalPrice * 100));
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
                    'cartCount' => $totalItemsCount
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
                'cartCount' => $totalItemsCount
            ]);
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
            
            array_splice($trainingIds, $key, 1);
            $cart->training_ids = array_values($trainingIds); // Réindexer le tableau
            $cart->save();

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
        }

        // public function getCartDetails(Request $request)
        // {
        //     $userId = Auth::id() ?? session()->getId();
        //     $cart = Cart::where('user_id', $userId)->first();

        //     if (!$cart || empty($cart->training_ids)) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Panier vide',
        //             'trainings' => [],
        //             'totalPrice' => 0
        //         ]);
        //     }
        //     // Récupérer toutes les formations dans le panier
        //     $trainings = Training::whereIn('id', $cart->training_ids)->get();
        //     // Calculer le prix total
        //     $totalPrice = $trainings->sum(function($training) {
        //         return $training->final_price ? $training->final_price : $training->price;
        //     });

        //     // Formater les données pour le front-end
        //     $formattedTrainings = $trainings->map(function($training) {
        //         return [
        //             'id' => $training->id,
        //             'title' => $training->title,
        //             'start_date' => $training->start_date,
        //             'end_date' => $training->end_date,
        //             'price' => (float) $training->price,
        //             'discount' => (float) $training->discount,
        //             'final_price' => (float) ($training->final_price ? $training->final_price : $training->price),
        //             'duration' => $training->formatted_duration
        //         ];
        //     });

        //     return response()->json([
        //         'success' => true,
        //         'trainings' => $formattedTrainings,
        //         'totalPrice' => $totalPrice
        //     ]);
        // }

        public function getCartItems()
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

 public function checkAvailability()
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
            
            if (empty($trainingIds)) {
                return response()->json([
                    'success' => true,
                    'formations' => [],
                    'message' => 'Aucune formation à vérifier'
                ]);
            }
            
            $formattedTrainings = [];
            
            foreach ($trainingIds as $trainingId) {
                // Récupérer la formation
                $training = Training::find($trainingId);
                
                if (!$training) continue;
                
                // CORRECTION: Utiliser une approche plus fiable pour compter les réservations
                // Compter les réservations confirmées pour cette formation
                $confirmedReservationsCount = Reservation::where('status', 1)
                    ->where(function($query) use ($trainingId) {
                        // Recherche dans le JSON pour les deux formats possibles d'ID
                        $query->whereRaw('JSON_CONTAINS(training_data, \'{"id":'.$trainingId.'}\')') 
                              ->orWhereRaw('JSON_CONTAINS(training_data, \'{"id":"'.$trainingId.'"}\')');
                    })
                    ->count();
                
                // Vérifier si l'utilisateur a des réservations en attente pour cette formation
                $pendingReservation = Reservation::where('user_id', $user->id)
                    ->where('status', 0)
                    ->where(function($query) use ($trainingId) {
                        $query->whereRaw('JSON_CONTAINS(training_data, \'{"id":'.$trainingId.'}\')') 
                              ->orWhereRaw('JSON_CONTAINS(training_data, \'{"id":"'.$trainingId.'"}\')');
                    })
                    ->exists();
                
                // Calculer les places restantes et déterminer si la formation est complète
                $remainingSeats = max(0, $training->total_seats - $confirmedReservationsCount);
                $isFull = ($remainingSeats === 0);
                
                // Ajouter des logs pour le débogage
                Log::info("Formation ID: $trainingId, Sièges totaux: {$training->total_seats}, Réservations confirmées: $confirmedReservationsCount, Places restantes: $remainingSeats, Complète: " . ($isFull ? 'Oui' : 'Non'));
                
                $formattedTrainings[] = [
                    'id' => $training->id,
                    'title' => $training->title,
                    'total_seats' => (int) $training->total_seats,
                    'available_seats' => (int) $remainingSeats,
                    'is_full' => $isFull,
                    'has_pending_reservation' => $pendingReservation
                ];
            }
            
            // Ajouter un log pour voir ce qui est envoyé au client
            Log::info("Formations formatées: " . json_encode($formattedTrainings));
            
            return response()->json([
                'success' => true,
                'formations' => $formattedTrainings
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
private function checkCartReservationStatus($trainingId, $userId)
{
    // Vérifier s'il y a une réservation en cours pour cette formation avec status 0
    $reservation = Reservation::where('user_id', $userId)
        ->where('training_id', $trainingId)
        ->where('status', 0)
        ->first();
    
    return $reservation ? 0 : null;
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

}