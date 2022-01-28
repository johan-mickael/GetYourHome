<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
		$allDocuments = $options['data'][1];
		$documents = $options['data'][0]->getDocuments();
		// foreach ($allDocuments as $doc) {
		// 	$builder
        //     ->add($doc->getId(), FileType::class, [
		// 		'label' => $doc->getName(),
		// 		'mapped' => false,
		// 		'required' => false,
		// 	]);
		// }
		$builder
            ->add('test', FileType::class, [
				'label' => 'test',
				'mapped' => false,
				'required' => false,
			]);
    }
}
