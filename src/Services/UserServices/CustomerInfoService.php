<?php
namespace App\Services\UserServices;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerInfoService{
    private $em;
    private $userRepository;
    private $profilRepository;
    private $validator;
    private $hasher;
    private $jwt;
    private $serializer;

    public function __construct(UserRepository $userRepository,JWTTokenManagerInterface $jwt,SerializerInterface $serializer) {
        $this->userRepository = $userRepository;
        $this->jwt = $jwt;
        $this->serializer = $serializer;
    }

    public function getCustomerInfo(UserInterface $user)
    {
        $json = $this->serializer->serialize(['customer' => $user], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'User:read']));

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}