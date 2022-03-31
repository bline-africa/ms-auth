<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Services\AdminServices\ListAdminService;
use App\Services\UserServices\ListUserService;
use App\Services\UserServices\ProviderInfoService;
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
           $user =  $adminRepository->findOneBy(['id' => $userInt->getSalt()]);
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
   
}
