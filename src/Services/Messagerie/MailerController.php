<?php
// src/Controller/MailerController.php
namespace App\Services\Messagerie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
    private  $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendEmail(String $subject, String $text,$recipient)
    {
        $email = (new Email())
            ->from('info@bline.africa')
            ->to($recipient)
            //->cc('cc@example.com')
            ->bcc('antoine01kaboome@gmail.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($text)
            ->html($text);

        $this->mailer->send($email);
    }
}
