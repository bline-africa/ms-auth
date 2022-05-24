<?php

namespace App\Services\UserServices;

use App\Entity\Admin;
use App\Entity\History;
use App\Entity\ProfilAdmin;
use App\Entity\User;
use App\Repository\AdminRepository;
use App\Repository\HistoryRepository;
use App\Repository\ProfilAdminRepository;
use App\Repository\UserRepository;
use App\Services\HistoryServices\CreateHistoryService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use phpDocumentor\Reflection\DocBlock\Serializer;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Uid\Uuid;

class CreateUserService
{

    private $em;
    private $adminRepository;
    private $profilRepository;
    private $validator;
    private $hasher;
    private $jwt;
    private $serializer;
    private $userRepository;
    private $historyRepository;

    public function __construct(
        EntityManagerInterface $em,
        AdminRepository $adminRepository,
        ProfilAdminRepository $profilRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwt,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        HistoryRepository $historyRepository
    ) {
        $this->em = $em;
        $this->adminRepository = $adminRepository;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->jwt = $jwt;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->historyRepository = $historyRepository;
    }
    public function createFirstAdmin(Admin $admin): JsonResponse
    {
        try {
            $verifAdmin = $this->adminRepository->findByUsername("admin");
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // dd($verifAdmin);
        // $verifAdmin = new stdClass();
        if (!is_null($verifAdmin) && count($verifAdmin) > 0) {
            return new JsonResponse(["message" => "User admin already exists"], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $admin->setPassword($this->hasher->hashPassword($admin, $admin->getPassword()));
        $errors = $this->validator->validate($admin);
        if (count($errors) > 0) {
            return new JsonResponse(["message" => "format invalid"], Response::HTTP_BAD_REQUEST);
        }
        try {
            $this->em->persist($admin);
            $this->em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return new JsonResponse(["message" => $admin], Response::HTTP_CREATED);
    }

    public function createAdmin(Admin $admin): JsonResponse
    {
        try {
            $verifProfil = $this->profilRepository->findOneBy(["id" => $admin->getProfilId()]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // dd($verifAdmin);
        // $verifAdmin = new stdClass();
        if (is_null($verifProfil)) {
            return new JsonResponse(["message" => "profil not exists"], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $admin->setPassword($this->hasher->hashPassword($admin, $admin->getPassword()));

        $admin->setRoles($verifProfil->getRoles());
        // dd($admin);
        $errors = $this->validator->validate($admin);
        if (count($errors) > 0) {
            return new JsonResponse(["message" => "format invalid"], Response::HTTP_BAD_REQUEST);
        }
        try {
            $this->em->persist($admin);
            $this->em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        $json = $this->serializer->serialize($admin, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'Admin:read']));
        //dd($json);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    public function loginAdmin(Admin $admin)
    {
        if (null === $admin) {
            return new JsonResponse([
                'message' => 'missings credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $json = $this->serializer->serialize($admin, 'json', [], ['groups' => 'Admin:read']);
        return new JsonResponse(
            $json,
            Response::HTTP_ACCEPTED
        );
    }

    public function loginUser(User $user, $idProfil, $historiqueService)
    {
        // dd($user);
        if (null === $user) {
            return new JsonResponse([
                'message' => 'missings credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $verifProfil = $this->profilRepository->findOneBy(["id" => $idProfil]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if ($verifProfil == null) {
            return new JsonResponse(["message" => "Profil not found"], Response::HTTP_NOT_FOUND);
        }
        $userVerif = $this->userRepository->findOneBy(["username" => $user->getUserIdentifier()]);
        $userMail = $this->userRepository->findOneBy(["email" => $user->getEmail()]);
       // dd($userVerif);
        if ($userMail) {
            $userVerif = $userMail;
        }
        if (!$userVerif->getIsvalid()) {
           /* return new JsonResponse([
                'message' => 'You need valid your account first, account not activated yet !'
            ], Response::HTTP_UNAUTHORIZED);*/
        }
        // dd($user);
        $userVerif->setLastConnect($user->getLastConnect());
        $userVerif->setLatitude($user->getLatitude());
        $userVerif->setLongitude($user->getLongitude());
        $userVerif->setAddressIp($user->getAddressIp());
        $userVerif->setProfilId($verifProfil);
        $history = $historiqueService->addHistory($userVerif);


        //dd($user);
        $this->em->persist($userVerif);
        $this->em->flush();
        // dd($userVerif);
        $json = $this->serializer->serialize(["token" => $this->jwt->create($userVerif), 'history' => $history], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        //dd($json);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    public function createUser(User $user, int $idProfil): JsonResponse
    {
        // $emailConstraint = new EmailValidator();
        try {
            $verifProfil = $this->profilRepository->findOneBy(["id" => $idProfil]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        //  dd($verifProfil);
        // $verifAdmin = new stdClass();
        if (is_null($verifProfil)) {
            return new JsonResponse(["message" => "profil not exists"], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $verifUser = $this->userRepository->findOneBy(['email' => $user->getEmail(), 'profilId' => $verifProfil]);
        if ($verifUser) {
            $message = "User " . $user->getEmail() . " already exists";
            return new JsonResponse(["message" => $message], Response::HTTP_CONFLICT);
        }
        $verifUser = $this->userRepository->findOneBy(['username' => $user->getUserIdentifier(), 'profilId' => $verifProfil]);
        if ($verifUser) {
            $message = "User " . $user->getUserIdentifier() . " already exists";
            return new JsonResponse(["message" => $message], Response::HTTP_CONFLICT);
        }
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $random = rand(10000, 99999);
        $user->setRoles($verifProfil->getRoles());
        $user->setProfilId($verifProfil);
        $user->setCode($random);

        // dd($admin);
        $errors = $this->validator->validate($user);
        $user->setIsvalid(false);
        $user->setCreatedAt(new DateTimeImmutable());
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse(["message" => $messages], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_FORBIDDEN);
        }

        $json = $this->serializer->serialize($user, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        //dd($json);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    public function openId(User $user, int $idProfil,$historiqueService)
    {
        try {
            $verifProfil = $this->profilRepository->findOneBy(["id" => $idProfil]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (strtoupper($user->getAccountType()) != "FACEBOOK" && strtoupper($user->getAccountType())  != "GOOGLE" && strtoupper($user->getAccountType())  != "APPLE") {
            return new JsonResponse(["message" => "account type not found"], Response::HTTP_NOT_FOUND);
        }
        //  dd($verifProfil);
        // $verifAdmin = new stdClass();
        if (is_null($verifProfil)) {
            return new JsonResponse(["message" => "profil not exists"], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        // dd("");
        $verifUser = $this->userRepository->findOneBy(["accountId" => $user->getAccountId(), "account_type" => $user->getAccountType(), "profilId" => $verifProfil]);
        if ($verifUser == null) {
            $verifUser = $user;
        }

        $verifUser->setPassword($this->hasher->hashPassword($verifUser, $verifUser->getAccountId()));
        $random = rand(10000, 99999);
        $verifUser->setRoles($verifProfil->getRoles());
        $verifUser->setProfilId($verifProfil);
        $verifUser->setCode($random);
        // dd($admin);
        $errors = $this->validator->validate($verifUser);
        $verifUser->setIsvalid(true);
        $verifUser->setCreatedAt(new DateTimeImmutable());
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse(["message" => $messages], Response::HTTP_BAD_REQUEST);
        }
        $verifUser->setLastConnect($user->getLastConnect());
        $verifUser->setLatitude($user->getLatitude());
        $verifUser->setLongitude($user->getLongitude());
        $verifUser->setAddressIp("");
        $verifUser->setProfilId($verifProfil);
        //$history = $historiqueService->addHistory($verifUser);
        try {
            $this->em->persist($verifUser);
            $this->em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_FORBIDDEN);
        }

        $json = $this->serializer->serialize(["user" => $verifUser, "token" => $this->jwt->create($verifUser)], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        //dd($json);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    public function validateUser($id, $code)
    {
        //  dd($id);
        $user = $this->userRepository->findOneBy(["id" => $id, "isvalid" => false]);
        if ($user == null) {
            return new JsonResponse(["message" => "user not found or account already valid"], Response::HTTP_NOT_FOUND);
        }
        if ($user->getCode() != $code) {
            return new JsonResponse(["message" => "code not match"], Response::HTTP_FORBIDDEN);
        }
        $user->setIsvalid(true);

        $this->em->persist($user);
        $this->em->flush();
        $json = $this->serializer->serialize($user, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        //dd($json);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    public function sendCode($id)
    {
        $user = $this->userRepository->findOneBy(["id" => $id]);
        if ($user == null) {
            return new JsonResponse(["message" => "user not match"], Response::HTTP_NOT_FOUND);
        }
        $json = $this->serializer->serialize($user, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        //dd($json);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    public function deleteAllUsers()
    {
        $this->historyRepository->deleteAllHistories();
        $this->userRepository->deleteAllUsers();
        $this->em->flush();

        return new JsonResponse(["message" => "ok"], Response::HTTP_OK);
    }
    public function deleteUser($uuid)
    {
        $user = $this->userRepository->findOneBy(['id' => $uuid]);
        if($user){
            $histories = $this->historyRepository->findBy(['userId' => $user->getId()]);
            
            foreach ($histories as $historie ) {
                $this->em->remove($historie);
            }
            $this->em->remove($user);
        }
        
        $this->em->flush();

        return new JsonResponse(["message" => "Suppression ok"], Response::HTTP_OK);
    }
}
