<?php

namespace App\Services\UserServices;

use App\Entity\Admin;
use App\Entity\ProfilAdmin;
use App\Entity\User;
use App\Repository\AdminRepository;
use App\Repository\ProfilAdminRepository;
use App\Repository\UserRepository;
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

    public function __construct(
        EntityManagerInterface $em,
        AdminRepository $adminRepository,
        ProfilAdminRepository $profilRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwt,
        SerializerInterface $serializer,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->adminRepository = $adminRepository;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->jwt = $jwt;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
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

    public function loginUser(User $user)
    {
        // dd($user);
        if (null === $user) {
            return new JsonResponse([
                'message' => 'missings credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
        //dd($user);
        $user = $this->userRepository->findOneBy(["username" => $user->getUserIdentifier()]);
        if (!$user->getIsvalid()) {
            /* return new JsonResponse([
                'message' => 'You need valid your account first, account not activated yet !'
            ], Response::HTTP_UNAUTHORIZED);*/
        }


        $json = $this->serializer->serialize(["token" => $this->jwt->create($user)], 'json', array_merge([
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

    public function validateUser($id, $code)
    {
        $user = $this->userRepository->findOneBy(["id" => $id,"isvalid" => false]);
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
}
