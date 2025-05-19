<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtendSessionForQuiz
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     // Vérifier si l'utilisateur est sur une page de quiz
    //     if ($request->route() &&
    //         (str_contains($request->route()->getName(), 'quizzes.attempt') ||
    //          str_contains($request->route()->getName(), 'quizzes.answer'))) {

    //         // Prolonger la durée de la session pour les quiz
    //         $minutes = 240; // 4 heures
    //         config(['session.lifetime' => $minutes]);

    //         // Régénérer l'ID de session pour appliquer immédiatement les modifications
    //         $request->session()->regenerate();

    //         // Mettre à jour le cookie de session avec la nouvelle durée
    //         $response = $next($request);
    //         $cookie = $response->headers->getCookies()[0] ?? null;
    //         if ($cookie) {
    //             $newCookie = cookie(
    //                 $cookie->getName(),
    //                 $cookie->getValue(),
    //                 $minutes
    //             );
    //             $response->headers->setCookie($newCookie);
    //         }

    //         return $response;
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next): Response
{
    // Vérifier si l'utilisateur est sur une page de quiz
    if ($request->route() &&
        (str_contains($request->route()->getName(), 'quizzes.attempt') ||
         str_contains($request->route()->getName(), 'quizzes.answer'))) {

        // Prolonger la durée de la session pour les quiz
        $minutes = 240; // 4 heures
        config(['session.lifetime' => $minutes]);

        // Sauvegarder le jeton CSRF actuel avant de régénérer la session
        $oldToken = $request->session()->token();

        // Régénérer l'ID de session mais conserver le jeton CSRF
        $request->session()->regenerate(true); // Le paramètre true préserve la session précédente

        // Si vous rencontrez encore des problèmes, essayez de restaurer manuellement le jeton CSRF
        // $request->session()->put('_token', $oldToken);

        // Le reste du code reste inchangé
        $response = $next($request);

        // Mettre à jour le cookie de session avec précaution
        if (method_exists($response->headers, 'getCookies')) {
            $cookies = $response->headers->getCookies();
            $cookie = !empty($cookies) ? $cookies[0] : null;

            if ($cookie) {
                $newCookie = cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $minutes
                );
                $response->headers->setCookie($newCookie);
            }
        }

        return $response;
    }

    return $next($request);
}
}
