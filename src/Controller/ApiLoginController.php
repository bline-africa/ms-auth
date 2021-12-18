<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\ProfilAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\ProfilAdminRepository;
use App\Repository\UserRepository;
use App\Services\ProfilServices\CreateProfilService;
use App\Services\UserServices\CreateUserService;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiLoginController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login",methods="POST")
     */
    public function index(?User $user, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
            'user' => $user,
            'token' => $JWTTokenManager->create($user)
        ], Response::HTTP_OK, [], ['groups' => 'Abonne:read']);
    }

    /**
     * @Route("/firstadmin",name="create_first_admin" ,methods="POST")
     */
    public function creator(
        ProfilAdminRepository $profilAdminRepository,
        Request $request,
        SerializerInterface $serializer,
        CreateUserService $userService
    ): JsonResponse {
        $content = $request->getContent();
        $admin = $serializer->deserialize($content, Admin::class, 'json');
        $profil = $profilAdminRepository->findOneBy(['id' => json_decode($content)->profilId]);
        $admin->setProfilId($profil);
        $admin->setIsvalid(true);
        $admin->setRoles(["ROLE_CREATOR"]);
        //$admin->setAccountType("none");
        $admin->setCreatedAt(new DateTimeImmutable());
        return  $userService->createFirstAdmin($admin);
    }

    /**
     * @Route("/api/create/admin",name="create_admin" ,methods="POST")
     */
    public function admin(
        ProfilAdminRepository $profilAdminRepository,
        Request $request,
        SerializerInterface $serializer,
        CreateUserService $userService
    ): JsonResponse {
        $content = $request->getContent();
        $admin = $serializer->deserialize($content, Admin::class, 'json');
        $profil = $profilAdminRepository->findOneBy(['id' => json_decode($content)->profilId]);
        $admin->setProfilId($profil);
        $admin->setIsvalid(true);
        //$admin->setRoles(["ROLE_CREATOR"]);
        //$admin->setAccountType("none");
        $admin->setCreatedAt(new DateTimeImmutable());
        return  $userService->createAdmin($admin);
    }

    /**
     * @Route("/api/firstprofile",name="create_first_profil" ,methods="POST")
     */
    public function createFirstProfil(
        Request $request,
        SerializerInterface $serializer,
        CreateProfilService $profilService
    ): JsonResponse {
        // $profil->setLibelle("Admin13");
        // $profil->setDescription("pour l'administration");
        $content = $request->getContent();
        $profil = $serializer->deserialize($content, ProfilAdmin::class, 'json');
        $profil->setIsValid(true);
        $profil->setLibelle("Super Admin");
        $profil->setRoles(["ROLE_CREATOR"]);

        return $profilService->createFirstProfil($profil);
    }

    /**
     * @Route("/api/create/profil",name="create_profil" ,methods="POST")
     */
    public function createProfil(
        Request $request,
        SerializerInterface $serializer,
        CreateProfilService $profilService
    ): JsonResponse {
        // $profil->setLibelle("Admin13");
        // $profil->setDescription("pour l'administration");
        $content = $request->getContent();
        $profil = new ProfilAdmin();
        $role = json_decode($content)->role;
        $libelle = json_decode($content)->libelle;
        $profil->setIsValid(true);
        $profil->setLibelle($libelle);
        // $profil->setRoles(["ROLE_CREATOR"]);
        $profil->setRoles([$role]);

        return $profilService->createFirstProfil($profil);
    }

    /**
     * @Route("api/login_admin", name="login_admin", methods="POST")
     */
    public function loginAdmin(Request $request, SerializerInterface $serializer, CreateUserService $userService)
    {
        $content = $request->getContent();
        $admin = $serializer->deserialize($content, Admin::class, 'json');

        // return $userService->loginAdmin($admin);
    }

    /**
     * @Route("api/login_user", name="login_user", methods="POST")
     */
    public function loginUser(Request $request, SerializerInterface $serializer, CreateUserService $userService)
    {
        $content = $request->getContent();
        $admin = $serializer->deserialize($content, User::class, 'json');

        return $userService->loginUser($admin);
    }

    /**
     * @Route("api/admin_profil", name="admin_login", methods="GET")
     */
    public function adminInfo(UserInterface $admin, JWTTokenManagerInterface $jwt, SerializerInterface $serializer): JsonResponse
    {
        //dd($admin);
        $jsonContent = $serializer->serialize($admin, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return $this->json([
            'user' => $admin,
        ], Response::HTTP_OK, [], ['groups' => 'Admin:read']);
    }

    /**
     * @Route("api/create/user", name="create_user", methods="POST")
     */
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        CreateUserService $userService
    ): JsonResponse {

        $content = $request->getContent();
        $user = $serializer->deserialize($content, User::class, 'json');

        $idProfil = json_decode($content)->profilId;
        // $profil = $profilAdminRepository->findOneBy(['id' => json_decode($content)->profilId]);
        return $userService->createUser($user, $idProfil);
    }

    /**
     * @Route("api/user/validate", name="validate_user", methods="POST")
     */

    public function validateUser(Request $request, CreateUserService $userService)
    {
        $content = $request->getContent();
        $id = json_decode($content)->id;
        $code = json_decode($content)->code;

        return $userService->validateUser((int)$id, $code);
    }
}
