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

class ListUserService
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
        
        ProfilAdminRepository $profilRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwt,
        SerializerInterface $serializer,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->jwt = $jwt;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
    }
   
    public function listCustomers(): JsonResponse
    {
        $list = $this->userRepository->findByType("ROLE_CUSTOMER");
        
       // dd($list);
       $data = array_filter($list,function($dt){
        //dd($dt["is_deleted"]);
        return $dt["is_deleted"] == false;
    });
        $json = $this->serializer->serialize(["list" => $data], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
        
    }
    public function listProvider(): JsonResponse
    {
        $list = $this->userRepository->findByType("ROLE_PROVIDER");
        
       // dd($list);
       $data = array_filter($list,function($dt){
           //dd($dt["is_deleted"]);
           return $dt["is_deleted"] == false;
       });
        $json = $this->serializer->serialize(["list" => $data], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
        
    }

    public function listProviderUuid(): JsonResponse
    {
        $list = $this->userRepository->findUuidByType("ROLE_PROVIDER");
        
       // dd($list);
       $data = array_filter($list,function($dt){
        //dd($dt["is_deleted"]);
        return $dt["is_deleted"] == false;
    });
        $json = $this->serializer->serialize(["list" => $data], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'UserUuid:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
        
    }
}
