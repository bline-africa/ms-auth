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

class FindUserService
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
    }
    public function findUser(String $email, $profilId): JsonResponse
    {
        try {
            $verifProfil = $this->profilRepository->findOneBy(["id" => $profilId]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // dd($verifAdmin);
        // $verifAdmin = new stdClass();
        if (is_null($verifProfil)) {
            return new JsonResponse(["message" => "Profil not found"], Response::HTTP_NOT_FOUND);
        }

        try {
            $userVerif = $this->userRepository->findOneBy(["email" => $email, "profilId" => $verifProfil]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (is_null($userVerif)) {
            return new JsonResponse(["message" => "User not found"], Response::HTTP_NOT_FOUND);
        }
        $random = rand(10000, 99999);
        $userVerif->setPasswordCode($random);
        $this->em->persist($userVerif);
        $this->em->flush();

        $json = $this->serializer->serialize($userVerif, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    public function findUserByPasswordCode($code)
    {
        try {
            $userVerif = $this->userRepository->findOneBy(["passwordCode" => $code]);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (is_null($userVerif)) {
            return new JsonResponse(["message" => "User not found"], Response::HTTP_NOT_FOUND);
        }
        
        $json = $this->serializer->serialize($userVerif, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
