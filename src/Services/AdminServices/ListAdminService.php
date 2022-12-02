<?php

namespace App\Services\AdminServices;

use App\Entity\Admin;
use App\Entity\Profil;
use App\Entity\ProfilAdmin;
use App\Entity\User;
use App\Repository\AdminRepository;
use App\Repository\ProfilAdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ListAdminService
{

    private $em;
    private $profilRepository;
    private $validator;
    private $hasher;
    private $serializer;
    private $adminRepository;
    private $user;

    public function __construct(
        EntityManagerInterface $em,
        ProfilAdminRepository $profilRepository,
        AdminRepository $adminRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->serializer = $serializer;
        $this->adminRepository = $adminRepository;
    }
    public function listAdmin(UserInterface $user): JsonResponse
    {
        $list = $this->adminRepository->findAll();
        $json = $this->serializer->serialize(["list" => $list], 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'Admin:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    public function disableEnable($id, $state)
    {
        $verif = $this->adminRepository->findOneBy(['id' => $id]);
        if (!$verif) {
            return new JsonResponse(["message" => "Admin not found"], Response::HTTP_NOT_FOUND);
        }
        try {
            $verif->setIsValid($state);
            $this->em->persist($verif);
            $this->em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return new JsonResponse(["message" => $verif], Response::HTTP_OK);
    }
}
