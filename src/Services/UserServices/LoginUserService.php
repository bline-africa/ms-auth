<?php
namespace App\Services\UserServices;

use App\Entity\History;
use App\Repository\UserRepository;
use App\Services\HistoryServices\CreateHistoryService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTimeImmutable;
use App\Entity\User;

class LoginUserService implements AuthenticationSuccessHandlerInterface{
    private $em;
    private $userRepository;
    private $profilRepository;
    private $validator;
    private $hasher;
    private $jwt;
    private $serializer;
    private CreateUserService $userService;
    private CreateHistoryService $historiqueService;

    public function __construct(UserRepository $userRepository,JWTTokenManagerInterface $jwt,SerializerInterface $serializer,CreateUserService $userService,
    CreateHistoryService $historiqueService) {
        $this->userRepository = $userRepository;
        $this->jwt = $jwt;
        $this->serializer = $serializer;
        $this->userService = $userService;
        $this->historiqueService = $historiqueService;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
       // dd($request);
        $content = $request->getContent();
        $idProfil = json_decode($content)->profilId;
        $user = $this->serializer->deserialize($content, User::class, 'json');
        $user->setLastConnect(new DateTimeImmutable());
       // $history = $this->historiqueService->addHistory($user);
       // dd($user);
       $history = new History();
        return $this->userService->loginUser($user,$idProfil,$this->historiqueService,"");
        // on success, let the request continue
       // return null;
    }

    
}