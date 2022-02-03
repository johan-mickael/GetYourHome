<?php

namespace App\Form;

use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;

class ClientsType extends AbstractType
{
	const ACTION = ['ajouter', 'modifier'];

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$authUser = $options["data"];
		$user = $options["data"]->getUser();
		$action = $user ? self::ACTION[1] : self::ACTION[0];
		$email = strcmp(self::ACTION[1], $action) == 0 ? $options["data"]->getUser()->getEmail() : '';
		$builder
			->add('email', EmailType::class, [
				'mapped' => false,
				'required' => true,
				'data' => $email
			]);
		if ($user->isEmployee()) {
			$builder
				->add('roles', TextType::class, [
					'mapped' => false,
					'required' => true,
					'data' => $email
				]);
		}
		if (strcmp(self::ACTION[0], $action) == 0)
			$builder
				->add('password', PasswordType::class, [
					'mapped' => false,
					'required' => true
				]);

		$builder
			->add('nom')
			->add('prenom')
			->add('dateNaissance', DateType::class, [
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd'
			])
			->add('adresse')
			->add('codePostale')
			->add('ville')
			->add('telephone');
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Clients::class,
		]);
	}
}
