<?php

defined('MOODLE_INTERNAL') || die();

/**
 * SQL condition for regular platform users.
 */
function local_analitica_avanzada_regular_user_sql(string $alias = 'u'): string {
    return "{$alias}.deleted = 0 AND {$alias}.suspended = 0 AND {$alias}.confirmed = 1 AND {$alias}.username <> 'guest'";
}

/**
 * Course IDs excluded from individual analytics rows.
 *
 * @return int[]
 */
function local_analitica_avanzada_excluded_course_ids(): array {
    return [327]; // Support Materials.
}

/**
 * Group filters based on shared naming conventions across courses.
 *
 * @return array<string, string> [joinedname => cleanlabel]
 */
function local_analitica_avanzada_get_special_group_filters(): array {
    return [
        'alcalaespartales' => 'Alcala espartales',
        'alcalaestacion' => 'Alcala estacion',
        'alcalavenecia' => 'Alcala Venecia',
        'alcorconjuzgados' => 'Alcorcon juzgados',
        'alicanteensache' => 'Alicante ensache',
        'alicanteensanche.334' => 'Alicante ensanche',
        'almeria.28d' => 'Almeria',
        'arganda.388' => 'Arganda',
        'arroyomolinos' => 'Arroyomolinos',
        'avila.2d4' => 'Avila',
        'alcorcon.retamas' => 'Alcorcon retamas',
        'alhaurin' => 'Alhaurin',
        'alicante.pzaamerica' => 'Alicante pza america',
        'barcelona.poblenou' => 'Barcelona Poblenou',
        'barcelona.santandreu' => 'Barcelona Sant Andreu',
        'benalmadena' => 'Benalmadena',
        'burgos' => 'Burgos',
        'canaveral' => 'Canaveral',
        'carmona' => 'Carmona',
        'castilleja' => 'Castilleja',
        'cordoba.arroyodelmoro' => 'Cordoba Arroyo del Moro',
        'cordoba.starosa' => 'Cordoba Sta Rosa',
        'coslada' => 'Coslada',
        'doshermanas' => 'Dos Hermanas',
        'entrenucleos' => 'Entrenucleos',
        'espartinas' => 'Espartinas',
        'getafe.buenavista' => 'Getafe Buenavista',
        'getafe.molinos' => 'Getafe Molinos',
        'getafe.norte' => 'Getafe Norte',
        'hospitalet.collblanc' => 'Hospitalet Collblanc',
        'hospitalet.pubillacases' => 'Hospitalet Pubilla Cases',
        'laalgaba' => 'La Algaba',
        'laspalmas' => 'Las Palmas',
        'leganessolagua' => 'Leganes Solagua',
        'lospalacios' => 'Los Palacios',
        'madrid.ensanchevallecas' => 'Madrid Ensanche Vallecas',
        'madrid.losrosales' => 'Madrid Los Rosales',
        'madrid.lucero' => 'Madrid Lucero',
        'madrid.pueblonuevo' => 'Madrid Pueblo Nuevo',
        'madrid.sanblas' => 'Madrid San Blas',
        'madrid.tetuan' => 'Madrid Tetuan',
        'madridcanillejas' => 'Madrid Canillejas',
        'malaga.aveuropa' => 'Malaga Av Europa',
        'mataro' => 'Mataro',
        'mostoles.pau' => 'Mostoles Pau',
        'paiporta' => 'Paiporta',
        'palomares' => 'Palomares',
        'pilas' => 'Pilas',
        'puertodelatorre' => 'Puerto de la Torre',
        'ripollet' => 'Ripollet',
        'rivas' => 'Rivas',
        'sanmartinvega' => 'San Martin de la Vega',
        'sevilla.bermejales' => 'Sevilla Bermejales',
        'sevilla.caguila' => 'Sevilla C Aguila',
        'sevilla.centro' => 'Sevilla Centro',
        'sevilla.este' => 'Sevilla Este',
        'sevilla.este.ciencia' => 'Sevilla Este Ciencia',
        'sevilla.macarena' => 'Sevilla Macarena',
        'sevilla.remedios' => 'Sevilla Remedios',
        'sevillaparsi' => 'Sevilla Parsi',
        'tenerife' => 'Tenerife',
        'tomares' => 'Tomares',
        'torrejonparqueeuropa' => 'Torrejon Parque Europa',
        'valdemoro.hospital' => 'Valdemoro Hospital',
        'badajozvaldepasillas.245' => 'Badajoz valdepasillas',
        'badalona.17a' => 'Badalona',
        'barcelonanoubarris' => 'Barcelona Nou Barris',
        'barcelonasagradafamilia' => 'Barcelona Sagrada Familia',
        'benetusser.2ca' => 'Benetusser',
        'boadilla' => 'Boadilla',
        'business' => 'Business',
        'camas.67a' => 'Camas',
        'carabanchelbuenavista' => 'Carabanchel Buenavista',
        'carabanchelvistaalegre.38e' => 'Carabanchel Vista Alegre',
        'coriadelrio.304' => 'Coria del Rio',
        'cornella.865' => 'Cornella',
        'craiova.6ef' => 'Craiova',
        'empresas' => 'Empresas',
        'esplugues.3ad' => 'Esplugues',
        'fuenlabradaarroyo' => 'Fuenlabrada Arroyo',
        'fuenlabradaloranca' => 'Fuenlabrada Loranca',
        'fuenlabradaserna' => 'Fuenlabrada Serna',
        'fuenlabradavivero.5b' => 'Fuenlabrada Vivero',
        'getafebercial' => 'Getafe Bercial',
        'getafecentro' => 'Getafe Centro',
        'getafejuancierva' => 'Getafe Juan Cierva',
        'getafesector3' => 'Getafe Sector 3',
        'granadavergeles.7a' => 'Granada Vergeles',
        'huelvaadoratrices' => 'Huelva Adoratrices',
        'huelvacentro.1b9' => 'Huelva Centro',
        'huelvapescaderia.3a9' => 'Huelva Pescaderia',
        'humanes' => 'Humanes',
        'jerez.207' => 'Jerez',
        'leganescarrascal' => 'Leganes Carrascal',
        'leganescentro' => 'Leganes Centro',
        'leganessolagua.311' => 'Leganes Solagua',
        'madrid4caminos' => 'Madrid 4 Caminos',
        'madridalbufera' => 'Madrid Albufera',
        'madridarganzuela' => 'Madrid Arganzuela',
        'madridchaminade' => 'Madrid Chaminade',
        'madridciudadangeles' => 'Madrid Ciudad Angeles',
        'madridespinillo' => 'Madrid Espinillo',
        'madridlasrosas.22f' => 'Madrid Las Rosas',
        'madridmoncloa.118' => 'Madrid Moncloa',
        'madridmoratalaz' => 'Madrid Moratalaz',
        'madridpalomeras.f3' => 'Madrid Palomeras',
        'madridpinardelrey.211' => 'Madrid Pinar del Rey',
        'madridptevallecas.1c4' => 'Madrid Pte Vallecas',
        'madridvaldebernardo' => 'Madrid Valdebernardo',
        'majadahonda' => 'Majadahonda',
        'malagapalo.110' => 'Malaga Palo',
        'malagateatinos.311' => 'Malaga Teatinos',
        'mataroelsmolins.b' => 'Mataro Els Molins',
        'mejorada.23f' => 'Mejorada',
        'merida.192' => 'Merida',
        'montequinto.238' => 'Montequinto',
        'mostolesestoril' => 'Mostoles Estoril',
        'mostolespradillo' => 'Mostoles Pradillo',
        'mostolessoto.fd' => 'Mostoles Soto',
        'navalcarnero' => 'Navalcarnero',
        'online' => 'Online',
        'palmamallorca' => 'Palma Mallorca',
        'parlacentro' => 'Parla Centro',
        'parlasur' => 'Parla Sur',
        'pintoprado' => 'Pinto Prado',
        'pintoteneria' => 'Pinto Teneria',
        'sesena' => 'Sesena',
        'sevillabami.2e1' => 'Sevilla Bami',
        'sevillagranplaza.774' => 'Sevilla Gran Plaza',
        'sevillalasalle.34b' => 'Sevilla La Salle',
        'sevillaparquealcosa' => 'Sevilla Parque Alcosa',
        'sevillapinomontano.93' => 'Sevilla Pino Montano',
        'sevillareinamercedes.e79' => 'Sevilla Reina Mercedes',
        'sevillasanjeronimo.993' => 'Sevilla San Jeronimo',
        'sevillastajusta.ff' => 'Sevilla Sta Justa',
        'sevillatharsis.31d' => 'Sevilla Tharsis',
        'sevillatriana.23e' => 'Sevilla Triana',
        'sevillaviapol' => 'Sevilla Viapol',
        'sjaznalfarache.18b' => 'San Juan de Aznalfarache',
        'sjrinconada.11b' => 'San Jose de la Rinconada',
        'tampico.315' => 'Tampico',
        'testgroup' => 'Testgroup',
        'tetouan.65' => 'Tetouan',
        'toledo' => 'Toledo',
        'torrejon.9c' => 'Torrejon',
        'umbrete.1ed' => 'Umbrete',
        'utrera.21e' => 'Utrera',
        'valdemoro.312' => 'Valdemoro',
        'valladolid.cc' => 'Valladolid',
        'villaverdealto' => 'Villaverde Alto',
        'villaverdebajo' => 'Villaverde Bajo',
    ];
}

/**
 * Synthetic key for groups not present in the nomenclature list.
 */
function local_analitica_avanzada_get_other_groups_filter_key(): string {
    return 'otrosgrupos';
}

/**
 * Normalize a group filter key for matching.
 */
function local_analitica_avanzada_normalize_group_filter_key(string $value): string {
    return preg_replace('/[^a-z0-9]/', '', core_text::strtolower($value));
}

/**
 * SQL expression to normalize group names before matching.
 */
function local_analitica_avanzada_group_name_normalized_sql(string $field = 'g.name'): string {
    return "LOWER(REPLACE(REPLACE(REPLACE(REPLACE({$field}, '-', ''), ' ', ''), '.', ''), '_', ''))";
}

/**
 * Build SQL condition for a group filter.
 */
function local_analitica_avanzada_get_group_filter_sql_condition(string $groupfilter, string $useridfield, string $paramprefix, array &$params): ?string {
    $groupfilter = core_text::strtolower(trim($groupfilter));
    if ($groupfilter === '') {
        return null;
    }

    $specialgroups = local_analitica_avanzada_get_special_group_filters();
    if ($groupfilter === local_analitica_avanzada_get_other_groups_filter_key()) {
        $knownclauses = [];
        $knownindex = 0;
        foreach (array_keys($specialgroups) as $specialgroupkey) {
            $paramname = $paramprefix . 'known' . $knownindex++;
            $params[$paramname] = '%' . local_analitica_avanzada_normalize_group_filter_key($specialgroupkey) . '%';
            $knownclauses[] = local_analitica_avanzada_group_name_normalized_sql('g_known.name') . " LIKE :{$paramname}";
        }

        if (empty($knownclauses)) {
            return "EXISTS (SELECT 1 FROM {groups_members} gm_any WHERE gm_any.userid = {$useridfield})";
        }

        return "EXISTS (
                    SELECT 1
                      FROM {groups_members} gm_any
                     WHERE gm_any.userid = {$useridfield}
                ) AND NOT EXISTS (
                    SELECT 1
                      FROM {groups_members} gm_known
                      JOIN {groups} g_known ON g_known.id = gm_known.groupid
                     WHERE gm_known.userid = {$useridfield}
                       AND (" . implode(' OR ', $knownclauses) . ")
                )";
    }

    if (!array_key_exists($groupfilter, $specialgroups)) {
        return null;
    }

    $needleparam = $paramprefix . 'needle';
    $params[$needleparam] = '%' . local_analitica_avanzada_normalize_group_filter_key($groupfilter) . '%';

    return "EXISTS (
                SELECT 1
                  FROM {groups_members} gm
                  JOIN {groups} g ON g.id = gm.groupid
                 WHERE gm.userid = {$useridfield}
                   AND " . local_analitica_avanzada_group_name_normalized_sql('g.name') . " LIKE :{$needleparam}
            )";
}

/**
 * Access control: only managers and eczonedirectors.
 */
function local_analitica_avanzada_user_can_view(?int $userid = null): bool {
    global $USER, $DB;

    if ($userid === null) {
        $userid = $USER->id;
    }

    if (is_siteadmin($userid)) {
        return true;
    }

    if (has_capability('local/analitica_avanzada:view', context_system::instance(), $userid)) {
        return true;
    }

    $sql = "SELECT 1
              FROM {role_assignments} ra
              JOIN {context} ctx
                ON ctx.id = ra.contextid
              JOIN {role} r
                ON r.id = ra.roleid
             WHERE ra.userid = :userid
               AND (
                    r.shortname IN ('manager', 'eczonedirector')
                    OR r.archetype = 'manager'
               )";

    return $DB->record_exists_sql($sql, ['userid' => $userid]);
}

/**
 * Dashboard visibility scope for current viewer.
 * Managers and eczonedirectors see all data (unrestricted).
 */
function local_analitica_avanzada_get_dashboard_scope(?int $userid = null): array {
    global $USER, $DB;

    static $cache = [];

    if ($userid === null) {
        $userid = $USER->id;
    }

    if (isset($cache[$userid])) {
        return $cache[$userid];
    }

    $scope = [
        'restricted' => false,
        'viewerid' => $userid,
        'courseids' => [],
        'groupids' => [],
        'userids' => [],
    ];

    // Admins, users with system capability, managers, and eczonedirectors see everything.
    if (is_siteadmin($userid) || has_capability('local/analitica_avanzada:view', context_system::instance(), $userid)) {
        $cache[$userid] = $scope;
        return $scope;
    }

    $rolesql = "SELECT 1
                  FROM {role_assignments} ra
                  JOIN {role} r ON r.id = ra.roleid
                 WHERE ra.userid = :userid
                   AND (r.shortname IN ('manager', 'eczonedirector') OR r.archetype = 'manager')";

    if ($DB->record_exists_sql($rolesql, ['userid' => $userid])) {
        $cache[$userid] = $scope;
        return $scope;
    }

    // Fallback: no access.
    $scope['restricted'] = true;
    $cache[$userid] = $scope;
    return $scope;
}

/**
 * SQL condition restricting to student-role users only.
 */
function local_analitica_avanzada_student_role_sql(string $useralias = 'u'): string {
    return "EXISTS (
        SELECT 1
          FROM {role_assignments} ra_s
          JOIN {role} r_s ON r_s.id = ra_s.roleid
         WHERE ra_s.userid = {$useralias}.id
           AND (r_s.shortname = 'student' OR r_s.archetype = 'student')
    )";
}

/**
 * Apply scoped course filtering.
 */
function local_analitica_avanzada_get_scoped_course_ids(array $scope = null): array {
    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    return !empty($scope['restricted']) ? array_values(array_unique($scope['courseids'] ?? [])) : [];
}

/**
 * Apply scoped user filtering.
 */
function local_analitica_avanzada_get_scoped_user_ids(array $scope = null): array {
    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    return !empty($scope['restricted']) ? array_values(array_unique($scope['userids'] ?? [])) : [];
}

/**
 * Format percent.
 */
function local_analitica_avanzada_format_percent(?float $value, int $decimals = 1): string {
    if ($value === null) {
        return '—';
    }
    return number_format($value, $decimals, ',', '.') . '%';
}

/**
 * Format duration in a compact way.
 */
function local_analitica_avanzada_format_duration(int $seconds): string {
    if ($seconds <= 0) {
        return '0 min';
    }

    $hours = floor($seconds / HOURSECS);
    $minutes = floor(($seconds % HOURSECS) / MINSECS);
    $secs = $seconds % MINSECS;

    if ($hours > 0) {
        return $hours . ' h ' . $minutes . ' min';
    }
    if ($minutes > 0) {
        return $minutes . ' min';
    }
    return $secs . ' s';
}

/**
 * Total regular student users.
 */
function local_analitica_avanzada_get_total_regular_users(array $scope = null): int {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $userids = local_analitica_avanzada_get_scoped_user_ids($scope);

    if (!empty($scope['restricted'])) {
        if (empty($userids)) {
            return 0;
        }
        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'totalusr');
        $sql = "SELECT COUNT(1)
                  FROM {user} u
                 WHERE " . local_analitica_avanzada_regular_user_sql('u') . "
                   AND u.id {$insql}
                   AND " . local_analitica_avanzada_student_role_sql('u');
        return (int) $DB->count_records_sql($sql, $params);
    }

    $sql = "SELECT COUNT(1)
              FROM {user} u
             WHERE " . local_analitica_avanzada_regular_user_sql('u') . "
               AND " . local_analitica_avanzada_student_role_sql('u');

    return (int) $DB->count_records_sql($sql);
}

/**
 * Global course completion rate (students only).
 */
function local_analitica_avanzada_get_global_completion_rate(array $scope = null): float {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $params = [];
    $conditions = [
        'ue.status = 0',
        'e.status = 0',
        'c.enablecompletion = 1',
        local_analitica_avanzada_regular_user_sql('u'),
        local_analitica_avanzada_student_role_sql('u'),
    ];

    if (!empty($scope['restricted'])) {
        $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($userids) || empty($courseids)) {
            return 0.0;
        }

        [$userinsql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'cmpusr');
        [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cmpcrs');
        $params = $userparams + $courseparams;
        $conditions[] = "ue.userid {$userinsql}";
        $conditions[] = "e.courseid {$courseinsql}";
    }

    $wheresql = implode(' AND ', $conditions);
    $totalsql = "SELECT COUNT(1)
                   FROM (
                        SELECT DISTINCT ue.userid, e.courseid
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON e.id = ue.enrolid
                          JOIN {course} c ON c.id = e.courseid
                          JOIN {user} u ON u.id = ue.userid
                         WHERE {$wheresql}
                   ) pairs";

    $completedsql = "SELECT COUNT(1)
                       FROM (
                            SELECT DISTINCT ue.userid, e.courseid
                              FROM {user_enrolments} ue
                              JOIN {enrol} e ON e.id = ue.enrolid
                              JOIN {course} c ON c.id = e.courseid
                              JOIN {user} u ON u.id = ue.userid
                              JOIN {course_completions} cc
                                ON cc.userid = ue.userid
                               AND cc.course = e.courseid
                               AND cc.timecompleted IS NOT NULL
                             WHERE {$wheresql}
                       ) completedpairs";

    $total = (int) $DB->count_records_sql($totalsql, $params);
    if ($total === 0) {
        return 0.0;
    }

    $completed = (int) $DB->count_records_sql($completedsql, $params);
    return ($completed / $total) * 100;
}

/**
 * Average session time for all regular student users, estimated from the last N days.
 */
function local_analitica_avanzada_get_global_average_session_time(int $since, array $scope = null): int {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $params = ['since' => $since];
    $conditions = [
        'l.anonymous = 0',
        'l.userid > 0',
        'l.timecreated >= :since',
        local_analitica_avanzada_regular_user_sql('u'),
        local_analitica_avanzada_student_role_sql('u'),
    ];

    if (!empty($scope['restricted'])) {
        $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
        if (empty($userids)) {
            return 0;
        }
        [$insql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'sessusr');
        $params += $userparams;
        $conditions[] = "l.userid {$insql}";
    }

    $sql = "SELECT l.userid, l.timecreated
              FROM {logstore_standard_log} l
              JOIN {user} u ON u.id = l.userid
             WHERE " . implode(' AND ', $conditions) . "
          ORDER BY l.userid ASC, l.timecreated ASC";

    $rs = $DB->get_recordset_sql($sql, $params);

    $gap = 30 * MINSECS;
    $lastuserid = null;
    $previoustime = null;
    $totaltime = 0;
    $totalsessions = 0;

    foreach ($rs as $row) {
        if ($lastuserid !== (int) $row->userid) {
            $lastuserid = (int) $row->userid;
            $previoustime = null;
        }

        if ($previoustime === null || (($row->timecreated - $previoustime) > $gap)) {
            $totalsessions++;
        } else {
            $totaltime += ($row->timecreated - $previoustime);
        }

        $previoustime = (int) $row->timecreated;
    }

    $rs->close();

    if ($totalsessions === 0) {
        return 0;
    }

    return (int) round($totaltime / $totalsessions);
}

/**
 * Global dashboard metrics (students only).
 */
function local_analitica_avanzada_get_global_metrics(array $scope = null): array {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
    $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);

    if (!empty($scope['restricted']) && (empty($userids) || empty($courseids))) {
        return [
            'totalusers' => 0,
            'inactivecount' => 0,
            'inactivepct' => 0,
            'lowgradecount' => 0,
            'lowgradepct' => 0,
            'completionrate' => 0,
            'avgsession' => 0,
        ];
    }

    $totalusers = local_analitica_avanzada_get_total_regular_users($scope);
    $inactivecutoff = time() - (7 * DAYSECS);

    $inactiveparams = ['inactivecutoff' => $inactivecutoff];
    $inactiveconditions = [
        local_analitica_avanzada_regular_user_sql('u'),
        local_analitica_avanzada_student_role_sql('u'),
        '(u.lastaccess = 0 OR u.lastaccess < :inactivecutoff)',
    ];

    if (!empty($scope['restricted'])) {
        [$inactiveinsql, $inactiveuserparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'inactiveusr');
        $inactiveparams += $inactiveuserparams;
        $inactiveconditions[] = "u.id {$inactiveinsql}";
    }

    $inactivesql = "SELECT COUNT(1)
                      FROM {user} u
                     WHERE " . implode(' AND ', $inactiveconditions);
    $inactivecount = (int) $DB->count_records_sql($inactivesql, $inactiveparams);

    $lowgradeparams = ['lowgrade' => 0.5];
    $lowgradeconditions = [
        "gi.itemtype = 'course'",
        'gg.finalgrade IS NOT NULL',
        local_analitica_avanzada_regular_user_sql('u'),
        local_analitica_avanzada_student_role_sql('u'),
    ];

    if (!empty($scope['restricted'])) {
        [$userinsql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'lowusr');
        [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'lowcrs');
        $lowgradeparams += $userparams + $courseparams;
        $lowgradeconditions[] = "gg.userid {$userinsql}";
        $lowgradeconditions[] = "gi.courseid {$courseinsql}";
    }

    $lowgradesql = "SELECT COUNT(1)
                      FROM (
                           SELECT gg.userid,
                                  AVG(
                                      CASE
                                          WHEN (gi.grademax - gi.grademin) > 0
                                          THEN (gg.finalgrade - gi.grademin) / (gi.grademax - gi.grademin)
                                          ELSE NULL
                                      END
                                  ) AS avggrade
                             FROM {grade_grades} gg
                             JOIN {grade_items} gi ON gi.id = gg.itemid
                             JOIN {user} u ON u.id = gg.userid
                            WHERE " . implode(' AND ', $lowgradeconditions) . "
                         GROUP BY gg.userid
                      ) gradeavg
                     WHERE gradeavg.avggrade < :lowgrade";

    $lowgradecount = (int) $DB->count_records_sql($lowgradesql, $lowgradeparams);
    $completionrate = local_analitica_avanzada_get_global_completion_rate($scope);
    $avgsession = local_analitica_avanzada_get_global_average_session_time(time() - (30 * DAYSECS), $scope);

    return [
        'totalusers' => $totalusers,
        'inactivecount' => $inactivecount,
        'inactivepct' => $totalusers > 0 ? ($inactivecount / $totalusers) * 100 : 0,
        'lowgradecount' => $lowgradecount,
        'lowgradepct' => $totalusers > 0 ? ($lowgradecount / $totalusers) * 100 : 0,
        'completionrate' => $completionrate,
        'avgsession' => $avgsession,
    ];
}

/**
 * Courses for the filter dropdown.
 */
function local_analitica_avanzada_get_courses_for_filter(array $scope = null): array {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $params = ['siteid' => SITEID];
    $conditions = ['c.id <> :siteid'];
    $excludedcourseids = local_analitica_avanzada_excluded_course_ids();
    if (!empty($excludedcourseids)) {
        [$excludedinsql, $excludedparams] = $DB->get_in_or_equal($excludedcourseids, SQL_PARAMS_NAMED, 'filterexcluded', false);
        $params += $excludedparams;
        $conditions[] = "c.id {$excludedinsql}";
    }

    if (!empty($scope['restricted'])) {
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($courseids)) {
            return [];
        }

        [$insql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'filtercrs');
        $params += $courseparams;
        $conditions[] = "c.id {$insql}";
    }

    $sql = "SELECT c.id, c.fullname
              FROM {course} c
             WHERE " . implode(' AND ', $conditions) . "
          ORDER BY c.fullname ASC";

    return $DB->get_records_sql_menu($sql, $params);
}

/**
 * Average grades for selected users.
 */
function local_analitica_avanzada_get_user_average_grades(array $userids, array $scope = null, int $courseid = 0): array {
    global $DB;

    if (empty($userids)) {
        return [];
    }

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $courseids = [];
    if (!empty($scope['restricted'])) {
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($courseids)) {
            return [];
        }
    }

    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uag');
    $conditions = [
        "gg.userid {$insql}",
        "gi.itemtype = 'course'",
        'gg.finalgrade IS NOT NULL',
    ];

    if (!empty($courseid)) {
        $conditions[] = 'gi.courseid = :gradecourseid';
        $params['gradecourseid'] = $courseid;
    } else if (!empty($courseids)) {
        [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'uagcrs');
        $params += $courseparams;
        $conditions[] = "gi.courseid {$courseinsql}";
    }

    $sql = "SELECT gg.userid,
                   AVG(
                       CASE
                           WHEN (gi.grademax - gi.grademin) > 0
                           THEN (gg.finalgrade - gi.grademin) / (gi.grademax - gi.grademin)
                           ELSE NULL
                       END
                   ) AS avggrade
              FROM {grade_grades} gg
              JOIN {grade_items} gi ON gi.id = gg.itemid
             WHERE " . implode(' AND ', $conditions) . "
          GROUP BY gg.userid";

    $records = $DB->get_records_sql($sql, $params);
    $result = [];

    foreach ($records as $record) {
        $result[(int) $record->userid] = $record->avggrade !== null ? ((float) $record->avggrade * 100) : null;
    }

    return $result;
}

/**
 * Average grade per user/course pair.
 *
 * @param array<int, array{userid:int, courseid:int}> $pairs
 * @return array<string, float|null> key "userid:courseid"
 */
function local_analitica_avanzada_get_user_course_average_grades(array $pairs): array {
    global $DB;

    if (empty($pairs)) {
        return [];
    }

    $orconditions = [];
    $params = [];
    foreach (array_values($pairs) as $index => $pair) {
        $useridkey = 'ucaguserid' . $index;
        $courseidkey = 'ucagcourseid' . $index;
        $params[$useridkey] = (int) $pair['userid'];
        $params[$courseidkey] = (int) $pair['courseid'];
        $orconditions[] = "(gg.userid = :{$useridkey} AND gi.courseid = :{$courseidkey})";
    }

    $sql = "SELECT gg.userid,
                   gi.courseid,
                   AVG(
                       CASE
                           WHEN (gi.grademax - gi.grademin) > 0
                           THEN (gg.finalgrade - gi.grademin) / (gi.grademax - gi.grademin)
                           ELSE NULL
                       END
                   ) AS avggrade
              FROM {grade_grades} gg
              JOIN {grade_items} gi ON gi.id = gg.itemid
             WHERE gi.itemtype = 'course'
               AND gg.finalgrade IS NOT NULL
               AND (" . implode(' OR ', $orconditions) . ")
          GROUP BY gg.userid, gi.courseid";

    $records = $DB->get_records_sql($sql, $params);
    $result = [];
    foreach ($records as $record) {
        $key = (int) $record->userid . ':' . (int) $record->courseid;
        $result[$key] = $record->avggrade !== null ? ((float) $record->avggrade * 100) : null;
    }

    return $result;
}

/**
 * Progress percentage for selected users, based on completion-tracked activities.
 */
function local_analitica_avanzada_get_user_progress(array $userids, int $courseid = 0, array $scope = null): array {
    global $DB;

    if (empty($userids)) {
        return [];
    }

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'upg');

    $conditions = [
        'ue.status = 0',
        'e.status = 0',
        "ue.userid {$insql}",
    ];

    if (!empty($courseid)) {
        $conditions[] = 'e.courseid = :progresscourseid';
        $params['progresscourseid'] = $courseid;
    } else if (!empty($scope['restricted'])) {
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($courseids)) {
            return [];
        }

        [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'upgcrs');
        $params += $courseparams;
        $conditions[] = "e.courseid {$courseinsql}";
    }

    $sql = "SELECT uc.userid,
                   COUNT(cm.id) AS totalmodules,
                   SUM(CASE WHEN cmc.completionstate > 0 THEN 1 ELSE 0 END) AS completedmodules
              FROM (
                    SELECT DISTINCT ue.userid, e.courseid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                     WHERE " . implode(' AND ', $conditions) . "
              ) uc
              JOIN {course_modules} cm
                ON cm.course = uc.courseid
               AND cm.completion > 0
               AND cm.deletioninprogress = 0
         LEFT JOIN {course_modules_completion} cmc
                ON cmc.coursemoduleid = cm.id
               AND cmc.userid = uc.userid
          GROUP BY uc.userid";

    $records = $DB->get_records_sql($sql, $params);
    $result = [];

    foreach ($records as $record) {
        if ((int) $record->totalmodules > 0) {
            $result[(int) $record->userid] = ((int) $record->completedmodules / (int) $record->totalmodules) * 100;
        } else {
            $result[(int) $record->userid] = null;
        }
    }

    return $result;
}

/**
 * Progress percentage per user/course pair.
 *
 * @param array<int, array{userid:int, courseid:int}> $pairs
 * @return array<string, float|null> key "userid:courseid"
 */
function local_analitica_avanzada_get_user_course_progress(array $pairs): array {
    global $DB;

    if (empty($pairs)) {
        return [];
    }

    $orconditions = [];
    $params = [];
    foreach (array_values($pairs) as $index => $pair) {
        $useridkey = 'ucpruserid' . $index;
        $courseidkey = 'ucprcourseid' . $index;
        $params[$useridkey] = (int) $pair['userid'];
        $params[$courseidkey] = (int) $pair['courseid'];
        $orconditions[] = "(uc.userid = :{$useridkey} AND uc.courseid = :{$courseidkey})";
    }

    $sql = "SELECT uc.userid,
                   uc.courseid,
                   COUNT(cm.id) AS totalmodules,
                   SUM(CASE WHEN cmc.completionstate > 0 THEN 1 ELSE 0 END) AS completedmodules
              FROM (
                    SELECT DISTINCT ue.userid, e.courseid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                     WHERE ue.status = 0
                       AND e.status = 0
              ) uc
              JOIN {course_modules} cm
                ON cm.course = uc.courseid
               AND cm.completion > 0
               AND cm.deletioninprogress = 0
         LEFT JOIN {course_modules_completion} cmc
                ON cmc.coursemoduleid = cm.id
               AND cmc.userid = uc.userid
             WHERE " . implode(' OR ', $orconditions) . "
          GROUP BY uc.userid, uc.courseid";

    $records = $DB->get_records_sql($sql, $params);
    $result = [];
    foreach ($records as $record) {
        $key = (int) $record->userid . ':' . (int) $record->courseid;
        if ((int) $record->totalmodules > 0) {
            $result[$key] = ((int) $record->completedmodules / (int) $record->totalmodules) * 100;
        } else {
            $result[$key] = null;
        }
    }

    return $result;
}

/**
 * Courses enrolled by selected users.
 */
function local_analitica_avanzada_get_user_courses(array $userids, array $scope = null, int $courseid = 0): array {
    global $DB;

    if (empty($userids)) {
        return [];
    }

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'ucrs');
    $conditions = [
        'ue.status = 0',
        'e.status = 0',
        "ue.userid {$insql}",
    ];

    if (!empty($courseid)) {
        $conditions[] = 'e.courseid = :ucrscourseid';
        $params['ucrscourseid'] = $courseid;
    }

    if (!empty($scope['restricted'])) {
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($courseids)) {
            return [];
        }

        [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'ucrscrs');
        $params += $courseparams;
        $conditions[] = "e.courseid {$courseinsql}";
    }

    $sql = "SELECT DISTINCT ue.userid, c.id, c.fullname
              FROM {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid
              JOIN {course} c ON c.id = e.courseid
             WHERE " . implode(' AND ', $conditions) . "
          ORDER BY c.fullname ASC";

    $records = $DB->get_records_sql($sql, $params);
    $result = [];

    foreach ($records as $record) {
        $userid = (int) $record->userid;
        if (!isset($result[$userid])) {
            $result[$userid] = [];
        }
        $result[$userid][] = [
            'id' => (int) $record->id,
            'fullname' => $record->fullname,
        ];
    }

    return $result;
}

/**
 * Estimated average session time by user in the last N days.
 */
function local_analitica_avanzada_get_user_session_times(array $userids, int $since): array {
    global $DB;

    if (empty($userids)) {
        return [];
    }

    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'ust');
    $params['sincesession'] = $since;

    $sql = "SELECT l.userid, l.timecreated
              FROM {logstore_standard_log} l
             WHERE l.userid {$insql}
               AND l.anonymous = 0
               AND l.timecreated >= :sincesession
          ORDER BY l.userid ASC, l.timecreated ASC";

    $rs = $DB->get_recordset_sql($sql, $params);

    $gap = 30 * MINSECS;
    $result = [];

    $currentuserid = null;
    $previoustime = null;
    $totaltime = 0;
    $sessioncount = 0;

    foreach ($rs as $row) {
        $userid = (int) $row->userid;
        $timecreated = (int) $row->timecreated;

        if ($currentuserid !== $userid) {
            if ($currentuserid !== null) {
                $result[$currentuserid] = $sessioncount > 0 ? (int) round($totaltime / $sessioncount) : 0;
            }

            $currentuserid = $userid;
            $previoustime = null;
            $totaltime = 0;
            $sessioncount = 0;
        }

        if ($previoustime === null || (($timecreated - $previoustime) > $gap)) {
            $sessioncount++;
        } else {
            $totaltime += ($timecreated - $previoustime);
        }

        $previoustime = $timecreated;
    }

    if ($currentuserid !== null) {
        $result[$currentuserid] = $sessioncount > 0 ? (int) round($totaltime / $sessioncount) : 0;
    }

    $rs->close();

    return $result;
}

/**
 * Get enrolment timestart/timeend for users (per user, taking first active enrolment found).
 * Returns [userid => ['timestart' => int, 'timeend' => int]]
 */
function local_analitica_avanzada_get_user_enrolment_dates(array $userids, int $courseid = 0, array $scope = null): array {
    global $DB;

    if (empty($userids)) {
        return [];
    }

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'ued');
    $conditions = [
        'ue.status = 0',
        'e.status = 0',
        "ue.userid {$insql}",
    ];

    if (!empty($courseid)) {
        $conditions[] = 'e.courseid = :uedcourseid';
        $params['uedcourseid'] = $courseid;
    } else if (!empty($scope['restricted'])) {
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($courseids)) {
            return [];
        }
        [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'uedcrs');
        $params += $courseparams;
        $conditions[] = "e.courseid {$courseinsql}";
    }

    $now = time();
    $params['uednow1'] = $now;
    $params['uednow2'] = $now;
    $params['uednow3'] = $now;
    $params['uednow4'] = $now;
    $params['uednow5'] = $now;
    $params['uednow6'] = $now;
    $params['uednow7'] = $now;
    $params['uednow8'] = $now;

    // Aggregate enrolments per user and compute a deterministic status.
    $sql = "SELECT ue.userid,
                   MIN(ue.timestart) AS timestart,
                   CASE
                       WHEN MAX(
                           CASE
                               WHEN (ue.timestart = 0 OR ue.timestart <= :uednow1)
                                AND (ue.timeend = 0 OR ue.timeend >= :uednow2)
                               THEN 1 ELSE 0
                           END
                       ) = 1
                       THEN MAX(
                           CASE
                               WHEN (ue.timestart = 0 OR ue.timestart <= :uednow3) AND ue.timeend > 0
                               THEN ue.timeend ELSE 0
                           END
                       )
                       ELSE MAX(
                           CASE
                               WHEN ue.timeend > 0 AND ue.timeend < :uednow4
                               THEN ue.timeend ELSE 0
                           END
                       )
                   END AS timeend,
                   CASE
                       WHEN MAX(
                           CASE
                               WHEN (ue.timestart = 0 OR ue.timestart <= :uednow5)
                                AND (ue.timeend = 0 OR ue.timeend >= :uednow6)
                               THEN 1 ELSE 0
                           END
                       ) = 1
                       THEN 'active'
                       WHEN MAX(CASE WHEN ue.timestart > :uednow7 THEN 1 ELSE 0 END) = 1
                       THEN 'pending'
                       WHEN MAX(CASE WHEN ue.timeend > 0 AND ue.timeend < :uednow8 THEN 1 ELSE 0 END) = 1
                       THEN 'finished'
                       ELSE 'active'
                   END AS enrolstatus
              FROM {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid
             WHERE " . implode(' AND ', $conditions) . "
          GROUP BY ue.userid";

    $records = $DB->get_records_sql($sql, $params);
    $result = [];

    foreach ($records as $record) {
        $result[(int) $record->userid] = [
            'timestart' => (int) $record->timestart,
            'timeend' => (int) $record->timeend,
            'enrolstatus' => (string) $record->enrolstatus,
        ];
    }

    return $result;
}

/**
 * Compute enrolment status label from timestart/timeend.
 * Returns 'pending', 'active' or 'finished'.
 */
function local_analitica_avanzada_get_enrolment_status(int $timestart, int $timeend): string {
    $now = time();

    if ($timestart > 0 && $now < $timestart) {
        return 'pending';
    }

    if ($timeend > 0 && $now > $timeend) {
        return 'finished';
    }

    return 'active';
}

/**
 * Render a status badge.
 */
function local_analitica_avanzada_render_status_badge(string $status): string {
    $labels = [
        'pending' => 'Pendiente',
        'active' => 'Activo',
        'finished' => 'Finalizado',
    ];
    $classes = [
        'pending' => 'aa-pill aa-pill-warning',
        'active' => 'aa-pill aa-pill-success',
        'finished' => 'aa-pill aa-pill-muted',
    ];

    $label = $labels[$status] ?? $status;
    $class = $classes[$status] ?? 'aa-pill';

    return html_writer::tag('span', $label, ['class' => $class]);
}

/**
 * Filtered and enriched users (students only).
 */
function local_analitica_avanzada_get_filtered_users(array $filters, int $page = 0, int $perpage = 25, array $scope = null): array {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $params = [];
    $joins = [];
    $where = [
        local_analitica_avanzada_regular_user_sql('u'),
        local_analitica_avanzada_student_role_sql('u'),
    ];

    $excludedcourseids = local_analitica_avanzada_excluded_course_ids();
    $selectedcourseid = (int) ($filters['courseid'] ?? 0);

    if (!empty($selectedcourseid) && in_array($selectedcourseid, $excludedcourseids, true)) {
        return ['total' => 0, 'users' => []];
    }

    if (!empty($scope['restricted'])) {
        $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($userids) || empty($courseids)) {
            return ['total' => 0, 'users' => []];
        }

        [$userinsql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'scopeusr');
        $params += $userparams;
        $where[] = "u.id {$userinsql}";

        if (!empty($selectedcourseid) && !in_array($selectedcourseid, $courseids, true)) {
            return ['total' => 0, 'users' => []];
        }
    }

    $enrolconditions = [
        'ue.status = 0',
        'e.status = 0',
    ];

    if (!empty($selectedcourseid)) {
        $enrolconditions[] = 'e.courseid = :filtercourseid';
        $params['filtercourseid'] = $selectedcourseid;
    } else if (!empty($scope['restricted'])) {
        [$courseinsql, $courseparams] = $DB->get_in_or_equal(local_analitica_avanzada_get_scoped_course_ids($scope), SQL_PARAMS_NAMED, 'ecscopecrs');
        $params += $courseparams;
        $enrolconditions[] = "e.courseid {$courseinsql}";
    }

    if (!empty($excludedcourseids)) {
        [$excludedinsql, $excludedparams] = $DB->get_in_or_equal($excludedcourseids, SQL_PARAMS_NAMED, 'ecexcluded', false);
        $params += $excludedparams;
        $enrolconditions[] = "e.courseid {$excludedinsql}";
    }

    $now = time();
    $params['ecnow1'] = $now;
    $params['ecnow2'] = $now;
    $params['ecnow3'] = $now;
    $params['ecnow4'] = $now;
    $params['ecnow5'] = $now;
    $params['ecnow6'] = $now;
    $params['ecnow7'] = $now;
    $params['ecnow8'] = $now;

    $joins[] = "JOIN (
                    SELECT ue.userid,
                           e.courseid,
                           c.fullname AS coursefullname,
                           MIN(ue.timestart) AS timestart,
                           CASE
                               WHEN MAX(
                                   CASE
                                       WHEN (ue.timestart = 0 OR ue.timestart <= :ecnow1)
                                        AND (ue.timeend = 0 OR ue.timeend >= :ecnow2)
                                       THEN 1 ELSE 0
                                   END
                               ) = 1
                               THEN MAX(
                                   CASE
                                       WHEN (ue.timestart = 0 OR ue.timestart <= :ecnow3) AND ue.timeend > 0
                                       THEN ue.timeend ELSE 0
                                   END
                               )
                               ELSE MAX(
                                   CASE
                                       WHEN ue.timeend > 0 AND ue.timeend < :ecnow4
                                       THEN ue.timeend ELSE 0
                                   END
                               )
                           END AS timeend,
                           CASE
                               WHEN MAX(
                                   CASE
                                       WHEN (ue.timestart = 0 OR ue.timestart <= :ecnow5)
                                        AND (ue.timeend = 0 OR ue.timeend >= :ecnow6)
                                       THEN 1 ELSE 0
                                   END
                               ) = 1 THEN 'active'
                               WHEN MAX(CASE WHEN ue.timestart > :ecnow7 THEN 1 ELSE 0 END) = 1 THEN 'pending'
                               WHEN MAX(CASE WHEN ue.timeend > 0 AND ue.timeend < :ecnow8 THEN 1 ELSE 0 END) = 1 THEN 'finished'
                               ELSE 'active'
                           END AS enrolstatus
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                      JOIN {course} c ON c.id = e.courseid
                     WHERE " . implode(' AND ', $enrolconditions) . "
                  GROUP BY ue.userid, e.courseid, c.fullname
                ) ec ON ec.userid = u.id";

    $groupfilter = (string) ($filters['groupfilter'] ?? '');
    $groupfiltercondition = local_analitica_avanzada_get_group_filter_sql_condition($groupfilter, 'u.id', 'usrgroupfilter', $params);
    if ($groupfiltercondition !== null) {
        $where[] = $groupfiltercondition;
    }

    if (!empty($filters['inactiveonly'])) {
        $where[] = '(u.lastaccess = 0 OR u.lastaccess < :inactivecutoff)';
        $params['inactivecutoff'] = time() - (7 * DAYSECS);
    }

    if (!empty($filters['lowgradeonly'])) {
        $joins[] = "JOIN (
                        SELECT gg.userid,
                               gi.courseid,
                               AVG(
                                   CASE
                                       WHEN (gi.grademax - gi.grademin) > 0
                                       THEN (gg.finalgrade - gi.grademin) / (gi.grademax - gi.grademin)
                                       ELSE NULL
                                   END
                               ) AS avggrade
                          FROM {grade_grades} gg
                          JOIN {grade_items} gi ON gi.id = gg.itemid
                          JOIN {user} ug ON ug.id = gg.userid
                         WHERE gi.itemtype = 'course'
                           AND gg.finalgrade IS NOT NULL
                           AND " . local_analitica_avanzada_regular_user_sql('ug');

        if (!empty($scope['restricted']) && empty($selectedcourseid)) {
            [$gradecourseinsql, $gradecourseparams] = $DB->get_in_or_equal(local_analitica_avanzada_get_scoped_course_ids($scope), SQL_PARAMS_NAMED, 'lgcrs');
            $params += $gradecourseparams;
            $joins[count($joins) - 1] .= "
                           AND gi.courseid {$gradecourseinsql}";
        }

        if (!empty($excludedcourseids)) {
            [$excludedgradesql, $excludedgradeparams] = $DB->get_in_or_equal($excludedcourseids, SQL_PARAMS_NAMED, 'lgexcluded', false);
            $params += $excludedgradeparams;
            $joins[count($joins) - 1] .= "
                           AND gi.courseid {$excludedgradesql}";
        }

        $joins[count($joins) - 1] .= "
                      GROUP BY gg.userid, gi.courseid
                    ) lg ON lg.userid = u.id AND lg.courseid = ec.courseid";
        $where[] = 'lg.avggrade < :lowgrademax';
        $params['lowgrademax'] = 0.5;
    }

    $statusfilter = $filters['status'] ?? '';
    if (!empty($statusfilter) && in_array($statusfilter, ['pending', 'active', 'finished'], true)) {
        $where[] = 'ec.enrolstatus = :statusfiltervalue';
        $params['statusfiltervalue'] = $statusfilter;
    }

    if (!empty($filters['search'])) {
        $search = trim($filters['search']);
        $searchparam = '%' . $DB->sql_like_escape($search) . '%';
        $fullname = $DB->sql_fullname('u.firstname', 'u.lastname');

        $where[] = '('
            . $DB->sql_like($fullname, ':searchfull', false, false)
            . ' OR ' . $DB->sql_like('u.firstname', ':searchfirstname', false, false)
            . ' OR ' . $DB->sql_like('u.lastname', ':searchlastname', false, false)
            . ' OR ' . $DB->sql_like('u.email', ':searchemail', false, false)
            . ' OR ' . $DB->sql_like('u.username', ':searchusername', false, false)
            . ')';

        $params['searchfull'] = $searchparam;
        $params['searchfirstname'] = $searchparam;
        $params['searchlastname'] = $searchparam;
        $params['searchemail'] = $searchparam;
        $params['searchusername'] = $searchparam;
    }

    $fromsql = '{user} u ' . implode(' ', $joins);
    $wheresql = implode(' AND ', $where);

    $countsql = "SELECT COUNT(1)
                   FROM {$fromsql}
                  WHERE {$wheresql}";
    $total = (int) $DB->count_records_sql($countsql, $params);

    $sql = "SELECT " . $DB->sql_concat('u.id', "'-'", 'ec.courseid') . " AS rowid,
                   u.id, u.firstname, u.lastname, u.username, u.email, u.lastaccess,
                   ec.courseid, ec.coursefullname, ec.timestart, ec.timeend, ec.enrolstatus
              FROM {$fromsql}
             WHERE {$wheresql}
          ORDER BY u.lastname ASC, u.firstname ASC, ec.coursefullname ASC";

    $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
    $users = array_values($records);
    $pairs = [];
    foreach ($users as $user) {
        $pairs[] = [
            'userid' => (int) $user->id,
            'courseid' => (int) $user->courseid,
        ];
    }

    $userids = array_values(array_unique(array_map(static function($pair) {
        return $pair['userid'];
    }, $pairs)));

    $grades = local_analitica_avanzada_get_user_course_average_grades($pairs);
    $progress = local_analitica_avanzada_get_user_course_progress($pairs);
    $sessions = local_analitica_avanzada_get_user_session_times($userids, time() - (30 * DAYSECS));

    foreach ($users as $user) {
        $userid = (int) $user->id;
        $courseid = (int) $user->courseid;
        $pairkey = $userid . ':' . $courseid;

        $user->avggrade = $grades[$pairkey] ?? null;
        $user->progress = $progress[$pairkey] ?? null;
        $user->avgsession = $sessions[$userid] ?? 0;
    }

    return [
        'total' => $total,
        'users' => $users,
    ];
}

/**
 * Retrieve ALL filtered users (no pagination) for export.
 */
function local_analitica_avanzada_get_all_filtered_users(array $filters, array $scope = null): array {
    $data = local_analitica_avanzada_get_filtered_users($filters, 0, 999999, $scope);
    return $data['users'];
}

/**
 * Render course badges.
 */
function local_analitica_avanzada_render_course_badges(array $courses): string {
    if (empty($courses)) {
        return '<span class="aa-muted">—</span>';
    }

    $output = [];
    foreach ($courses as $course) {
        $output[] = html_writer::tag('span', format_string($course['fullname']), ['class' => 'aa-badge']);
    }

    return implode('', $output);
}

/**
 * Most visited activities/resources from the last 30 days (students only).
 */
function local_analitica_avanzada_get_top_resources(int $limit = 20, int $courseid = 0, array $scope = null, string $moduletype = '', string $groupfilter = ''): array {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $monthstart = time() - (30 * DAYSECS);
    $weekstart = time() - WEEKSECS;
    $daystart = time() - DAYSECS;

    $params = [
        'ctxmodule' => CONTEXT_MODULE,
        'monthstart' => $monthstart,
    ];
    $conditions = [
        'l.contextlevel = :ctxmodule',
        'l.timecreated >= :monthstart',
        'l.anonymous = 0',
        'l.userid > 0',
        "l.crud = 'r'",
        local_analitica_avanzada_student_role_sql_log(),
    ];

    if (!empty($scope['restricted'])) {
        $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($userids) || empty($courseids)) {
            return [];
        }

        [$userinsql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'resusr');
        $params += $userparams;
        $conditions[] = "l.userid {$userinsql}";

        if (!empty($courseid)) {
            if (!in_array($courseid, $courseids, true)) {
                return [];
            }
            $conditions[] = 'cm.course = :resourcescourseid';
            $params['resourcescourseid'] = $courseid;
        } else {
            [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'rescrs');
            $params += $courseparams;
            $conditions[] = "cm.course {$courseinsql}";
        }
    } else if (!empty($courseid)) {
        $conditions[] = 'cm.course = :resourcescourseid';
        $params['resourcescourseid'] = $courseid;
    }

    // Module type filter.
    if (!empty($moduletype)) {
        $conditions[] = 'mo.name = :moduletypefilter';
        $params['moduletypefilter'] = $moduletype;
    }

    $groupcondition = local_analitica_avanzada_get_group_filter_sql_condition($groupfilter, 'l.userid', 'resgroupfilter', $params);
    if ($groupcondition !== null) {
        $conditions[] = $groupcondition;
    }

    $params += [
        'resdaystart1' => $daystart,
        'resdaystart2' => $daystart,
        'resweekstart1' => $weekstart,
        'resweekstart2' => $weekstart,
        'resmonthstart1' => $monthstart,
        'resmonthstart2' => $monthstart,
    ];

    $topsql = "SELECT l.contextinstanceid AS cmid,
                      SUM(CASE WHEN l.timecreated >= :resdaystart1 THEN 1 ELSE 0 END) AS dayviews,
                      COUNT(DISTINCT CASE WHEN l.timecreated >= :resdaystart2 THEN l.userid ELSE NULL END) AS dayusers,
                      SUM(CASE WHEN l.timecreated >= :resweekstart1 THEN 1 ELSE 0 END) AS weekviews,
                      COUNT(DISTINCT CASE WHEN l.timecreated >= :resweekstart2 THEN l.userid ELSE NULL END) AS weekusers,
                      SUM(CASE WHEN l.timecreated >= :resmonthstart1 THEN 1 ELSE 0 END) AS monthviews,
                      COUNT(DISTINCT CASE WHEN l.timecreated >= :resmonthstart2 THEN l.userid ELSE NULL END) AS monthusers
                 FROM {logstore_standard_log} l
                 JOIN {course_modules} cm ON cm.id = l.contextinstanceid
                 JOIN {modules} mo ON mo.id = cm.module
                 JOIN {user} u_res ON u_res.id = l.userid
                WHERE " . implode(' AND ', $conditions) . "
             GROUP BY l.contextinstanceid
             ORDER BY monthviews DESC";

    $toprecords = array_values($DB->get_records_sql($topsql, $params, 0, $limit));
    if (empty($toprecords)) {
        return [];
    }

    $totalmonthviews = 0;
    $cmids = [];
    foreach ($toprecords as $record) {
        $totalmonthviews += (int) $record->monthviews;
        $cmids[] = (int) $record->cmid;
    }

    $cms = $DB->get_records_list('course_modules', 'id', $cmids, '', 'id,course,module');

    $moduleids = [];
    $courseids_res = [];
    foreach ($cms as $cm) {
        $moduleids[] = (int) $cm->module;
        $courseids_res[] = (int) $cm->course;
    }

    $moduleids = array_unique($moduleids);
    $courseids_res = array_unique($courseids_res);

    $modules = !empty($moduleids) ? $DB->get_records_list('modules', 'id', $moduleids, '', 'id,name') : [];
    $courses_res = !empty($courseids_res) ? $DB->get_records_list('course', 'id', $courseids_res, '', 'id,fullname') : [];

    $modinfocache = [];
    $results = [];

    foreach ($toprecords as $record) {
        $cmid = (int) $record->cmid;
        if (empty($cms[$cmid])) {
            continue;
        }

        $cm = $cms[$cmid];
        $courseidcurrent = (int) $cm->course;

        if (!isset($modinfocache[$courseidcurrent])) {
            $modinfocache[$courseidcurrent] = get_fast_modinfo($courseidcurrent);
        }

        $modinfo = $modinfocache[$courseidcurrent];
        $cmname = 'CM ' . $cmid;

        if (isset($modinfo->cms[$cmid])) {
            $cmname = $modinfo->cms[$cmid]->name;
        }

        $results[] = [
            'name' => format_string($cmname),
            'course' => isset($courses_res[$courseidcurrent]) ? format_string($courses_res[$courseidcurrent]->fullname) : '—',
            'type' => $modules[$cm->module]->name ?? 'activity',
            'dayviews' => (int) $record->dayviews,
            'dayusers' => (int) $record->dayusers,
            'weekviews' => (int) $record->weekviews,
            'weekusers' => (int) $record->weekusers,
            'monthviews' => (int) $record->monthviews,
            'monthusers' => (int) $record->monthusers,
            'sharepct' => $totalmonthviews > 0 ? (((int) $record->monthviews) / $totalmonthviews) * 100 : 0,
        ];
    }

    return $results;
}

/**
 * Subquery to check that the log user has student role.
 */
function local_analitica_avanzada_student_role_sql_log(string $useralias = 'u_res'): string {
    return "EXISTS (
        SELECT 1
          FROM {role_assignments} ra_s
          JOIN {role} r_s ON r_s.id = ra_s.roleid
         WHERE ra_s.userid = {$useralias}.id
           AND (r_s.shortname = 'student' OR r_s.archetype = 'student')
    )";
}

/**
 * Get all module types available in the log for filter dropdown.
 */
function local_analitica_avanzada_get_resource_module_types(int $courseid = 0, array $scope = null, string $groupfilter = ''): array {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $monthstart = time() - (30 * DAYSECS);

    $params = [
        'ctxmodule' => CONTEXT_MODULE,
        'monthstart' => $monthstart,
    ];
    $conditions = [
        'l.contextlevel = :ctxmodule',
        'l.timecreated >= :monthstart',
        'l.anonymous = 0',
        'l.userid > 0',
        "l.crud = 'r'",
        local_analitica_avanzada_student_role_sql_log(),
    ];

    if (!empty($scope['restricted'])) {
        $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($userids) || empty($courseids)) {
            return [];
        }
        [$userinsql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'mtusr');
        $params += $userparams;
        $conditions[] = "l.userid {$userinsql}";
        if (!empty($courseid)) {
            if (!in_array($courseid, $courseids, true)) {
                return [];
            }
            $conditions[] = 'cm.course = :mtcourseid';
            $params['mtcourseid'] = $courseid;
        } else {
            [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'mtcrs');
            $params += $courseparams;
            $conditions[] = "cm.course {$courseinsql}";
        }
    } else if (!empty($courseid)) {
        $conditions[] = 'cm.course = :mtcourseid';
        $params['mtcourseid'] = $courseid;
    }

    $groupcondition = local_analitica_avanzada_get_group_filter_sql_condition($groupfilter, 'l.userid', 'mtgroupfilter', $params);
    if ($groupcondition !== null) {
        $conditions[] = $groupcondition;
    }

    $sql = "SELECT DISTINCT mo.name
              FROM {logstore_standard_log} l
              JOIN {user} u_res ON u_res.id = l.userid
              JOIN {course_modules} cm ON cm.id = l.contextinstanceid
              JOIN {modules} mo ON mo.id = cm.module
             WHERE " . implode(' AND ', $conditions) . "
          ORDER BY mo.name ASC";

    $records = $DB->get_records_sql($sql, $params);
    $types = [];
    foreach ($records as $record) {
        $types[] = $record->name;
    }

    return $types;
}
