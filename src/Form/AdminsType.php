<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Form;

use App\Entity\Administrateurs;
use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminsType extends AbstractType
{
	const ACTION = ['ajouter', 'modifier'];

	/**
	 * Formulaire de création ou de modification d'un client et son compte utilisateur en même temps
	 * On a la possibilité de créer et modifier le client et son compte dans une seule formulaire
	 * Le formulaire de la section utilisateur n'est pas mappé à l'entité Client donc on le rajoute manuellement
	*/
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		/**
		 * $options['data'] est un objet Client
		 * $user est l' entité utilisateur associé au client
		 */
		$user = $options['data']->getUser();

		// Si $user est nulle c'est qu'on va créer l'utilisateur sinon on va modifier l'utilisateur existant
		$action = $user ? self::ACTION[1] : self::ACTION[0];

		// Si $user existe, mettre l'email comme valeur par défaut sur le champ email
		$email = strcmp(self::ACTION[1], $action) == 0 ? $user->getEmail() : '';

		// Section de formulaire pour l'entité User
		$builder
			->add('email', EmailType::class, [
				'mapped' => false,
				'required' => true,
				'data' => $email
			]);
		$builder
			->add('password', PasswordType::class, [
				'mapped' => false,
			]);

		// Section de formulaire pour l'entité Client
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
			'data_class' => Administrateurs::class,
		]);
	}
}
