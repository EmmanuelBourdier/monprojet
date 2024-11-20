<?php

namespace App\Controller\Admin;

use App\Entity\Commentaire;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentaireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commentaire::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('message')->setLabel('Commentaire')
            ->setFormTypeOptions([
                'attr' => ['rows' => 10],
            ])
            ->setTemplatePath('admin/fields/textarea.html.twig'), // Optional: Custom template for better display,
                
            TextField::new('verified')->setLabel('Accepté')
        ];
    }
}
