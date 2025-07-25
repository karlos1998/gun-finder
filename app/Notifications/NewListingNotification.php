<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewListingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The listing instance.
     *
     * @var \App\Models\Listing
     */
    protected $listing;

    /**
     * Create a new notification instance.
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $gunModel = $this->listing->gunModel;
        $url = $this->listing->url;

        return (new MailMessage)
            ->subject("Nowe ogłoszenie dla {$gunModel->name}")
            ->greeting("Cześć {$notifiable->name}!")
            ->line("Znaleźliśmy nowe ogłoszenie dla modelu broni, który śledzisz: {$gunModel->name}")
            ->line("Tytuł: {$this->listing->title}")
            ->when($this->listing->price, function ($message) {
                return $message->line("Cena: {$this->listing->price}");
            })
            ->when($this->listing->description, function ($message) {
                return $message->line("Opis: {$this->listing->description}");
            })
            ->when($this->listing->city, function ($message) {
                $location = $this->listing->city;
                if ($this->listing->region) {
                    $location .= ", {$this->listing->region}";
                }
                return $message->line("Lokalizacja: {$location}");
            })
            ->when($this->listing->condition, function ($message) {
                return $message->line("Stan: {$this->listing->condition}");
            })
            ->action('Zobacz ogłoszenie', $url)
            ->line('Dziękujemy za korzystanie z naszej aplikacji!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'gun_model_id' => $this->listing->gun_model_id,
            'title' => $this->listing->title,
            'price' => $this->listing->price,
        ];
    }
}
