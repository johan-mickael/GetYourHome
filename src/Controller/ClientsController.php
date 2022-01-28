<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\User;
use App\Form\ClientsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/clients', name: 'clients_')]
class ClientsController extends AbstractController
{
	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(ManagerRegistry $doctrine): Response
	{
		$clients = $doctrine->getRepository(Clients::class)->findAll();

		return $this->render('clients/index.html.twig', [
			'clients' => $clients,
		]);
	}

	#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
	{

		$client = new Clients();
		$form = $this->createForm(ClientsType::class, $client);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = new User;
			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$form->get('password')->getData()
				)
			);
			$user->setEmail($form->get('email')->getData());
			$client->setUser($user);
			$entityManager->persist($client);
			$entityManager->flush();

			return $this->redirectToRoute('clients_index');
		}

		return $this->render('clients/new.html.twig', [
			'client' => $client,
			'form' => $form->createView(),
		]);
	}

	#[Route('/{id}', name: 'show', methods: ['GET'])]
	public function show(Clients $client): Response
	{
		return $this->render('clients/show.html.twig', [
			'client' => $client,
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Clients $client, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
	{
		$form = $this->createForm(ClientsType::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = $client->getUser();
			$user->setEmail($form->get('email')->getData());
			$client->setUser($user);
			$entityManager->flush();

			return $this->redirectToRoute('clients_index');
		}

		return $this->render('clients/edit.html.twig', [
			'client' => $client,
			'form' => $form->createView(),
		]);
	}


	#[Route('/{id}', name: 'delete', methods: ['POST'])]
	public function delete(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
			$entityManager->remove($client);
			$entityManager->flush();
		}

		return $this->redirectToRoute('clients_index');
	}
}
