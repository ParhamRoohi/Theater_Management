<?php

namespace App\Entity; 
 
use App\Repository\ReviewRepository; 
use Doctrine\DBAL\Types\Types; 
use Doctrine\ORM\Mapping as ORM; 
use Symfony\Component\Validator\Constraints as Assert; 
 
#[ORM\Entity(repositoryClass: ReviewRepository::class)] 
class Review 
{ 
    #[ORM\Id] 
    #[ORM\GeneratedValue] 
    #[ORM\Column] 
    private ?int $id = null; 
 
    #[ORM\ManyToOne] 
    #[ORM\JoinColumn(nullable: false)] 
    private ?User $author = null; 
 
    #[ORM\Column(type: Types::TEXT)] 
    #[Assert\NotBlank(message: 'The comment text cannot be empty or just spaces.')] 
    #[Assert\Length( 
        min: 1, 
        max: 500, 
        minMessage: 'The comment text must be at least {{ limit }} character long.', 
        maxMessage: 'The comment text cannot be longer than {{ limit }} characters.' 
    )] 
    private ?string $comment = null; 
 
    #[ORM\ManyToOne] 
    private ?Theater $theaterName = null; 
 
    #[ORM\Column] 
    private ?\DateTimeImmutable $createdAt = null; 
 
    #[ORM\Column] 
    private ?\DateTimeImmutable $updatedAt = null; 
 
    #[ORM\Column(nullable: true)] 
    private ?int $rate = null; 
 
    public function getId(): ?int 
    { 
        return $this->id; 
    } 
 
    public function getAuthor(): ?User 
    { 
        return $this->author; 
    } 
 
    public function setAuthor(?User $author): static 
    { 
        $this->author = $author; 
 
        return $this; 
    } 
 
    public function getComment(): ?string 
    { 
        return $this->comment; 
    } 
 
    public function setComment(string $comment): static 
    { 
        $this->comment = $comment; 
 
        return $this; 
    } 
 
    public function getTheaterName(): ?Theater 
    { 
        return $this->theaterName; 
    } 
 
    public function setTheaterName(?Theater $theaterName): static 
    { 
        $this->theaterName = $theaterName; 
 
        return $this; 
    } 
 
    public function getCreatedAt(): ?\DateTimeImmutable 
    { 
        return $this->createdAt; 
    } 
 
    public function setCreatedAt(\DateTimeImmutable $createdAt): static 
    { 
        $this->createdAt = $createdAt; 
 
        return $this; 
    } 
 
    public function getUpdatedAt(): ?\DateTimeImmutable 
    { 
        return $this->updatedAt; 
    } 
 
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static 
    { 
        $this->updatedAt = $updatedAt; 
 
        return $this; 
    } 
 
    public function getRate(): ?int 
    { 
        return $this->rate; 
    } 
 
    public function setRate(?int $rate): static 
    { 
        $this->rate = $rate; 
 
        return $this; 
    } 
}