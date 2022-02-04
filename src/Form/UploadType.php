<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class UploadType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		// $allDocuments est la variable qui désigne les documents que le client n'a pas encore transmis ou qui n'ont pas été validé
		$allDocuments = $options['data']['notSubmittedDocuments'];

		// Géneration des champs input file pour chaque document que le client doit transférer
		foreach ($allDocuments as $doc) {
			$builder
				->add($doc->getId(), FileType::class, [
					'label' => $doc->getName(),
					'mapped' => false,
					'required' => false,
					'constraints' => [
						new File([
							/**
							 * La taille maximale pour chaque document est de 4096Ko
							 * Types de document autorisé: pdf, jpeg, jpg, png
							 */
							'maxSize' => '4M',
							'mimeTypes' => [
								'application/pdf',
								"image/jpeg",
								"image/jpg",
								"image/png"
							],
							'mimeTypesMessage' => 'Veuillez choisir un document ou une image valide. (pdf, jpeg, jpg, png)',
						])
					],
				]);
		}
	}
}
