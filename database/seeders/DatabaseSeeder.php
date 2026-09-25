<?php

namespace Database\Seeders;

use App\Models\AnneeScolaire;
use App\Models\Batiment;
use App\Models\Campagne;
use App\Models\CandidatProfile;
use App\Models\Chambre;
use App\Models\CritereSelection;
use App\Models\DocType;
use App\Models\Dossier;
use App\Models\DossierDoc;
use App\Models\Etablissement;
use App\Models\Filiere;
use App\Models\IncidentReclamation;
use App\Models\Internat;
use App\Models\Lit;
use App\Models\Mouvement;
use App\Models\Niveau;
use App\Models\Presence;
use App\Models\Region;
use App\Models\User;
use App\Services\RestaurationService;
use App\Services\SelectionScoringService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Régions
        $regCasa = Region::create(['code' => 'CS', 'nom' => 'Casablanca-Settat', 'chef_lieu' => 'Casablanca']);
        $regRabat = Region::create(['code' => 'RSK', 'nom' => 'Rabat-Salé-Kénitra', 'chef_lieu' => 'Rabat']);
        $regTanger = Region::create(['code' => 'TTA', 'nom' => 'Tanger-Tétouan-Al Hoceïma', 'chef_lieu' => 'Tanger']);
        $regMarrakech = Region::create(['code' => 'MS', 'nom' => 'Marrakech-Safi', 'chef_lieu' => 'Marrakech']);

        // 2. Établissements
        $etabCasa = Etablissement::create([
            'region_id' => $regCasa->id,
            'code' => 'CMC-CS',
            'nom' => 'Cité des Métiers et des Compétences Casablanca-Settat',
            'ville' => 'Nouaceur',
            'adresse' => 'Zone Urbaine Nouaceur, Casablanca',
            'telephone' => '0522001122',
            'email' => 'cmc.casa@ofppt.ma',
        ]);

        $etabRabat = Etablissement::create([
            'region_id' => $regRabat->id,
            'code' => 'ISTA-HR',
            'nom' => 'ISTA Hay Riad Rabat',
            'ville' => 'Rabat',
            'adresse' => 'Avenue Allal El Fassi, Rabat',
            'telephone' => '0537002233',
            'email' => 'ista.hayriad@ofppt.ma',
        ]);

        $etabTanger = Etablissement::create([
            'region_id' => $regTanger->id,
            'code' => 'CMC-TTA',
            'nom' => 'Cité des Métiers et des Compétences Tanger',
            'ville' => 'Tanger',
            'adresse' => 'Tanger Automotive City, Gzenaya',
            'telephone' => '0539003344',
            'email' => 'cmc.tanger@ofppt.ma',
        ]);

        // 3. Maisons des Stagiaires (Internats)
        $internatCasa = Internat::create([
            'etablissement_id' => $etabCasa->id,
            'nom' => 'Maison des Stagiaires CMC Casablanca-Settat',
            'capacite_totale' => 120,
            'capacite_garcons' => 60,
            'capacite_filles' => 60,
            'description' => "Résidence moderne pour stagiaires avec espaces de restauration, foyer, salles d'étude et connexion haut débit.",
            'reglement_interieur' => "1. Couvre-feu fixé à 22h00 les soirs de semaine et 23h00 le week-end.\n2. Respect absolu de la tranquillité et du repos d'autrui.\n3. Propreté obligatoire des chambres et respect du mobilier.\n4. Interdiction stricte de fumer dans les bâtiments.",
            'date_arrivee_prevue' => Carbon::now()->addDays(5)->toDateString(),
            'telephone' => '0522001125',
            'statut' => 'actif',
        ]);

        $internatRabat = Internat::create([
            'etablissement_id' => $etabRabat->id,
            'nom' => 'Maison des Stagiaires ISTA Hay Riad',
            'capacite_totale' => 80,
            'capacite_garcons' => 40,
            'capacite_filles' => 40,
            'description' => 'Internat rénové au cœur du pôle technologique de Rabat.',
            'reglement_interieur' => "Règlement intérieur standard de l'OFPPT pour les internats.",
            'date_arrivee_prevue' => Carbon::now()->addDays(7)->toDateString(),
            'telephone' => '0537002238',
            'statut' => 'actif',
        ]);

        // 4. Bâtiments, Chambres et Lits pour Internat Casablanca
        $batA = Batiment::create([
            'internat_id' => $internatCasa->id,
            'nom' => 'Bâtiment A - Résidence Garçons',
            'code' => 'BAT-A',
            'genre' => 'garcons',
            'nombre_etages' => 2,
            'description' => 'Bâtiment hébergeant les stagiaires masculins',
        ]);

        $batB = Batiment::create([
            'internat_id' => $internatCasa->id,
            'nom' => 'Bâtiment B - Résidence Filles',
            'code' => 'BAT-B',
            'genre' => 'filles',
            'nombre_etages' => 2,
            'description' => 'Bâtiment hébergeant les stagiaires féminines',
        ]);

        // Créer chambres et lits
        $allLitsCasa = [];
        foreach ([$batA, $batB] as $bat) {
            for ($etage = 0; $etage <= 1; $etage++) {
                for ($ch = 1; $ch <= 4; $ch++) {
                    $numChambre = ($etage * 100) + $ch;
                    $isPMR = ($etage === 0 && $ch === 1);
                    $chambre = Chambre::create([
                        'batiment_id' => $bat->id,
                        'numero' => "CH-{$numChambre}",
                        'etage' => $etage,
                        'capacite' => 2,
                        'type' => $isPMR ? 'pmr' : 'standard',
                        'statut' => 'disponible',
                        'equipements' => '2 lits simples, 2 armoires, 2 bureaux individuels, salle d’eau privative, Wi-Fi',
                    ]);

                    for ($l = 1; $l <= 2; $l++) {
                        $lit = Lit::create([
                            'chambre_id' => $chambre->id,
                            'code' => "{$bat->code}-CH{$numChambre}-L{$l}",
                            'position' => $l === 1 ? 'bas' : 'haut',
                            'statut' => 'libre',
                        ]);
                        $allLitsCasa[] = $lit;
                    }
                }
            }
        }

        // 5. Niveaux de formation OFPPT
        $niveauxData = [
            ['code' => 'TS', 'nom' => 'Niveau Technicien Spécialisé', 'description' => 'Bac+2 délivrant un diplôme de Technicien Spécialisé.'],
            ['code' => 'T', 'nom' => 'Niveau Technicien', 'description' => 'Formation niveau Bac accessible dès le niveau 2ème année Bac.'],
            ['code' => 'Q', 'nom' => 'Niveau Qualification', 'description' => 'Formation qualifiante accessible après la 3ème année de collège.'],
            ['code' => 'S', 'nom' => 'Niveau Spécialisation', 'description' => 'Formation pratique accessible dès le cycle primaire.'],
        ];

        $niveauxMap = [];
        foreach ($niveauxData as $nData) {
            $niveauxMap[$nData['code']] = Niveau::create($nData);
        }

        // 6. Filières
        $filieresData = [
            ['niveau_id' => $niveauxMap['TS']->id, 'code' => 'DEV-DIG', 'nom' => 'Développement Digital - Option Web Full Stack'],
            ['niveau_id' => $niveauxMap['TS']->id, 'code' => 'INFRA-SYS', 'nom' => 'Infrastructure Digitale - Option CyberSécurité'],
            ['niveau_id' => $niveauxMap['TS']->id, 'code' => 'GE', 'nom' => 'Gestion des Entreprises'],
            ['niveau_id' => $niveauxMap['T']->id, 'code' => 'ELEC-INDUS', 'nom' => 'Électricité Industrielle'],
            ['niveau_id' => $niveauxMap['T']->id, 'code' => 'MECA-AUTO', 'nom' => 'Mécanique Automobile & Diagnostic'],
            ['niveau_id' => $niveauxMap['Q']->id, 'code' => 'SOUDURE', 'nom' => 'Soudage et Chaudronnerie'],
            ['niveau_id' => $niveauxMap['S']->id, 'code' => 'ELEC-BAT', 'nom' => 'Électricité de Bâtiment'],
        ];

        $filieresList = [];
        foreach ($filieresData as $fData) {
            $filieresList[] = Filiere::create($fData);
        }

        // 7. Pièces justificatives requises (DocTypes)
        $docTypes = [
            ['code' => 'cin', 'nom' => "Carte d'Identité Nationale (CIN ou Tuteur)", 'description' => 'Copie recto-verso de la CIN du candidat (ou du père/tuteur si mineur)', 'est_obligatoire' => true],
            ['code' => 'attestation_scolaire', 'nom' => 'Attestation d’inscription ou poursuite de formation', 'description' => 'Attestation officielle délivrée par l’établissement OFPPT', 'est_obligatoire' => true],
            ['code' => 'justificatif_revenu', 'nom' => 'Justificatif des revenus des parents / tuteur', 'description' => 'Attestation de salaire, avis d’imposition ou certificat de non-imposition', 'est_obligatoire' => true],
            ['code' => 'certificat_medical', 'nom' => 'Certificat Médical d’aptitude à la vie en collectivité', 'description' => 'Délivré par un médecin attestant de l’absence de maladies contagieuses', 'est_obligatoire' => true],
            ['code' => 'photo_identite', 'nom' => 'Photo d’identité récente', 'description' => 'Photo couleur format passeport avec fond blanc', 'est_obligatoire' => false],
        ];

        $docTypesList = [];
        foreach ($docTypes as $dType) {
            $docTypesList[] = DocType::create($dType);
        }

        // 8. Critères de sélection nationaux
        $criteres = [
            ['code' => 'CRIT-DIST', 'nom' => 'Éloignement géographique du domicile familial', 'type_critere' => 'distance', 'points_base' => 30, 'poids' => 1.20, 'description' => 'Priorité aux candidats habitant à plus de 100km de l’établissement'],
            ['code' => 'CRIT-REV', 'nom' => 'Situation socio-économique (Revenus modestes)', 'type_critere' => 'revenu', 'points_base' => 25, 'poids' => 1.00, 'description' => 'Barème inversement proportionnel aux revenus déclarés des parents'],
            ['code' => 'CRIT-ORPH', 'nom' => 'Situation familiale critique (Orphelin)', 'type_critere' => 'orphelin', 'points_base' => 15, 'poids' => 1.00, 'description' => 'Bonus de priorité sociale pour les candidats orphelins'],
            ['code' => 'CRIT-HAND', 'nom' => 'Stagiaire en situation de handicap (PMR)', 'type_critere' => 'handicap', 'points_base' => 20, 'poids' => 1.50, 'description' => 'Accès prioritaire aux chambres équipées du rez-de-chaussée'],
            ['code' => 'CRIT-RECOND', 'nom' => 'Reconduction ancien stagiaire assidu', 'type_critere' => 'reconduction', 'points_base' => 10, 'poids' => 1.00, 'description' => 'Reconduction pour les stagiaires ayant séjourné avec succès l’an passé'],
        ];

        foreach ($criteres as $crit) {
            CritereSelection::create($crit);
        }

        // 9. Année scolaire et Campagne
        $annee = AnneeScolaire::create([
            'code' => '2026-2027',
            'date_debut' => '2026-09-01',
            'date_fin' => '2027-06-30',
            'est_active' => true,
        ]);

        $campagne = Campagne::create([
            'annee_scolaire_id' => $annee->id,
            'internat_id' => $internatCasa->id,
            'titre' => 'Campagne d’Hébergement 2026-2027 - Maison des Stagiaires CMC Casa',
            'date_debut' => Carbon::now()->subDays(15),
            'date_fin' => Carbon::now()->addDays(20),
            'statut' => 'ouverte',
            'instructions' => 'Veuillez renseigner rigoureusement vos données socio-économiques et téléverser des documents lisibles en PDF ou JPG. Toute fausse déclaration entraîne le rejet immédiat du dossier.',
            'quota_places' => 60,
        ]);

        // 10. Comptes Utilisateurs clés (Admin, Gestionnaire, Directeur, Directions)
        $usersToCreate = [
            [
                'name' => 'Driss Alami (Administrateur)',
                'email' => 'driss.alami@ofppt.ma',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'telephone' => '0661000001',
            ],
            [
                'name' => 'Fatima Zahra Bennani (Gestionnaire Internat)',
                'email' => 'fatima.zahra@ofppt.ma',
                'password' => Hash::make('password'),
                'role' => 'gestionnaire',
                'telephone' => '0661000002',
                'etablissement_id' => $etabCasa->id,
                'internat_id' => $internatCasa->id,
                'region_id' => $regCasa->id,
            ],
            [
                'name' => 'Mohammed Chaoui (Directeur Établissement)',
                'email' => 'mohammed.chaoui@ofppt.ma',
                'password' => Hash::make('password'),
                'role' => 'directeur',
                'telephone' => '0661000003',
                'etablissement_id' => $etabCasa->id,
                'internat_id' => $internatCasa->id,
                'region_id' => $regCasa->id,
            ],
            [
                'name' => 'Amina Tazi (Direction Régionale)',
                'email' => 'amina.tazi@ofppt.ma',
                'password' => Hash::make('password'),
                'role' => 'direction_regionale',
                'telephone' => '0661000004',
                'region_id' => $regCasa->id,
            ],
            [
                'name' => 'Karim Idrissi (Direction Centrale OFPPT)',
                'email' => 'karim.idrissi@ofppt.ma',
                'password' => Hash::make('password'),
                'role' => 'direction_centrale',
                'telephone' => '0661000005',
            ],
        ];

        foreach ($usersToCreate as $uData) {
            User::create($uData);
        }

        // 11. Création de Candidats / Stagiaires avec profils variés
        $candidatsSeed = [
            [
                'name' => 'Yassine Mansouri',
                'email' => 'yassine@example.com',
                'cin' => 'BH458291',
                'cef' => 'CEF-2026-98124',
                'date_naissance' => '2004-05-14',
                'genre' => 'garcon',
                'ville_origine' => 'Ouarzazate',
                'distance_km' => 450,
                'revenu' => 2200.00,
                'freres' => 4,
                'orphelin' => true,
                'handicap' => false,
                'regime' => 'Normal',
                'statut_dossier' => 'admis',
                'lit_index' => 0, // aura le lit 0
            ],
            [
                'name' => 'Salma Amrani',
                'email' => 'salma@example.com',
                'cin' => 'CD339182',
                'cef' => 'CEF-2026-77312',
                'date_naissance' => '2005-08-22',
                'genre' => 'fille',
                'ville_origine' => 'Errachidia',
                'distance_km' => 520,
                'revenu' => 1900.00,
                'freres' => 3,
                'orphelin' => false,
                'handicap' => false,
                'regime' => 'Sans gluten',
                'statut_dossier' => 'admis',
                'lit_index' => 16, // premier lit du batiment filles
            ],
            [
                'name' => 'Hamza Berrada',
                'email' => 'hamza@example.com',
                'cin' => 'EE992144',
                'cef' => 'CEF-2026-44219',
                'date_naissance' => '2003-11-03',
                'genre' => 'garcon',
                'ville_origine' => 'Khénifra',
                'distance_km' => 260,
                'revenu' => 3100.00,
                'freres' => 2,
                'orphelin' => false,
                'handicap' => true, // PMR
                'regime' => 'Végétarien',
                'statut_dossier' => 'admis',
                'lit_index' => 1, // chambre PMR rez de chaussée
            ],
            [
                'name' => 'Kawtar El Fassi',
                'email' => 'kawtar@example.com',
                'cin' => 'FA128499',
                'cef' => 'CEF-2026-55198',
                'date_naissance' => '2004-03-30',
                'genre' => 'fille',
                'ville_origine' => 'Taza',
                'distance_km' => 380,
                'revenu' => 2800.00,
                'freres' => 2,
                'orphelin' => false,
                'handicap' => false,
                'regime' => 'Normal',
                'statut_dossier' => 'admis',
                'lit_index' => 17,
            ],
            [
                'name' => 'Omar Tadlaoui',
                'email' => 'omar@example.com',
                'cin' => 'BK782190',
                'cef' => 'CEF-2026-11843',
                'date_naissance' => '2005-01-19',
                'genre' => 'garcon',
                'ville_origine' => 'Beni Mellal',
                'distance_km' => 210,
                'revenu' => 3900.00,
                'freres' => 1,
                'orphelin' => false,
                'handicap' => false,
                'regime' => 'Normal',
                'statut_dossier' => 'liste_attente',
                'lit_index' => null,
            ],
            [
                'name' => 'Noura Zerouali',
                'email' => 'noura@example.com',
                'cin' => 'LC448123',
                'cef' => 'CEF-2026-66382',
                'date_naissance' => '2004-09-12',
                'genre' => 'fille',
                'ville_origine' => 'Safi',
                'distance_km' => 240,
                'revenu' => 4500.00,
                'freres' => 2,
                'orphelin' => false,
                'handicap' => false,
                'regime' => 'Sans lactose',
                'statut_dossier' => 'en_commission',
                'lit_index' => null,
            ],
            [
                'name' => 'Mehdi Souiri',
                'email' => 'mehdi@example.com',
                'cin' => 'SH884129',
                'cef' => 'CEF-2026-33918',
                'date_naissance' => '2005-07-08',
                'genre' => 'garcon',
                'ville_origine' => 'Essaouira',
                'distance_km' => 350,
                'revenu' => 2500.00,
                'freres' => 3,
                'orphelin' => false,
                'handicap' => false,
                'regime' => 'Normal',
                'statut_dossier' => 'complet',
                'lit_index' => null,
            ],
            [
                'name' => 'Ayoub Chraibi',
                'email' => 'ayoub@example.com',
                'cin' => 'BE661902',
                'cef' => 'CEF-2026-90412',
                'date_naissance' => '2004-12-05',
                'genre' => 'garcon',
                'ville_origine' => 'Casablanca',
                'distance_km' => 18,
                'revenu' => 8500.00,
                'freres' => 1,
                'orphelin' => false,
                'handicap' => false,
                'regime' => 'Normal',
                'statut_dossier' => 'refuse',
                'lit_index' => null,
            ],
        ];

        $scoringService = new SelectionScoringService;
        $adminUser = User::where('role', 'admin')->first();

        foreach ($candidatsSeed as $idx => $cData) {
            $user = User::create([
                'name' => $cData['name'],
                'email' => $cData['email'],
                'password' => Hash::make('password'),
                'role' => 'candidat',
                'telephone' => '0670'.str_pad((string) ($idx + 1000), 6, '0', STR_PAD_LEFT),
            ]);

            $profile = CandidatProfile::create([
                'user_id' => $user->id,
                'cin' => $cData['cin'],
                'is_mineur' => false,
                'cef' => $cData['cef'],
                'date_naissance' => $cData['date_naissance'],
                'genre' => $cData['genre'],
                'ville_origine' => $cData['ville_origine'],
                'adresse' => "Rue Al Amal, {$cData['ville_origine']}",
                'telephone' => $user->telephone,
                'distance_km' => $cData['distance_km'],
                'revenu_mensuel_parents' => $cData['revenu'],
                'nombre_freres_soeurs' => $cData['freres'],
                'est_orphelin' => $cData['orphelin'],
                'en_situation_handicap' => $cData['handicap'],
                'niveau_id' => $niveauxMap['TS']->id,
                'filiere_id' => $filieresList[0]->id,
                'annee_precedente_heberge' => false,
                'regime_alimentaire' => $cData['regime'],
                'contact_urgence_nom' => 'Parent '.$cData['name'],
                'contact_urgence_telephone' => '0662112233',
            ]);

            // Attribuer un lit si index défini
            $litAssigne = null;
            if ($cData['lit_index'] !== null && isset($allLitsCasa[$cData['lit_index']])) {
                $litAssigne = $allLitsCasa[$cData['lit_index']];
                $litAssigne->statut = 'occupe';
                $litAssigne->save();
            }

            $numDossier = 'DOS-2026-'.str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT);
            $dossier = Dossier::create([
                'numero_dossier' => $numDossier,
                'candidat_id' => $user->id,
                'campagne_id' => $campagne->id,
                'internat_id' => $internatCasa->id,
                'date_depot' => Carbon::now()->subDays(10 - $idx),
                'statut' => $cData['statut_dossier'],
                'motif_decision' => $cData['statut_dossier'] === 'admis' ? 'Candidat retenu en commission (Quota hébergement respecté)' : ($cData['statut_dossier'] === 'refuse' ? 'Proximité du domicile (< 30km)' : 'En attente de délibération finale'),
                'is_valide' => in_array($cData['statut_dossier'], ['admis', 'complet', 'liste_attente']),
                'valide_par_admin' => in_array($cData['statut_dossier'], ['admis', 'refuse']),
                'valide_par_user_id' => $adminUser->id,
                'date_validation' => in_array($cData['statut_dossier'], ['admis', 'refuse']) ? Carbon::now()->subDays(2) : null,
                'lit_id' => $litAssigne?->id,
                'date_arrivee_effective' => $litAssigne ? Carbon::now()->subDays(1) : null,
            ]);

            // Calcul du score
            $scoreRes = $scoringService->calculateScore($dossier);
            $dossier->score_total = $scoreRes['total'];
            $dossier->details_score = $scoreRes['details'];
            $dossier->save();

            // Créer les documents joints
            foreach ($docTypesList as $dType) {
                DossierDoc::create([
                    'dossier_id' => $dossier->id,
                    'doc_type_id' => $dType->id,
                    'file_path' => 'documents/demo_justificatif.pdf',
                    'original_name' => "{$dType->code}_{$cData['cin']}.pdf",
                    'is_valid' => true,
                    'admin_message' => 'Document conforme et lisible vérifié.',
                    'verified_by_user_id' => $adminUser->id,
                    'verified_at' => Carbon::now()->subDays(2),
                ]);
            }

            // Mouvements et présences pour les stagiaires admis avec lit
            if ($litAssigne) {
                Mouvement::create([
                    'dossier_id' => $dossier->id,
                    'internat_id' => $internatCasa->id,
                    'type' => 'arrivee',
                    'date_mouvement' => Carbon::now()->subDays(1),
                    'nouveau_lit_id' => $litAssigne->id,
                    'motif' => 'Arrivée officielle et remise des clés de chambre',
                    'enregistre_par_user_id' => $adminUser->id,
                ]);

                // Pointage présence aujourd'hui
                Presence::create([
                    'internat_id' => $internatCasa->id,
                    'dossier_id' => $dossier->id,
                    'date_presence' => Carbon::today()->toDateString(),
                    'type_controle' => 'soir',
                    'statut' => 'present',
                    'remarque' => 'Présent au contrôle nocturne',
                    'pointe_par_user_id' => $adminUser->id,
                ]);
            }
        }

        // 12. Incidents et réclamations de test
        IncidentReclamation::create([
            'internat_id' => $internatCasa->id,
            'dossier_id' => 1,
            'user_id' => User::where('email', 'yassine@example.com')->value('id'),
            'type' => 'maintenance_chambre',
            'priorite' => 'moyenne',
            'titre' => 'Fuite d’eau dans la salle d’eau CH-001',
            'description' => 'Un léger écoulement sous le lavabo de la chambre a été constaté ce matin.',
            'chambre_id' => 1,
            'statut' => 'en_cours',
            'reponse_gestionnaire' => 'Plombier mandaté pour intervention cet après-midi à 14h30.',
            'traite_par_user_id' => $adminUser->id,
        ]);

        IncidentReclamation::create([
            'internat_id' => $internatCasa->id,
            'dossier_id' => 2,
            'user_id' => User::where('email', 'salma@example.com')->value('id'),
            'type' => 'reclamation',
            'priorite' => 'haute',
            'titre' => 'Précision sur le régime sans gluten',
            'description' => 'Je souhaite m’assurer que les repas du midi prévoient bien du pain sans gluten adapté.',
            'statut' => 'traite',
            'reponse_gestionnaire' => 'Commande spéciale confirmée auprès du prestataire de restauration pour le pain sans gluten.',
            'traite_par_user_id' => $adminUser->id,
            'date_resolution' => Carbon::now(),
        ]);

        // 13. Synchronisation des Repas Restauration pour aujourd'hui
        $restaurationService = new RestaurationService;
        $restaurationService->synchroniserRepasJournee($internatCasa, Carbon::today()->toDateString(), $adminUser->id);
    }
}
