<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $trainings;
    public $startDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, array $trainings)
    {
        $this->user = $user;
        $this->trainings = $trainings;
        
        // On garde la date du premier cours comme référence 
        // (toutes les formations commencent le même jour puisqu'on filtre par date)
        $this->startDate = \Carbon\Carbon::parse($trainings[0]->start_date)->format('d/m/Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Rappel: Vos formations commencent dans 2 jours')
                    ->view('emails.formation-reminder');
    }
}