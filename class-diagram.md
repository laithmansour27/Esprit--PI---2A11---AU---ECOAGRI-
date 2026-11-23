# ECOAGRI: class diagram and database mapping

Sources: the "Présentation ECOAGRI" deck v2 (per-module slides, use cases) and the entity list of your master prompt.
Deck v2 no longer contains the MCD summary slide.
**Only Exploitation–Parcelle is confirmed in the MCD.** Every other link is either read from a
per-module slide of the deck (marked *deck*) or an assumption (marked *À CONFIRMER*).

```mermaid
erDiagram
    ROLES ||--o{ UTILISATEURS : "deck"
    UTILISATEURS ||--o| AGRICULTEURS : "À CONFIRMER (1-1)"
    AGRICULTEURS ||--o{ EXPLOITATIONS : "À CONFIRMER"
    EXPLOITATIONS ||--o{ PARCELLES : "CONFIRMÉ 1-N"
    TYPES_SOL ||--o{ PARCELLES : "À CONFIRMER"
    PARCELLES ||--o{ CULTURES : "deck"
    SOURCES_EAU ||--o{ IRRIGATIONS : "deck"
    PARCELLES ||--o{ IRRIGATIONS : "À CONFIRMER"
    RESSOURCES ||--o{ UTILISATIONS_RESSOURCE : "À CONFIRMER"
    EXPLOITATIONS ||--o{ UTILISATIONS_RESSOURCE : "À CONFIRMER"
    PRATIQUES_ECOLOGIQUES ||--o{ ADOPTIONS_PRATIQUE : "À CONFIRMER"
    EXPLOITATIONS ||--o{ ADOPTIONS_PRATIQUE : "À CONFIRMER"
    AGRICULTEURS ||--o{ PRODUITS : "À CONFIRMER"
    PRODUITS ||--o{ COMMANDES : "deck"
    AGRICULTEURS ||--o{ COMMANDES : "acheteur, À CONFIRMER"
    AGRICULTEURS ||--o{ OFFRES : "réservation, À CONFIRMER"
    AGRICULTEURS ||--o{ EQUIPEMENTS : "À CONFIRMER"
    EQUIPEMENTS ||--o{ MAINTENANCES : "deck"
    UTILISATEURS ||--o{ PUBLICATIONS : "conseiller, À CONFIRMER"
    AGRICULTEURS ||--o{ DECHETS : "À CONFIRMER"
    DECHETS ||--o{ OFFRES : "deck"
    CATEGORIES_INFO ||--o{ PUBLICATIONS : "deck"
```

## Points to confirm with the tutor

1. **Roles**: three (agriculteur, conseiller, administrateur). See `docs/use-cases.md` for the stale "Acheteur" texts.
2. **Two models**: the master prompt's entities (Exploitation, Ressource, PratiqueÉcologique…) and the module slides
   (Produit, Commande, Déchet, Offre, Publication, Équipement…) were never merged in the deck. This schema merges them.
3. **Équipement / Maintenance** are in the deck but outside the six modules.
4. **Text columns replaced by foreign keys**: `nom_agriculteur`, `nom_acheteur`, `nom_parcelle`, `type_sol`.
5. **Permissions**: no permissions table in the deck.
6. **Attributes** of Agriculteur, Exploitation, Ressource, UtilisationRessource and PratiqueÉcologique are not defined.
