<?php

namespace App\Controller;

use App\Entity\Administrateurs;
use App\Entity\User;
use App\Form\AdminsType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/administrateurs', name: 'administrateurs_')]
class AdministrateursController extends AbstractController
{
	// Initialisation des attributs de classe
	public function __construct(Security $security)
	{
		$this->security = $security;
		// $this->user est l'utilisateur authentifié sur l'application
		$this->user = $this->security->getUser();
	}

	#[Route('/', name: 'index')]
	public function index(ManagerRegistry $doctrine): Response
	{
		$admins = $doctrine->getRepository(Administrateurs::class)->findAll();
		return $this->render('administrateurs/index.html.twig', compact('admins'));
	}

	// Formulaire pour ajouter un nouveau employé
	#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
	{
		$admin = new Administrateurs();
		$form = $this->createForm(AdminsType::class, $admin);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Création d'un utilisateur relatif au employé
			$admin->setUserOnCreateAdmin(
				$form->get('email')->getData(),
				// Cryptage le mot de passe du employé
				$userPasswordHasher->hashPassword(
					new User,
					$form->get('password')->getData()
				)
			);

			$entityManager->persist($admin);
			$entityManager->flush();

			return $this->redirectToRoute('administrateurs_index');
		}

		return $this->render('administrateurs/new.html.twig', [
			'admin' => $admin,
			'form' => $form->createView(),
		]);
	}

	// Affichage d'un employé
	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(Administrateurs $admin): Response
	{
		return $this->render('administrateurs/show.html.twig', [
			'admin' => $admin,
		]);
	}

	// Modification d'un employé
	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Administrateurs $admin, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
	{
		$form = $this->createForm(AdminsType::class, $admin);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Modification des identifiants du employé
			$admin->setUserOnCreateAdmin(
				$form->get('email')->getData(),
				// Cryptage le mot de passe du employé
				$userPasswordHasher->hashPassword(
					new User,
					$form->get('password')->getData()
				)
			);

			$entityManager->flush();

			return $this->redirectToRoute('administrateurs_index');
		}

		return $this->render('administrateurs/edit.html.twig', [
			'admin' => $admin,
			'form' => $form->createView(),
		]);
	}

	// Suppression d'un employé
	#[Route('/{id}', name: 'delete', methods: ['POST', 'DELETE'])]
	public function delete(Request $request, Administrateurs $admin, EntityManagerInterface $entityManager): Response
	{
		// Verification de la validité du jeton de l'utilisateur connecté
		if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->request->get('_token'))) {
			$entityManager->remove($admin);
			$entityManager->flush();
		}

		// Redirection vers la liste des employés
		return $this->redirectToRoute('administrateurs_index');
	}
}
