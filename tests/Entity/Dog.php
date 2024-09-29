<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Linkage\DoctrineRowLevelSecurity\RowLevelSecurity;

#[ORM\Table]
#[ORM\Entity]
#[RowLevelSecurity(name: 'dog_policy', role: 'dog_owner', using: 'owner_id = current_user::uuid')]
class Dog
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public string $id;

    #[ORM\ManyToOne(targetEntity: DogOwner::class)]
    public DogOwner $owner;

    #[ORM\Column(type: 'string')]
    public string $name;
}
