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
use App\Services\HistoryServices\CreateHistoryService;
use App\Services\UserServices\FindUserService;
use App\Services\UserServices\UpdateUserService;
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
    public function loginUser(Request $request, SerializerInterface $serializer, CreateUserService $userService, CreateHistoryService $historiqueService)
    {
        //dd($request);
        $content = $request->getContent();
        $idProfil = json_decode($content)->profilId;
        $lang = json_decode($content)->lang??"en";
        $user = $serializer->deserialize($content, User::class, 'json');
        
        $user->setLastConnect(new DateTimeImmutable());
      //  $history = $historiqueService->addHistory($user,$idProfil);
        return $userService->loginUser($user, $idProfil, $historiqueService,$lang);
    }

    public function loginUserTest(Request $request, SerializerInterface $serializer, CreateUserService $userService, CreateHistoryService $historiqueService)
    {
        //dd($request);
        $content = $request->getContent();
        $idProfil = json_decode($content)->profilId;
        $lang = json_decode($content)->lang??"en";
        $user = $serializer->deserialize($content, User::class, 'json');
        
        $user->setLastConnect(new DateTimeImmutable());
      //  $history = $historiqueService->addHistory($user,$idProfil);
        return $userService->loginUserTest($user, $idProfil, $historiqueService,$lang);
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
     * @Route("api/create/user_by_admin", name="create_user_by_admin", methods="POST")
     */
    public function createUserByAdmin(
        Request $request,
        SerializerInterface $serializer,
        CreateUserService $userService
    ): JsonResponse {

        $content = $request->getContent();
        $user = $serializer->deserialize($content, User::class, 'json');

        $idProfil = json_decode($content)->profilId;
        // $profil = $profilAdminRepository->findOneBy(['id' => json_decode($content)->profilId]);
        return $userService->createUserByAdmin($user, $idProfil);
    }

    /**
     * @Route("api/create/openid", name="create_user_openid", methods="POST")
     */
    function loginOpenId(
        Request $request,
        SerializerInterface $serializer,
        CreateUserService $userService,
        CreateHistoryService $historiqueService
    ): JsonResponse {
        $content = $request->getContent();
        $parsed = json_decode($content);
        $accountId = $parsed->accountId;
        $lastname = $parsed->lastname;
        $firstname = $parsed->firstname;
        $email = $parsed->email;
        $profilId = $parsed->profilId;
        $phone1 = $parsed->phone1;
        $adress = $parsed->address;
        $accountType = $parsed->accountType;
        $user = new User();

        $user->setUsername($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setPhone1($phone1);
        $user->setAddress($adress);
        $user->setAccountType($accountType);
        $user->setAccountId($accountId);
        $user->setMustChangePassword(false);
        $user->setLastConnect(new DateTimeImmutable());

       // $history = $historiqueService->addHistory($user);
        return $userService->openId($user, $profilId,$historiqueService);
    }

    /**
     * @Route("api/user/validate", name="validate_user", methods="POST")
     */

    public function validateUser(Request $request, CreateUserService $userService)
    {
        $content = $request->getContent();
        $id = json_decode($content)->id;
        $code = json_decode($content)->code;
        //dd($id);
        return $userService->validateUser($id, $code);
    }

    /**
     * @Route("api/user/send_code", name="send_code", methods="POST")
     */

    public function sendCode(Request $request, CreateUserService $userService)
    {
        $content = $request->getContent();
        $id = json_decode($content)->id;

        return $userService->sendCode($id);
    }

    /**
     * @Route("api/user/find_user", name="find_user", methods="POST")
     */
    public function findUserByEmail(Request $request, FindUserService $findUserService): JsonResponse
    {
        $content = $request->getContent();
        $email = json_decode($content)->email;
        $profil = json_decode($content)->profilId;

        return $findUserService->findUser($email, $profil);
    }

    /**
     * @Route("api/user/find_user_code", name="find_user_code", methods="POST")
     */
    public function findUserByPasswordCode(Request $request, FindUserService $findUserService): JsonResponse
    {
        $content = $request->getContent();
        $code = json_decode($content)->code;

        return $findUserService->findUserByPasswordCode($code);
    }

    /**
     * @Route("api/user/update_user_password", name="update_user_password", methods="POST")
     */
    public function updateUserPassword(Request $request, UpdateUserService $updateUserService): JsonResponse
    {
        $content = $request->getContent();
        $id = json_decode($content)->id;
        $password = json_decode($content)->password;

        return $updateUserService->updateUserPassword($id,$password);
    }

    #[Route('/api/user/enable_all_account', name: 'enable_account', methods: "GET")]
    public function listDeleteRequest(Request $request,CreateUserService $createUserService): JsonResponse
    {
        return $createUserService->enableAllAccount();
    }

}
