<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\User;
use App\Form\ClientsType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

#[Route('/clients', name: 'clients_')]
class ClientsController extends AbstractController
{
	// Initialisation des attributs de classe
	public function __construct(Security $security)
	{
		$this->security = $security;
		// $this->user est l'utilisateur authentifié sur l'application
		$this->user = $this->security->getUser();
	}

	// Affiche la liste des clients
	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(ManagerRegistry $doctrine): Response
	{
		$clients = $doctrine->getRepository(Clients::class)->findAll();

		return $this->render('clients/index.html.twig', [
			'clients' => $clients,
		]);
	}

	// Formulaire pour ajouter un nouveau client
	#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
	{
		$client = new Clients();
		$form = $this->createForm(ClientsType::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Création d'un utilisateur relatif au client
			$client->setUserOnCreateClient(
				$form->get('email')->getData(),
				// Cryptage le mot de passe du client
				$userPasswordHasher->hashPassword(
					new User,
					$form->get('password')->getData()
				)
			);

			$entityManager->persist($client);
			$entityManager->flush();

			return $this->redirectToRoute('clients_index');
		}

		return $this->render('clients/new.html.twig', [
			'client' => $client,
			'form' => $form->createView(),
		]);
	}

	// Affichage d'un client
	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(Clients $client): Response
	{
		/**
		 * Si le client tente de changer l'id sur l'uri pour voir un autre client, on change l'ID par son propre ID
		 * On le redirige sur son profil après
		 * */
		if(!$this->user->isEmployee() && $this->user !== $client->getUser()) {
			return $this->redirectToRoute('clients_show', ['id' => $this->user->getClients()->getId()]);
		}
		
		return $this->render('clients/show.html.twig', [
			'client' => $client,
		]);
	}

	// Modification d'un client
	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Clients $client, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
	{
		$form = $this->createForm(ClientsType::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Modification des identifiants du client
			$client->setUserOnCreateClient(
				$form->get('email')->getData(),
				// Cryptage le mot de passe du client
				$userPasswordHasher->hashPassword(
					new User,
					$form->get('password')->getData()
				)
			);

			$entityManager->flush();

			return $this->redirectToRoute('clients_index');
		}

		return $this->render('clients/edit.html.twig', [
			'client' => $client,
			'form' => $form->createView(),
		]);
	}

	// Suppression d'un client
	#[Route('/{id}', name: 'delete', methods: ['POST', 'DELETE'])]
	public function delete(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
	{
		// Verification de la validité du jeton de l'utilisateur connecté
		if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
			try {
				$entityManager->remove($client);
				$entityManager->flush();
			} catch (ForeignKeyConstraintViolationException $ex) {
				/**
				 * Levée d'une exception
				 * Contrainte dans la basee de données sur les clés étrangères
				 * Un client qui possède des projets ne peut pas être supprimé
				 * */
				$this->addFlash(
					'message',
					'Impossible de supprimer le client car il possède un projet.'
				);
			}
		}

		// Redirection vers la liste des clients
		return $this->redirectToRoute('clients_index');
	}
}
