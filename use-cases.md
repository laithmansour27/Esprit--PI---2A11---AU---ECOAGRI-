# ECOAGRI: use cases to roles (source of the access rules)

Source: "Présentation ECOAGRI" deck v2 (slides 22 to 29). This table drives role-based access control.
**Utilisateur** is a generic actor; Agriculteur, Conseiller and Administrateur inherit its use cases
(register, log in, view and edit own profile, consult advice and alerts).

| Module | Agriculteur | Conseiller | Administrateur |
|---|---|---|---|
| Utilisateurs | (inherited) | (inherited) | Add, edit, delete, list users; assign a role |
| Parcelles et cultures | Add parcel, add culture, list own cultures, edit culture | Analyse parcels and cultures | List parcels (edit, delete parcel), delete culture |
| Irrigation | Add irrigation, list, edit, delete irrigations | Analyse water consumption | Add, list, edit, delete water sources; view volumes |
| Marketplace | Add, edit, delete, list products; place and cancel orders; view and update received orders | none | Validate products; list orders |
| Déchets agricoles | Add waste; publish, edit, delete, list offers; reserve and cancel reservations | none | none |
| Conseils et alertes | Consult advice and alerts, filter by category | Add and edit publications | Delete publications; manage categories |
| Équipements | Add equipment; list own; plan, edit maintenance | none | List equipment (delete), list maintenance (delete) |

## Points to confirm
1. **Acheteur** still appears in two deck texts (slide 4 "Idée du site", slide 12 user-management description) but in no use case diagram. Treated as removed: farmers also buy.
2. **Slide 4** says the Administrateur publishes advice; the use cases give it to the Conseiller. Treated as Conseiller.
3. **Équipements** has a slide, a data model and use cases, but is not one of the six numbered modules.
4. **Permissions**: module 1 mentions "permissions" but no entity exists; role-based access is hard-coded from this table for now.
5. **Conseiller** has no create/edit rights outside publications: analysis is read-only.
6. A farmer must only edit **their own** records; the deck never says it, so that is an assumption.
