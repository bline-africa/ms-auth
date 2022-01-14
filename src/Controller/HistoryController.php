<?php

namespace App\Controller;

use App\Services\HistoryServices\ListHistoryService;
use App\Services\UserServices\ProviderInfoService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;


class HistoryController extends AbstractController
{
    #[Route('/api/lindex', name: 'lindex')]
    public function index(): Response
    {
        return $this->render('provider/index.html.twig', [
            'controller_name' => 'ProviderController',
        ]);
    }

    #[Route('/api/user/user_history', name: 'history', methods: "GET")]
    public function getUserInfo(ListHistoryService $historyService,UserInterface $user):JsonResponse
    {
       // dd("ok");
        return $historyService->listHistory();
    }

    #[Route('/api/user/history_by_user', name: 'history_user', methods: "POST")]
    public function getUserInfoByUser(Request $request,ListHistoryService $historyService):JsonResponse
    {
        $content = $request->getContent();
        $json = json_decode($content);
        //dd($json);
        return $historyService->listHistoryByUser($json->id);
    }

    
}
