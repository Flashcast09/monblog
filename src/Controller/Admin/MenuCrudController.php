<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuCrudController extends AbstractCrudController
{
    const MENU_PAGES = 0;
    const MENU_ARTICLES = 1;
    const MENU_LINKS = 2;
    const MENU_CATEGORIES = 3;

    public function __construct(
        private MenuRepository $menuRepo,
        private RequestStack $requestStack
        )
    {
        
    }

    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $subMenuIndex = $this->getSubMenuIndex();
        return $this->menuRepo->getIndexQueryBuilder($this -> getFieldNameFromSubMenuIndex($subMenuIndex));
    }

    public function configureCrud(Crud $crud): Crud
    {   
        $subMenuIndex = $this->getSubMenuIndex();
        
        $entityLabelInSingular = 'un menu';

        /* A switch statement. */
        $entityLabelInPlural = match ($subMenuIndex){
            self::MENU_ARTICLES => 'Articles',
            self::MENU_CATEGORIES => 'Categories',
            self::MENU_LINKS => 'Liens personnalisés',
            default => 'Pages'
            

        };

        /* Setting the label of the entity in singular and plural. */
        return $crud 
            ->setEntityLabelInSingular($entityLabelInSingular)
            ->setEntityLabelInPlural($entityLabelInPlural);
        // return parent::configureCrud($crud);
        
    }

   

    
   /**
    * It returns an array of fields
    * 
    * @param string pageName The name of the page.
    * 
    * @return iterable An array of fields
    */
    public function configureFields(string $pageName): iterable
    {
        return [
            
            /* Returning an array of fields. */
            $subMenuIndex = $this->getSubMenuIndex(),

            yield TextField::new('name', 'Titre de la navigation'),
            yield NumberField::new('menuOrder', 'Ordre'),
            yield $this ->getFieldFromSubMenuIndex($subMenuIndex) -> setRequired('true'),
            yield BooleanField::new('isVisible', 'Visible'),
            yield AssociationField::new('subMenus', 'Sous-éléments'),
            
        ];
    }

    private function getFieldNameFromSubMenuIndex(int $subMenuIndex): string
    {
        return match ($subMenuIndex){
            self::MENU_ARTICLES => 'article',
            self::MENU_CATEGORIES => 'category',
            self::MENU_LINKS => 'link',
            default => 'page',
        };
    }
     
    private function getFieldFromSubMenuIndex(int $subMenuIndex): AssociationField|TextField
     {
        $fieldName = $this->getFieldNameFromSubMenuIndex($subMenuIndex);
        
        
        return ($fieldName === 'link') ? TextField::new($fieldName, 'Lien') : AssociationField::new($fieldName);

        
     }
   
    

     private function getSubMenuIndex(): int
    {
        return $this->requestStack->getMainRequest()->query->getInt('submenuIndex');
        
    }
}


