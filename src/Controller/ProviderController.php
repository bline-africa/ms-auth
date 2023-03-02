<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\UserServices\ProviderInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function getUserInfo(ProviderInfoService $providerInfoService,UserInterface $user,Request $request):JsonResponse
    {
        dd(apache_request_headers());
        return $providerInfoService->getProviderInfo($user);
    }

    #[Route('/api/user/delete', name: 'delete_user', methods: "POST")]
    public function deleteUser(Request $request, UserRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $req = json_decode($request->getContent());
        $user = $repository->findOneBy(['id' => $req->id]);
        if (!is_null($user)) {
            $em->remove($user);
            $em->flush();
            return $this->json('User with id: ' . $req->id . ' removed !', Response::HTTP_OK);
        } else {
            return $this->json('User is not found !', Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/user/update_info', name: 'update_info', methods: "POST")]
    public function updateInfo(Request $request, ProviderInfoService $providerInfoService, EntityManagerInterface $em): JsonResponse
    {
        $req = json_decode($request->getContent());
        $firstname = $req->firstname;
        $lastname = $req->lastname;
        $id = $req->id;
        return $providerInfoService->updateInfo($id,$lastname,$firstname);
    }

    
}
