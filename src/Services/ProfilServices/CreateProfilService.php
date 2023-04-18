<?php

namespace App\Services\ProfilServices;

use App\Entity\Profil;
use App\Entity\ProfilAdmin;
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

class CreateProfilService
{

    private $em;
    private $profilRepository;
    private $validator;
    private $hasher;

    public function __construct(
        EntityManagerInterface $em,
        ProfilAdminRepository $profilRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher
    ) {
        $this->em = $em;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
    }
    public function createFirstProfil(ProfilAdmin $profil): JsonResponse
    {
        try {
            $verifAdmin = $this->profilRepository->findByLibelle($profil->getLibelle());
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (!is_null($verifAdmin) && count($verifAdmin) > 0) {
            return new JsonResponse(["message" => "Profil already exists"], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $errors = $this->validator->validate($profil);
        if (count($errors) > 0) {
            return new JsonResponse(["message" => "format invalid"], Response::HTTP_BAD_REQUEST);
        }
        try {
            $this->em->persist($profil);
            $this->em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(["message" => $ex->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return new JsonResponse(["message" => $profil], Response::HTTP_CREATED);
    }

    
}
