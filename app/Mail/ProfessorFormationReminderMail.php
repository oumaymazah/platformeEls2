<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfessorFormationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainings;
    public $professor;
    public $reminderMessage;
    public $startDate;

    /**
     * Create a new message instance.
     *
     * @param array $trainings
     * @param User $professor
     * @return void
     */
    public function __construct(array $trainings, User $professor)
    {
        $this->trainings = $trainings;
        $this->professor = $professor;
        $this->reminderMessage = "Nous vous rappelons respectueusement votre engagement pour ces formations. En cas d'empêchement ou de retard, nous vous prions de bien vouloir en informer le centre.";
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
                    ->view('emails.professor-formation-reminder')
                    ->with([
                        'trainings' => $this->trainings,
                        'professor' => $this->professor,
                        'reminderMessage' => $this->reminderMessage,
                        'startDate' => $this->startDate
                    ]);
    }
}