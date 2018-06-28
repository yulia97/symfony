<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeRepository")
 * @ORM\Table(name="likes")
 */
class Like
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $liker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likedBy")
     * @ORM\JoinColumn(nullable=false)
     */
    private $liked;

    public function getId()
    {
        return $this->id;
    }

    public function getLiker(): ?User
    {
        return $this->liker;
    }

    public function setLiker(?User $liker): self
    {
        $this->liker = $liker;

        return $this;
    }

    public function getLiked(): ?User
    {
        return $this->liked;
    }

    public function setLiked(?User $liked): self
    {
        $this->liked = $liked;

        return $this;
    }
}
