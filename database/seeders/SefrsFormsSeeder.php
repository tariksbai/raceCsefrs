<?php

namespace Database\Seeders;

use App\Models\InternalForm;
use App\Models\InternalFormField;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder des 9 formulaires thématiques du CSEFRS.
 *
 * Chaque thématique génère un formulaire interne public et actif,
 * composé d'un tronc commun (profil du répondant) et de questions
 * spécifiques au sujet traité.
 */
class SefrsFormsSeeder extends Seeder
{
    /**
     * Questions communes posées en tête de chaque formulaire.
     */
    private function champsCommuns(): array
    {
        return [
            [
                'type' => 'text',
                'label' => 'Nom et prénom (facultatif)',
                'placeholder' => 'Laissez vide pour répondre anonymement',
                'required' => false,
            ],
            [
                'type' => 'email',
                'label' => 'Adresse électronique (facultatif)',
                'placeholder' => 'exemple@domaine.ma',
                'required' => false,
            ],
            [
                'type' => 'select',
                'label' => 'Votre profil',
                'required' => true,
                'options' => [
                    'Enseignant(e)',
                    'Cadre administratif',
                    'Inspecteur / Inspectrice',
                    'Chercheur / Chercheuse',
                    'Étudiant(e)',
                    'Parent d\'élève',
                    'Représentant(e) de la société civile',
                    'Autre',
                ],
            ],
            [
                'type' => 'select',
                'label' => 'Région',
                'required' => true,
                'options' => [
                    'Tanger-Tétouan-Al Hoceïma',
                    'L\'Oriental',
                    'Fès-Meknès',
                    'Rabat-Salé-Kénitra',
                    'Béni Mellal-Khénifra',
                    'Casablanca-Settat',
                    'Marrakech-Safi',
                    'Drâa-Tafilalet',
                    'Souss-Massa',
                    'Guelmim-Oued Noun',
                    'Laâyoune-Sakia El Hamra',
                    'Dakhla-Oued Ed-Dahab',
                ],
            ],
        ];
    }

    /**
     * Questions de clôture communes à tous les formulaires.
     */
    private function champsCloture(): array
    {
        return [
            [
                'type' => 'rating',
                'label' => 'Quelle importance accordez-vous à cette thématique ? (1 = faible, 5 = très élevée)',
                'required' => true,
            ],
            [
                'type' => 'textarea',
                'label' => 'Recommandations et propositions complémentaires',
                'placeholder' => 'Exprimez librement vos propositions...',
                'required' => false,
            ],
        ];
    }

    /**
     * Définition des 9 thématiques et de leurs questions spécifiques.
     */
    private function thematiques(): array
    {
        return [
            [
                'title' => 'Fondements d\'un système intégré de développement professionnel des enseignants',
                'description' => 'Consultation sur les bases d\'un dispositif national et intégré de développement professionnel tout au long de la carrière enseignante.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels leviers prioritaires pour le développement professionnel des enseignants ?',
                        'required' => true,
                        'options' => [
                            'Formation initiale renforcée',
                            'Formation continue certifiante',
                            'Accompagnement par les pairs (mentorat)',
                            'Évaluation formative des pratiques',
                            'Mobilité et évolution de carrière',
                            'Ressources numériques pédagogiques',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'Le dispositif actuel de formation continue répond-il aux besoins réels des enseignants ?',
                        'required' => true,
                        'options' => ['Tout à fait', 'Plutôt oui', 'Plutôt non', 'Pas du tout', 'Sans avis'],
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Nombre de jours de formation continue suivis durant les 12 derniers mois',
                        'required' => false,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quels obstacles freinent aujourd\'hui le développement professionnel ?',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Formation continue face aux mutations économiques et technologiques',
                'description' => 'Consultation sur l\'adaptation de la formation continue aux transformations du marché du travail et aux évolutions technologiques.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels secteurs nécessitent en priorité une offre de formation continue renforcée ?',
                        'required' => true,
                        'options' => [
                            'Numérique et intelligence artificielle',
                            'Énergies renouvelables',
                            'Industrie automobile et aéronautique',
                            'Agriculture et agro-industrie',
                            'Santé et biotechnologies',
                            'Tourisme et services',
                            'Logistique et transport',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'L\'offre actuelle de formation continue est-elle alignée sur les besoins des entreprises ?',
                        'required' => true,
                        'options' => ['Fortement alignée', 'Partiellement alignée', 'Faiblement alignée', 'Non alignée', 'Sans avis'],
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Quelle modalité de formation vous semble la plus efficace ?',
                        'required' => true,
                        'options' => ['Présentiel', 'À distance (e-learning)', 'Hybride', 'Formation en situation de travail', 'Alternance'],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quelles compétences nouvelles devraient être intégrées en priorité ?',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Gouvernance territoriale intégrée du SEFRS',
                'description' => 'Consultation sur la déconcentration, la régionalisation avancée et la coordination territoriale du système d\'éducation, de formation et de recherche scientifique.',
                'champs' => [
                    [
                        'type' => 'radio',
                        'label' => 'Le niveau actuel de déconcentration des décisions vous paraît-il suffisant ?',
                        'required' => true,
                        'options' => ['Largement suffisant', 'Suffisant', 'Insuffisant', 'Très insuffisant', 'Sans avis'],
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => 'Quelles compétences devraient être transférées au niveau régional ?',
                        'required' => true,
                        'options' => [
                            'Gestion des ressources humaines',
                            'Planification de la carte scolaire',
                            'Gestion budgétaire',
                            'Adaptation curriculaire locale',
                            'Partenariats avec les collectivités territoriales',
                            'Évaluation et suivi de la qualité',
                        ],
                    ],
                    [
                        'type' => 'rating',
                        'label' => 'Qualité de la coordination entre AREF, directions provinciales et établissements',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quels mécanismes de coordination territoriale proposez-vous ?',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Histoire du système éducatif marocain',
                'description' => 'Consultation sur la mémoire, les héritages et les grandes étapes de construction du système éducatif national.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Quelles périodes vous semblent déterminantes dans l\'évolution du système éducatif ?',
                        'required' => true,
                        'options' => [
                            'Enseignement traditionnel (avant 1912)',
                            'Période du Protectorat (1912-1956)',
                            'Marocanisation et unification (1956-1980)',
                            'Réformes des années 1980-1990',
                            'Charte Nationale d\'Éducation et de Formation (1999)',
                            'Programme d\'urgence (2009-2012)',
                            'Vision Stratégique 2015-2030',
                            'Loi-cadre 51.17 (2019)',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'L\'histoire du système éducatif est-elle suffisamment enseignée et documentée ?',
                        'required' => true,
                        'options' => ['Oui, pleinement', 'Partiellement', 'Insuffisamment', 'Pas du tout', 'Sans avis'],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quels enseignements tirer du passé pour les réformes actuelles ?',
                        'required' => false,
                    ],
                    [
                        'type' => 'file',
                        'label' => 'Document ou témoignage à verser à la mémoire collective (facultatif)',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Intelligence Artificielle et transformation du système d\'éducation, de formation et de recherche',
                'description' => 'Consultation sur les opportunités, les risques et les conditions d\'un usage responsable de l\'intelligence artificielle dans le SEFRS.',
                'champs' => [
                    [
                        'type' => 'radio',
                        'label' => 'Utilisez-vous déjà des outils d\'intelligence artificielle dans votre pratique ?',
                        'required' => true,
                        'options' => ['Quotidiennement', 'Régulièrement', 'Occasionnellement', 'Jamais'],
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels usages de l\'IA vous paraissent les plus prometteurs ?',
                        'required' => true,
                        'options' => [
                            'Personnalisation des apprentissages',
                            'Aide à la correction et à l\'évaluation',
                            'Génération de ressources pédagogiques',
                            'Détection précoce du décrochage scolaire',
                            'Assistance à la recherche scientifique',
                            'Gestion administrative et planification',
                            'Accessibilité et inclusion',
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels risques vous préoccupent le plus ?',
                        'required' => true,
                        'options' => [
                            'Plagiat et intégrité académique',
                            'Protection des données personnelles',
                            'Creusement des inégalités numériques',
                            'Perte d\'esprit critique des apprenants',
                            'Biais algorithmiques',
                            'Dépendance technologique',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'Une charte nationale d\'usage de l\'IA dans l\'éducation est-elle nécessaire ?',
                        'required' => true,
                        'options' => ['Indispensable', 'Souhaitable', 'Peu utile', 'Inutile', 'Sans avis'],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quelles conditions pour un déploiement responsable de l\'IA ?',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Ouverture des établissements sur leur environnement local',
                'description' => 'Consultation sur les partenariats entre établissements scolaires et universitaires et leur écosystème territorial.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Avec quels acteurs les partenariats devraient-ils être renforcés ?',
                        'required' => true,
                        'options' => [
                            'Collectivités territoriales',
                            'Entreprises et secteur privé',
                            'Associations et société civile',
                            'Institutions culturelles',
                            'Structures sportives',
                            'Établissements de santé',
                            'Universités et centres de recherche',
                        ],
                    ],
                    [
                        'type' => 'rating',
                        'label' => 'Niveau actuel d\'ouverture de votre établissement sur son environnement',
                        'required' => true,
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'Les locaux scolaires devraient-ils être ouverts à la communauté hors temps scolaire ?',
                        'required' => true,
                        'options' => ['Oui, largement', 'Oui, sous conditions', 'Non', 'Sans avis'],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quelles initiatives locales réussies mériteraient d\'être généralisées ?',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Pour un accès effectif et l\'accomplissement du droit à l\'enseignement obligatoire',
                'description' => 'Consultation sur la scolarisation universelle, la lutte contre l\'abandon scolaire et l\'effectivité du droit à l\'éducation.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels sont les principaux freins à la scolarisation effective ?',
                        'required' => true,
                        'options' => [
                            'Éloignement géographique',
                            'Précarité économique des familles',
                            'Insuffisance du transport scolaire',
                            'Manque d\'internats et de cantines',
                            'Facteurs socio-culturels',
                            'Handicap et absence d\'aménagements',
                            'Travail des enfants',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'L\'obligation scolaire jusqu\'à 15 ans est-elle effectivement appliquée ?',
                        'required' => true,
                        'options' => ['Pleinement', 'Majoritairement', 'Partiellement', 'Faiblement', 'Sans avis'],
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => 'Quelles mesures prioritaires contre l\'abandon scolaire ?',
                        'required' => true,
                        'options' => [
                            'Soutien scolaire ciblé',
                            'Aides financières aux familles (Tayssir)',
                            'Développement du préscolaire',
                            'Écoles communautaires en milieu rural',
                            'Accompagnement psychosocial',
                            'Passerelles vers la formation professionnelle',
                        ],
                    ],
                    [
                        'type' => 'date',
                        'label' => 'Date de votre dernière observation de terrain sur ce sujet (facultatif)',
                        'required' => false,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Propositions pour garantir l\'effectivité du droit à l\'éducation',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Transformation curriculaire : enjeux, défis et clés de construction',
                'description' => 'Consultation sur la refonte des curricula, les approches pédagogiques et l\'articulation entre savoirs et compétences.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Quelles compétences le curriculum doit-il développer en priorité ?',
                        'required' => true,
                        'options' => [
                            'Maîtrise des langues',
                            'Compétences scientifiques et mathématiques',
                            'Compétences numériques',
                            'Esprit critique et résolution de problèmes',
                            'Compétences citoyennes et valeurs',
                            'Créativité et expression artistique',
                            'Compétences entrepreneuriales',
                            'Ouverture culturelle et amazighité',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'Les programmes actuels sont-ils trop chargés ?',
                        'required' => true,
                        'options' => ['Très surchargés', 'Plutôt surchargés', 'Équilibrés', 'Trop légers', 'Sans avis'],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'Quelle place accorder à la langue amazighe dans le curriculum ?',
                        'required' => true,
                        'options' => ['Généralisation à tous les niveaux', 'Extension progressive', 'Maintien de l\'existant', 'Sans avis'],
                    ],
                    [
                        'type' => 'rating',
                        'label' => 'Adéquation entre les curricula et les besoins réels des apprenants',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Quelles clés de réussite pour la transformation curriculaire ?',
                        'required' => false,
                    ],
                ],
            ],
            [
                'title' => 'Valorisation de la recherche scientifique au Maroc',
                'description' => 'Consultation sur le financement, la diffusion et le transfert des résultats de la recherche scientifique vers l\'économie et la société.',
                'champs' => [
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels leviers pour mieux valoriser la recherche nationale ?',
                        'required' => true,
                        'options' => [
                            'Augmentation du financement public',
                            'Incitations au partenariat public-privé',
                            'Renforcement des brevets et de la propriété intellectuelle',
                            'Création d\'incubateurs et de start-up académiques',
                            'Mobilité des chercheurs',
                            'Science ouverte et libre accès aux publications',
                            'Valorisation de la carrière des chercheurs',
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'label' => 'Le budget national consacré à la recherche est-il suffisant ?',
                        'required' => true,
                        'options' => ['Suffisant', 'Insuffisant', 'Très insuffisant', 'Sans avis'],
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => 'Quels domaines de recherche devraient être prioritaires ?',
                        'required' => true,
                        'options' => [
                            'Eau et sécurité hydrique',
                            'Énergies renouvelables',
                            'Santé et sciences médicales',
                            'Agriculture et sécurité alimentaire',
                            'Intelligence artificielle et numérique',
                            'Sciences humaines et sociales',
                            'Changement climatique',
                        ],
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Nombre de publications scientifiques que vous avez produites (facultatif)',
                        'required' => false,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Comment renforcer le lien entre recherche, entreprise et société ?',
                        'required' => false,
                    ],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $auteur = User::where('user_type', 'admin')->first();

        foreach ($this->thematiques() as $thematique) {
            // Évite les doublons si le seeder est relancé
            if (InternalForm::where('title', $thematique['title'])->exists()) {
                $this->command->warn("Déjà présent, ignoré : {$thematique['title']}");
                continue;
            }

            $champs = array_merge(
                $this->champsCommuns(),
                $thematique['champs'],
                $this->champsCloture()
            );

            // Structure JSON conservée pour le constructeur drag & drop
            $structure = ['fields' => []];
            foreach ($champs as $index => $champ) {
                $structure['fields'][] = [
                    'type' => $champ['type'],
                    'label' => $champ['label'],
                    'placeholder' => $champ['placeholder'] ?? '',
                    'required' => $champ['required'],
                    'options' => $champ['options'] ?? [],
                    'order' => $index,
                ];
            }

            $formulaire = InternalForm::create([
                'title' => $thematique['title'],
                'description' => $thematique['description'],
                'is_public' => true,
                'allow_anonymous' => true,
                'status' => 'active',
                'structure' => $structure,
                'created_by' => $auteur?->id,
            ]);

            foreach ($champs as $index => $champ) {
                InternalFormField::create([
                    'form_id' => $formulaire->id,
                    'type' => $champ['type'],
                    'label' => $champ['label'],
                    'placeholder' => $champ['placeholder'] ?? null,
                    'required' => $champ['required'],
                    'options' => $champ['options'] ?? null,
                    'order' => $index,
                ]);
            }

            $this->command->info("Créé ({$formulaire->id}) : {$thematique['title']} — ".count($champs).' champs');
        }
    }
}
