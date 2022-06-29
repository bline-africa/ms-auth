<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\DeleteRequests;
use App\Repository\AdminRepository;
use App\Services\AdminServices\ListAdminService;
use App\Services\UserServices\CreateUserService;
use App\Services\UserServices\ListUserService;
use App\Services\UserServices\ProviderInfoService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

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


    #[Route('/api/admin/info', name: 'admin_info_edit', methods: "PUT")]
    public function editAdminInfo(Request $request, UserInterface $userInt, SerializerInterface $serializer,
        AdminRepository $adminRepository, EntityManagerInterface $em): JsonResponse
    {
        $req = $request->getContent();
        $reqUser = $serializer->deserialize($req, Admin::class, 'json');
        try {
            $user = $adminRepository->findOneBy(['id' => $userInt->getSalt()]);
            if (!is_null($reqUser->getLastname())) $user->setLastname($reqUser->getLastname());
            if (!is_null($reqUser->getFirstname())) $user->setFirstname($reqUser->getFirstname());
            if (!is_null($reqUser->getEmail())) $user->setEmail($reqUser->getEmail());
            if (!is_null($reqUser->getAddress())) $user->setAddress($reqUser->getAddress());
            if (!is_null($reqUser->getIsValid())) $user->setIsValid($reqUser->getIsValid());
            if (!is_null($reqUser->getPhone1())) $user->setPhone1($reqUser->getPhone1());

            $em->persist($user);
            $em->flush();

            return $this->json($user, Response::HTTP_OK, [], ['groups' => 'Admin:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'error' => 400,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/user/delete_all', name: 'delete_all_users')]
    public function deleteAllUsers(CreateUserService $createUserService): JsonResponse
    {
        return $createUserService->deleteAllUsers();
    }

    #[Route('/api/user/delete_user', name: 'delete_user_single', methods: "POST")]
    public function deleteUser(Request $request,CreateUserService $createUserService,UserInterface $userInt): JsonResponse
    {
        $req = $request->getContent();
        $id = json_decode($req)->id;
        return $createUserService->deleteUser($id);
    }

    #[Route('/api/user/request_account_delete', name: 'request_account_delete', methods: "POST")]
    public function request_account_delete(Request $request,CreateUserService $createUserService): JsonResponse
    {
        $req = $request->getContent();
        $id = json_decode($req)->id;
        $user_name = json_decode($req)->name;
        //$date_request = json_decode($req)->date_request;
        $request = new DeleteRequests();
        $request->setUserName($user_name);
        $request->setUserId(Uuid::fromString($id));
        $request->setDateRequest(new DateTime());
        return $createUserService->deleteRequest($request);
    }

    #[Route('/api/user/confirm_delete_request', name: 'confirm_delete_request', methods: "POST")]
    public function confirmRequestDelete(Request $request,CreateUserService $createUserService): JsonResponse
    {
        $req = $request->getContent();
        $id = json_decode($req)->id;
        
        return $createUserService->confirmDeleteRequest($id);
    }

    #[Route('/api/user/list_delete_request', name: 'list_delete_request', methods: "GET")]
    public function listDeleteRequest(Request $request,CreateUserService $createUserService): JsonResponse
    {
        $req = $request->getContent();
        $id = json_decode($req)->id;
        $user_name = json_decode($req)->name;
        //$date_request = json_decode($req)->date_request;
        $request = new DeleteRequests();
        $request->setUserName($user_name);
        $request->setUserId(Uuid::fromString($id));
        $request->setDateRequest(new DateTime());
        return $createUserService->deleteRequest($request);
    }

   
}
