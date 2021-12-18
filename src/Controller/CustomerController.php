<?php

namespace App\Controller;

use App\Services\UserServices\CustomerInfoService;
use App\Services\UserServices\ProviderInfoService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;


class CustomerController extends AbstractController
{
    #[Route('/api/customer', name: 'customer')]
    public function index(): Response
    {
        return $this->render('provider/index.html.twig', [
            'controller_name' => 'ProviderController',
        ]);
    }

    #[Route('/api/customer/info', name: 'customer_info', methods: "GET")]
    public function getUserInfo(CustomerInfoService $customerInfoService,UserInterface $user):JsonResponse
    {
        return $customerInfoService->getCustomerInfo($user);
    }
}
