<?php

namespace App\Controller;

use App\Services\AdminServices\ListAdminService;
use App\Services\UserServices\CreateUserService;
use App\Services\UserServices\ListUserService;
use App\Services\UserServices\ProviderInfoService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;


class AdminController extends AbstractController
{
    #[Route('/api/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('provider/index.html.twig', [
            'controller_name' => 'ProviderController',
        ]);
    }
    #[Route('/api/admin/list', name: 'list_admin')]
    public function listAdmin(ListAdminService $listAdminService,UserInterface $user): JsonResponse
    {
        return $listAdminService->listAdmin($user);
    }
    #[Route('/api/customer/list', name: 'list_user')]
    public function listUser(ListUserService $listUserService): JsonResponse
    {
        return $listUserService->listCustomers();
    }

    #[Route('/api/provider/list', name: 'list_provider')]
    public function listProvider(ListUserService $listUserService): JsonResponse
    {
        return $listUserService->listProvider();
    }

    #[Route('/api/provider/list_uuid', name: 'list_provider_uuid')]
    public function listProviderUuid(ListUserService $listUserService): JsonResponse
    {
        return $listUserService->listProviderUuid();
    }

    #[Route('/api/user/delete_all', name: 'delete_all_users')]
    public function deleteAllUsers(CreateUserService $createUserService): JsonResponse
    {
        return $createUserService->deleteAllUsers();
    }
   
}
