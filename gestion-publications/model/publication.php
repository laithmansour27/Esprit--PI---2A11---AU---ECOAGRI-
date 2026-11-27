<?php
class Publication
{
    private ?int $id;
    private string $type;
    private string $titre;
    private string $contenu;
    private string $niveau;
    private string $region;
    private string $datePublication;
    private string $etat;
    private int $idUser;

    public function __construct(
        string $type,
        string $titre,
        string $contenu,
        string $niveau,
        string $region,
        string $datePublication,
        string $etat,
        int $idUser = 1,
        ?int $id = null
    ) {
        $this->type = $type;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->niveau = $niveau;
        $this->region = $region;
        $this->datePublication = $datePublication;
        $this->etat = $etat;
        $this->idUser = $idUser;
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getType(): string { return $this->type; }
    public function getTitre(): string { return $this->titre; }
    public function getContenu(): string { return $this->contenu; }
    public function getNiveau(): string { return $this->niveau; }
    public function getRegion(): string { return $this->region; }
    public function getDatePublication(): string { return $this->datePublication; }
    public function getEtat(): string { return $this->etat; }
    public function getIdUser(): int { return $this->idUser; }
}