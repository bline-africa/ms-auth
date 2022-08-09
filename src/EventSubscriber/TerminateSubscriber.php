<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Services\Messagerie\MailerController;
use App\Entity\Abonne;
use App\Entity\Demande;
use App\Entity\Devis;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use App\Repository\ProfilAdminRepository;
use App\Repository\TransactionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class TerminateSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $serializer;
    private  $twig;
    private $demandeRepository;
    private $userRepository;
    private $devisRepository;
    private $factureRepository;
    private $transactionRepository;
    private $profileRepository;
    private $logger;
    public function __construct(
        MailerController $mailer,
        SerializerInterface $serializer,
        Environment $twig,
        ProfilAdminRepository $profilAdminRepository,
        LoggerInterface $logger,

    ) {
        $this->mailer = $mailer;
        $this->serializer = $serializer;
        $this->twig = $twig;
        $this->profileRepository = $profilAdminRepository;
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::TERMINATE => [
                ['terminateProcess', 10]
            ],
            KernelEvents::RESPONSE => [
                ['customResponse', 0],
            ],
        ];
    }
    public function customResponse(ResponseEvent $event)
    {
        if ($event->getRequest()->getMethod() === 'OPTIONS') {
            $event->setResponse(
                new Response('', 204, [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Credentials' => 'true',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'DNT, X-User-Token, Keep-Alive, User-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type',
                    'Access-Control-Max-Age' => 1728000,
                    'Content-Type' => 'text/plain charset=UTF-8',
                    'Content-Length' => 0
                ])
            );
            return;
        }
        $response = $event->getResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($response->getContent());
        // dd($response);
        //$response->header->set("message","fd");

        $response->setStatusCode($response->getStatusCode());
        $event->setResponse($response);
    }

    public function terminateProcess(TerminateEvent $event)
    {
        $request = $event->getRequest();
        $content = $request->getContent();
        $response = $event->getResponse();
        $resContent = $response->getContent();
        $url = $request->getUri();
        $uri = $request->getRequestUri();
        $method = $request->getMethod();
        $error = "";
        $abonne = new User();


        $recipient = "antoine03kaboome@gmail.com";


        //mail à envoyer pour la validation du compe abonné
        if (($uri == "/api/create/user" && $method == "POST" && $response->getStatusCode() == Response::HTTP_CREATED) ||
            ($uri == "/api/user/send_code" && $method == "POST" && $response->getStatusCode() == Response::HTTP_OK)
        ) {
            try {
                $user = $this->serializer->deserialize($resContent, User::class, 'json');
                $recipient = $user->getEmail();
                $lang = json_decode($content)->lang;
               $this->logger->info("langResult : ".$lang);
                $profil = $this->profileRepository->findOneBy(['id' => json_decode($content)->profilId]);
                $template = 'account_validation.html.twig';
                $object = '';
                if (strtoupper($profil->getLibelle()) == "PROVIDER") {
                    if ($lang == 'en') {
                        $object = ' Provider account validation';
                        $template = 'account_validation_provider_en.html.twig';
                    } else {
                        $object = 'Validation de votre compte prestataire';
                        $template = 'account_validation_provider.html.twig';
                    }
                } else {
                    if ($lang == 'en') {
                        $object = 'User account validation';
                        $template = 'account_validation_en.html.twig';
                    } else {
                        $object = 'Validation de votre compte utilisateur';
                        $template = 'account_validation.html.twig';
                    }
                }

                $random = rand(10000, 99999);
                $content = $this->twig->render($template, [
                    'user' => $user,
                    "logo" => $request->getSchemeAndHttpHost() . "/images/logo_bline.png",
                    //'code_recu' => $random
                ]);
                $this->mailer->sendEmail($object, $content, $recipient);
                //  $abonne->getCodeR
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
            //$random = random_int(1, 10);
           
        }
        if ($uri == "/api/user/find_user" && $method == "POST" && $response->getStatusCode() == Response::HTTP_OK) {
            try {
                $user = $this->serializer->deserialize($resContent, User::class, 'json');
                $recipient = $user->getEmail();
                //  $abonne->getCodeR
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
            $content = $this->twig->render('password_code_check.html.twig', [
                'user' => $user,
                "logo" => $request->getSchemeAndHttpHost() . "/images/logo_bline.png",
                //'code_recu' => $random
            ]);
            $this->mailer->sendEmail("Password update", $content, $recipient);
        }
    }
}
