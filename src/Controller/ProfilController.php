<?php

namespace App\Controller;

use App\Services\ProfilServices\ListProfilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    

    
    /**
     * @Route("/api/profil/list", name="list_profil", methods="GET")
     */
    public function getProfil(ListProfilService $service):JsonResponse
    {
       return  $service->listProfil(null,null);
    }
}
