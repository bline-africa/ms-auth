<?php
namespace App\Services\UserServices;

use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ProviderInfoService{
    private $em;
    private $userRepository;
    private $profilRepository;
    private $validator;
    private $hasher;
    private $jwt;
    private $serializer;
    private $adminRepository;

    public function __construct(UserRepository $userRepository,JWTTokenManagerInterface $jwt,SerializerInterface $serializer,EntityManagerInterface $em,AdminRepository $adminRepository) {
        $this->userRepository = $userRepository;
        $this->jwt = $jwt;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->adminRepository = $adminRepository;
    }

    public function getProviderInfo(UserInterface $user)
    {
        $json = $this->serializer->serialize(['provider' => $user], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    public function updateInfo($id,$lastname,$firstname)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if(!$user){
            //return new JsonResponse(["message" => "User not found !"], Response::HTTP_NOT_FOUND);
        }
        $user = $this->adminRepository->findOneBy(['id' => $id]);
        if(!$user){
            return new JsonResponse(["message" => "User not found !"], Response::HTTP_NOT_FOUND);
        }
        $user->setLastname($lastname);
        $user->setFirstname($firstname);

        try {
            $this->em->persist($user);
            $this->em->flush();
            return new JsonResponse(["id" => $user->getId()], Response::HTTP_OK);
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
}