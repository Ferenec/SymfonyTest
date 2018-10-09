<?php

namespace App\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductDataAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('intProductDataId', IntegerType::class, ['label' => 'ID'])
            ->add('strProductName', TextType::class, ['label' => 'Product Name'])
            ->add('strProductDesc', TextareaType::class, ['label' => 'Product Description'])
            ->add('strProductCode', TextType::class, ['label' => 'Product Code'])
            ->add('dtmAdded', DateType::class, ['label' => 'Date Added'])
            ->add('dtmDiscontinued', DateType::class, ['label' => 'Discontinued'])
            ->add('stmTimestamp', DateType::class, ['label' => 'Date'])
            ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('strProductCode')
            ->addIdentifier('strProductName')
            ->add('dtmDiscontinued')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid->add('strProductCode');
    }

}