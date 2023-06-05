<?php 

namespace App\Service;

use App\Repository\MenuRepository;
use App\Entity\Menu;

class MenuService{

    public function __construct(private MenuRepository $MenuRepo)
    {

    }

    /**
     * @return Menu[]
     */

    public function findAll(): array
    {
        return $this->MenuRepo->findAllForTwig();
    }
}