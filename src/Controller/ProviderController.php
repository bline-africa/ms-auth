<?php

namespace App\Controller;

use App\Services\UserServices\ProviderInfoService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;


class ProviderController extends AbstractController
{
    #[Route('/api/provider', name: 'provider')]
    public function index(): Response
    {
        return $this->render('provider/index.html.twig', [
            'controller_name' => 'ProviderController',
        ]);
    }

    #[Route('/api/provider/info', name: 'provider_info', methods: "GET")]
    public function getUserInfo(ProviderInfoService $providerInfoService,UserInterface $user):JsonResponse
    {
        return $providerInfoService->getProviderInfo($user);
    }

    
}
