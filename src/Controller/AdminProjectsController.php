<?php 
// src/Controller/AdminController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProjectsController extends AbstractController
{
    #[Route('/project', name:'route-project')]
    public function index(): Response
    {
 		return $this->render('admin/project/index.html.twig');
    }
}


 ?>