<?php

namespace App\Services\HistoryServices;

use App\Entity\History;
use App\Entity\User;
use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ListHistoryService
{

    private $em;
    private $validator;
    private $serializer;

    public function __construct(
        EntityManagerInterface $em,
        HistoryRepository $historyRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->historyRepository = $historyRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->serializer = $serializer;
    }
    public function listHistory():JsonResponse
    {
        $histories = $this->historyRepository->findAll();
        $json = $this->serializer->serialize(["list" => $histories], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'History:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
       // return $history;
    }

    public function listHistoryByUser($id):JsonResponse
    {
        $histories = $this->historyRepository->findBy(['userId' => $id]);
        $json = $this->serializer->serialize(["list" => $histories], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'History1:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
       // return $history;
    }
}
