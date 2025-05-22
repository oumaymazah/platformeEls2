<?php



namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $totalPrice;

    public function __construct($reservation, $totalPrice)
    {
        $this->reservation = $reservation;
        $this->totalPrice = $totalPrice;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre réservation')
                    ->view('emails.reservation-confirmation')
                    ->with([
                        'reservation' => $this->reservation,
                        'totalPrice' => $this->totalPrice
                    ]);
    }
}