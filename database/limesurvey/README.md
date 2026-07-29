# Formulaires thématiques CSEFRS

Ce dossier contient les 9 consultations thématiques, prêtes à l'import dans **LimeSurvey**.

## Contenu

| # | Thématique | Questions |
|---|------------|-----------|
| 2 | Fondements d'un système intégré de développement professionnel des enseignants | 10 |
| 3 | Formation continue face aux mutations économiques et technologiques | 10 |
| 4 | Gouvernance territoriale intégrée du SEFRS | 10 |
| 5 | Histoire du système éducatif marocain | 10 |
| 6 | Intelligence Artificielle et transformation du système d'éducation, de formation et de recherche | 11 |
| 7 | Ouverture des établissements sur leur environnement local | 10 |
| 8 | Pour un accès effectif et l'accomplissement du droit à l'enseignement obligatoire | 11 |
| 9 | Transformation curriculaire : enjeux, défis et clés de construction | 11 |
| 10 | Valorisation de la recherche scientifique au Maroc | 11 |

Chaque formulaire comprend :
- un **tronc commun** (nom, e-mail, profil, région) ;
- des **questions spécifiques** à la thématique ;
- une **clôture commune** (échelle d'importance, recommandations libres).

---

## A. Import dans LimeSurvey

### Via l'interface web

1. Se connecter à l'administration LimeSurvey.
2. **Enquêtes** → **Créer une enquête** → onglet **Importer**.
3. Sélectionner un fichier `.lss` de ce dossier, puis **Importer l'enquête**.
4. Répéter pour chacune des 9 thématiques.
5. Activer l'enquête (bouton **Activer cette enquête**).

### Via la ligne de commande (sur le serveur)

```bash
# Copier les fichiers vers le serveur
scp database/limesurvey/*.lss root@192.168.4.182:/tmp/lss/

# Puis, sur le serveur, pour chaque fichier :
cd /var/www/limesurvey
php application/commands/console.php import survey /tmp/lss/2-fondements-*.lss
```

> Si la commande `import` n'est pas disponible dans votre version de LimeSurvey,
> utilisez l'interface web — l'import `.lss` y est toujours pris en charge.

---

## B. Import dans la plateforme Citizen Forms

Les mêmes formulaires sont générés directement en base par un seeder :

```bash
cd /var/www/citizen-forms
php artisan db:seed --class=SefrsFormsSeeder --force
```

Le seeder est **idempotent** : relancé, il ignore les formulaires déjà présents
(comparaison sur le titre) et ne crée pas de doublons.

Les formulaires sont créés avec le statut `active`, en accès **public** et
**anonyme**, donc immédiatement accessibles à :

```
https://votre-domaine/forms/internal/{id}
```

---

## C. Régénérer les fichiers .lss

Si vous modifiez les formulaires dans la plateforme, régénérez les exports :

```bash
php database/scripts/export_limesurvey.php
```

Pour écrire dans un autre dossier :

```bash
php database/scripts/export_limesurvey.php /chemin/de/sortie
```

---

## Correspondance des types de questions

| Plateforme | LimeSurvey | Libellé |
|------------|-----------|---------|
| `text` | `S` | Texte court |
| `textarea` | `T` | Texte long |
| `email` | `S` | Texte court + validation e-mail |
| `number` | `N` | Numérique |
| `date` | `D` | Date |
| `radio` | `L` | Liste (boutons radio) |
| `select` | `!` | Liste déroulante |
| `checkbox` | `M` | Choix multiples |
| `file` | `\|` | Téléversement de fichier |
| `rating` | `5` | Échelle de 1 à 5 |
