<?php
class Reponse
{
    private ?int $id;
    private string $contenu;
    private int $idPublication;
    private int $idUser;

    public function __construct(string $contenu, int $idPublication, int $idUser = 1, ?int $id = null)
    {
        $this->contenu = $contenu;
        $this->idPublication = $idPublication;
        $this->idUser = $idUser;
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getContenu(): string { return $this->contenu; }
    public function getIdPublication(): int { return $this->idPublication; }
    public function getIdUser(): int { return $this->idUser; }
}