<?php

namespace App\Notifications;

use App\Models\GunModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Karlos3098\SimplyConnectLaravelNotifications\Services\SimplyConnectMessage;

class NewListingsGroupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The gun model instance.
     *
     * @var \App\Models\GunModel
     */
    protected $gunModel;

    /**
     * The listings collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $listings;

    /**
     * Create a new notification instance.
     */
    public function __construct(GunModel $gunModel, Collection $listings)
    {
        $this->gunModel = $gunModel;
        $this->listings = $listings->take(5); // Limit to 5 listings per notification
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'simply-connect'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Nowe ogłoszenia dla {$this->gunModel->name}")
            ->greeting("Cześć {$notifiable->name}!")
            ->line("Znaleźliśmy nowe ogłoszenia dla modelu broni, który śledzisz: {$this->gunModel->name}");

        // Add each listing to the message
        $this->listings->each(function ($listing, $index) use ($message) {
            $message->line("[Ogłoszenie " . ($index + 1) . ": " . $listing->title . "]({$listing->full_url})");

            if ($listing->price) {
                $message->line("Cena: {$listing->price}");
            }

        });

        // Add a link to view all listings for this gun model
        $allListingsUrl = url("/gun-models/{$this->gunModel->id}/listings");
        $message->line("Aby zobaczyć wszystkie ogłoszenia dla tego modelu, kliknij poniższy link:")
                ->action("Zobacz wszystkie ogłoszenia", $allListingsUrl)
                ->line("Dziękujemy za korzystanie z naszej aplikacji!");

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'gun_model_id' => $this->gunModel->id,
            'gun_model_name' => $this->gunModel->name,
            'listings_count' => $this->listings->count(),
            'listings' => $this->listings->map(function ($listing) {
                return [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'price' => $listing->price,
                ];
            })->toArray(),
        ];
    }

    public function toSimplyConnect(object $notifiable): SimplyConnectMessage
    {
        return (new SimplyConnectMessage)
            ->phoneNumber("+48884167733")
            ->line("Gun Finder - nowe ogłoszenie")
            ->line("Kategoria: {$this->gunModel->name}")
            ->line("Ogłoszenie: {$this->listings->first()->title}")
            ->line("Cena: {$this->listings->first()->price}")
            ->line("Link: {$this->listings->first()->full_url}");
    }
}
