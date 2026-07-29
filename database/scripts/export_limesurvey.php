<?php

/**
 * Génère un fichier LimeSurvey (.lss) par formulaire interne.
 *
 * Usage :
 *   php database/scripts/export_limesurvey.php [dossier_de_sortie]
 *
 * Les fichiers produits s'importent dans LimeSurvey via :
 *   Enquêtes > Créer une enquête > Importer un fichier .lss
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\InternalForm;

$sortie = $argv[1] ?? __DIR__.'/../limesurvey';
@mkdir($sortie, 0755, true);

/**
 * Correspondance entre les types de champs de la plateforme
 * et les codes de type de question LimeSurvey.
 */
function typeLimeSurvey(string $type): string
{
    return match ($type) {
        'text'     => 'S',  // Texte court
        'textarea' => 'T',  // Texte long
        'email'    => 'S',  // Texte court (avec validation)
        'number'   => 'N',  // Numérique
        'date'     => 'D',  // Date
        'radio'    => 'L',  // Liste (bouton radio)
        'select'   => '!',  // Liste déroulante
        'checkbox' => 'M',  // Choix multiples
        'file'     => '|',  // Téléversement de fichier
        'rating'   => '5',  // Échelle de 1 à 5
        default    => 'S',
    };
}

/** Échappe le contenu destiné à une section CDATA. */
function cdata(?string $valeur): string
{
    return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', (string) $valeur).']]>';
}

/** Construit une ligne <row> à partir d'un tableau associatif. */
function ligne(array $colonnes): string
{
    $xml = "   <row>\n";
    foreach ($colonnes as $nom => $valeur) {
        $xml .= "    <{$nom}>".cdata((string) $valeur)."</{$nom}>\n";
    }

    return $xml."   </row>\n";
}

$total = 0;

foreach (InternalForm::with('fields')->orderBy('id')->get() as $formulaire) {
    $sid = 900000 + $formulaire->id;   // Identifiant d'enquête LimeSurvey
    $gid = $sid + 1;                   // Identifiant du groupe de questions
    $qid = $sid * 10;                  // Base des identifiants de questions

    $questions = '';
    $sousQuestions = '';
    $reponses = '';
    $langueQuestions = '';

    foreach ($formulaire->fields->sortBy('order') as $index => $champ) {
        $qid++;
        $code = 'Q'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

        $questions .= ligne([
            'qid' => $qid,
            'parent_qid' => 0,
            'sid' => $sid,
            'gid' => $gid,
            'type' => typeLimeSurvey($champ->type),
            'title' => $code,
            'preg' => $champ->type === 'email' ? '/^[^@\s]+@[^@\s]+\.[a-zA-Z]{2,}$/' : '',
            'other' => 'N',
            'mandatory' => $champ->required ? 'Y' : 'N',
            'question_order' => $index + 1,
            'scale_id' => 0,
            'same_default' => 0,
            'relevance' => 1,
        ]);

        $langueQuestions .= ligne([
            'qid' => $qid,
            'language' => 'fr',
            'question' => $champ->label,
            'help' => $champ->placeholder ?? '',
            'script' => '',
        ]);

        // Les questions à choix nécessitent des réponses ou sous-questions
        if (in_array($champ->type, ['radio', 'select'], true) && $champ->options) {
            foreach (array_values($champ->options) as $rang => $option) {
                $reponses .= ligne([
                    'aid' => $qid * 100 + $rang + 1,
                    'qid' => $qid,
                    'code' => 'A'.($rang + 1),
                    'sortorder' => $rang + 1,
                    'assessment_value' => 0,
                    'scale_id' => 0,
                    'language' => 'fr',
                    'answer' => $option,
                ]);
            }
        }

        if ($champ->type === 'checkbox' && $champ->options) {
            foreach (array_values($champ->options) as $rang => $option) {
                $sousQuestions .= ligne([
                    'qid' => $qid * 100 + $rang + 1,
                    'parent_qid' => $qid,
                    'sid' => $sid,
                    'gid' => $gid,
                    'type' => 'T',
                    'title' => 'SQ'.str_pad((string) ($rang + 1), 3, '0', STR_PAD_LEFT),
                    'question' => $option,
                    'question_order' => $rang + 1,
                    'language' => 'fr',
                    'scale_id' => 0,
                    'relevance' => 1,
                ]);
            }
        }
    }

    // En-têtes de l'enquête
    $xmlSurveyRow = ligne([
        'sid' => $sid,
        'admin' => 'CSEFRS',
        'active' => 'N',
        'anonymized' => $formulaire->allow_anonymous ? 'Y' : 'N',
        'format' => 'G',           // Une page par groupe
        'language' => 'fr',
        'datestamp' => 'Y',
        'ipaddr' => 'N',
        'showprogress' => 'Y',
        'listpublic' => $formulaire->is_public ? 'Y' : 'N',
    ]);

    $xmlLanguageRow = ligne([
        'surveyls_survey_id' => $sid,
        'surveyls_language' => 'fr',
        'surveyls_title' => $formulaire->title,
        'surveyls_description' => $formulaire->description ?? '',
        'surveyls_welcometext' => 'Merci de participer à cette consultation du CSEFRS.',
        'surveyls_endtext' => 'Votre contribution a bien été enregistrée. Merci de votre participation.',
    ]);

    $xmlGroupRow = ligne([
        'gid' => $gid,
        'sid' => $sid,
        'group_order' => 1,
        'randomization_group' => '',
        'grelevance' => 1,
    ]);

    $xmlGroupL10nRow = ligne([
        'gid' => $gid,
        'group_name' => 'Questionnaire',
        'description' => $formulaire->description ?? '',
        'language' => 'fr',
    ]);

    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<document>
 <LimeSurveyDocType>Survey</LimeSurveyDocType>
 <DBVersion>484</DBVersion>
 <languages>
  <language>fr</language>
 </languages>
 <surveys>
  <fields>
   <fieldname>sid</fieldname>
   <fieldname>admin</fieldname>
   <fieldname>active</fieldname>
   <fieldname>anonymized</fieldname>
   <fieldname>format</fieldname>
   <fieldname>language</fieldname>
   <fieldname>datestamp</fieldname>
   <fieldname>ipaddr</fieldname>
   <fieldname>showprogress</fieldname>
   <fieldname>listpublic</fieldname>
  </fields>
  <rows>
{$xmlSurveyRow}  </rows>
 </surveys>
 <surveys_languagesettings>
  <fields>
   <fieldname>surveyls_survey_id</fieldname>
   <fieldname>surveyls_language</fieldname>
   <fieldname>surveyls_title</fieldname>
   <fieldname>surveyls_description</fieldname>
   <fieldname>surveyls_welcometext</fieldname>
   <fieldname>surveyls_endtext</fieldname>
  </fields>
  <rows>
{$xmlLanguageRow}  </rows>
 </surveys_languagesettings>
 <groups>
  <fields>
   <fieldname>gid</fieldname>
   <fieldname>sid</fieldname>
   <fieldname>group_order</fieldname>
   <fieldname>randomization_group</fieldname>
   <fieldname>grelevance</fieldname>
  </fields>
  <rows>
{$xmlGroupRow}  </rows>
 </groups>
 <group_l10ns>
  <fields>
   <fieldname>gid</fieldname>
   <fieldname>group_name</fieldname>
   <fieldname>description</fieldname>
   <fieldname>language</fieldname>
  </fields>
  <rows>
{$xmlGroupL10nRow}  </rows>
 </group_l10ns>
 <questions>
  <fields>
   <fieldname>qid</fieldname>
   <fieldname>parent_qid</fieldname>
   <fieldname>sid</fieldname>
   <fieldname>gid</fieldname>
   <fieldname>type</fieldname>
   <fieldname>title</fieldname>
   <fieldname>preg</fieldname>
   <fieldname>other</fieldname>
   <fieldname>mandatory</fieldname>
   <fieldname>question_order</fieldname>
   <fieldname>scale_id</fieldname>
   <fieldname>same_default</fieldname>
   <fieldname>relevance</fieldname>
  </fields>
  <rows>
{$questions}  </rows>
 </questions>
 <question_l10ns>
  <fields>
   <fieldname>qid</fieldname>
   <fieldname>language</fieldname>
   <fieldname>question</fieldname>
   <fieldname>help</fieldname>
   <fieldname>script</fieldname>
  </fields>
  <rows>
{$langueQuestions}  </rows>
 </question_l10ns>
 <subquestions>
  <fields>
   <fieldname>qid</fieldname>
   <fieldname>parent_qid</fieldname>
   <fieldname>sid</fieldname>
   <fieldname>gid</fieldname>
   <fieldname>type</fieldname>
   <fieldname>title</fieldname>
   <fieldname>question</fieldname>
   <fieldname>question_order</fieldname>
   <fieldname>language</fieldname>
   <fieldname>scale_id</fieldname>
   <fieldname>relevance</fieldname>
  </fields>
  <rows>
{$sousQuestions}  </rows>
 </subquestions>
 <answers>
  <fields>
   <fieldname>aid</fieldname>
   <fieldname>qid</fieldname>
   <fieldname>code</fieldname>
   <fieldname>sortorder</fieldname>
   <fieldname>assessment_value</fieldname>
   <fieldname>scale_id</fieldname>
   <fieldname>language</fieldname>
   <fieldname>answer</fieldname>
  </fields>
  <rows>
{$reponses}  </rows>
 </answers>
</document>
XML;

    // Slug de nom de fichier lisible
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(
        iconv('UTF-8', 'ASCII//TRANSLIT', $formulaire->title)
    ));
    $slug = trim((string) $slug, '-');
    $chemin = rtrim($sortie, '/')."/{$formulaire->id}-".substr($slug, 0, 60).'.lss';

    file_put_contents($chemin, $xml);
    $total++;
    echo "✓ {$chemin} (".$formulaire->fields->count()." questions)\n";
}

echo "\n{$total} fichier(s) .lss généré(s) dans : {$sortie}\n";
