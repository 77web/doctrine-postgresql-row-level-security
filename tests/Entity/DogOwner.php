<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class DogOwner
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public string $id;

    #[ORM\Column(type: 'string')]
    public string $name;
}
