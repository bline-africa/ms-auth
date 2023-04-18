<?php
// src/EventSubscriber/ExceptionSubscriber.php
namespace App\EventSubscriber;

use App\Services\MailerController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct()
    {
    }
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['processException'],
            ],
            

        ];
    }

    public function customResponse(ResponseEvent $event)
    {
        echo json_encode("");
    }
    public function processException(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        //dd($request);
        $exception = $event->getThrowable();
        $message = sprintf(
            'My Error says: %s with code: %s on this request uri : %s method : %s',
            $exception->getMessage(),
            $exception->getCode(),
            $request->getUri(),
            $request->getMethod()
        );
        $message = $exception->getMessage();

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent(json_encode(["error" => $message]));
        $response->headers->set('Content-Type', 'application/json');

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            //$response->headers->replace($exception->getHeaders());
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        // sends the modified response object to the event

        $event->setResponse($response);
        //  echo json_encode($response);

    }

    public function logException(ExceptionEvent $event)
    {
        dd(array('ddf' => 2));
    }

    public function notifyException(ExceptionEvent $event)
    {
    }

    public function terminateProcess(TerminateEvent $event)
    {
        $response = $event->getResponse();
        // $this->mailer->sendEmail("Http " . $response->getStatusCode(), "ok");
    }
}
