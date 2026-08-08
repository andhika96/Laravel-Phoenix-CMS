<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PageBuilderElementorV23FormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $body,
        public bool $isHtml = true,
        public array $files = [],
    ) {}

    public function build(): static
    {
        $mail = $this->subject($this->subjectLine);

        if ($this->isHtml) {
            $mail->html($this->body);
        } else {
            $mail->text('emails.pagebuilder-elementor-v23-form-text', ['body' => $this->body]);
        }

        foreach ($this->files as $file) {
            $mail->attach($file['path'], [
                'as' => $file['name'],
                'mime' => $file['mime'],
            ]);
        }

        return $mail;
    }
}
