<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldType\CheckboxFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezboolean FieldType.
 */
class CheckboxFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition)
    {
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', CheckboxFieldType::class, [
                'required' => false,
                'label' => /** @Desc("Default value") */'field_definition.ezboolean.default_value',
            ])
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm->add($defaultValueForm);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        CheckboxFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $label,
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
