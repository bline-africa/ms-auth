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

class CreateHistoryService
{

    private $em;
    private $validator;

    public function __construct(
        EntityManagerInterface $em,
        HistoryRepository $historyRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher
    ) {
        $this->em = $em;
        $this->historyRepository = $historyRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
    }
    public function addHistory(User $user):History
    {
        $history = new History();
        $history->setUserId($user);
        $history->setAddressIp($user->getAddressIp());
        $history->setLatitude($user->getLatitude());
        $history->setLongitude($user->getLongitude());
        $history->setDateConnect($user->getLastConnect());
        //dd($history);
        $this->em->persist($history);
        $this->em->flush();

        return $history;
    }
}
