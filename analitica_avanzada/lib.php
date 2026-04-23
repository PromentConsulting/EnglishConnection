<?php

defined('MOODLE_INTERNAL') || die();

/**
 * SQL condition for regular platform users.
 */
function local_analitica_avanzada_regular_user_sql(string $alias = 'u'): string {
    return "{$alias}.deleted = 0 AND {$alias}.suspended = 0 AND {$alias}.confirmed = 1 AND {$alias}.username <> 'guest'";
}

/**
 * Access control for site admins, users with system capability and teachers in any course.
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
               AND ctx.contextlevel = :contextlevel
              JOIN {role} r
                ON r.id = ra.roleid
             WHERE ra.userid = :userid
               AND (
                    r.shortname IN ('teacher', 'editingteacher')
                    OR r.archetype IN ('teacher', 'editingteacher')
               )";

    return $DB->record_exists_sql($sql, [
        'contextlevel' => CONTEXT_COURSE,
        'userid' => $userid,
    ]);
}


/**
 * Dashboard visibility scope for current viewer.
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

    if (is_siteadmin($userid) || has_capability('local/analitica_avanzada:view', context_system::instance(), $userid)) {
        $cache[$userid] = $scope;
        return $scope;
    }

    $rolesql = "SELECT DISTINCT ctx.instanceid AS courseid
                  FROM {role_assignments} ra
                  JOIN {context} ctx
                    ON ctx.id = ra.contextid
                   AND ctx.contextlevel = :contextlevel
                  JOIN {role} r
                    ON r.id = ra.roleid
                 WHERE ra.userid = :userid
                   AND (
                        r.shortname IN ('teacher', 'editingteacher')
                        OR r.archetype IN ('teacher', 'editingteacher')
                   )";

    $teachercourses = $DB->get_records_sql($rolesql, [
        'contextlevel' => CONTEXT_COURSE,
        'userid' => $userid,
    ]);

    if (empty($teachercourses)) {
        $scope['restricted'] = true;
        $cache[$userid] = $scope;
        return $scope;
    }

    $courseids = array_map(static function($record) {
        return (int) $record->courseid;
    }, array_values($teachercourses));
    [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'scpc');
    $groupparams = $courseparams + ['groupuserid' => $userid];

    $groupsql = "SELECT DISTINCT g.id, g.courseid
                   FROM {groups} g
                   JOIN {groups_members} gm
                     ON gm.groupid = g.id
                  WHERE gm.userid = :groupuserid
                    AND g.courseid {$courseinsql}";

    $grouprecords = $DB->get_records_sql($groupsql, $groupparams);
    if (empty($grouprecords)) {
        $scope['restricted'] = true;
        $cache[$userid] = $scope;
        return $scope;
    }

    $groupids = [];
    $visiblecourseids = [];
    foreach ($grouprecords as $grouprecord) {
        $groupids[] = (int) $grouprecord->id;
        $visiblecourseids[] = (int) $grouprecord->courseid;
    }

    $visiblecourseids = array_values(array_unique($visiblecourseids));
    [$groupinsql, $groupparams] = $DB->get_in_or_equal($groupids, SQL_PARAMS_NAMED, 'scpg');
    $userparams = $groupparams + [
        'viewerid' => $userid,
        'studentcontextlevel' => CONTEXT_COURSE,
    ];

    $usersql = "SELECT DISTINCT gm.userid
                  FROM {groups_members} gm
                  JOIN {groups} g
                    ON g.id = gm.groupid
                  JOIN {user} u
                    ON u.id = gm.userid
                  JOIN {user_enrolments} ue
                    ON ue.userid = gm.userid
                   AND ue.status = 0
                  JOIN {enrol} e
                    ON e.id = ue.enrolid
                   AND e.status = 0
                   AND e.courseid = g.courseid
                  JOIN {context} ctx
                    ON ctx.contextlevel = :studentcontextlevel
                   AND ctx.instanceid = g.courseid
                  JOIN {role_assignments} ra
                    ON ra.userid = gm.userid
                   AND ra.contextid = ctx.id
                  JOIN {role} r
                    ON r.id = ra.roleid
                   AND (
                        r.shortname = 'student'
                        OR r.archetype = 'student'
                   )
                 WHERE gm.groupid {$groupinsql}
                   AND gm.userid <> :viewerid
                   AND " . local_analitica_avanzada_regular_user_sql('u');

    $visibleusers = $DB->get_records_sql($usersql, $userparams);

    $scope['restricted'] = true;
    $scope['courseids'] = $visiblecourseids;
    $scope['groupids'] = $groupids;
    $scope['userids'] = array_map(static function($record) {
        return (int) $record->userid;
    }, array_values($visibleusers));

    $cache[$userid] = $scope;
    return $scope;
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
 * Total regular users.
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
                   AND u.id {$insql}";

        return (int) $DB->count_records_sql($sql, $params);
    }

    $sql = "SELECT COUNT(1)
              FROM {user} u
             WHERE " . local_analitica_avanzada_regular_user_sql('u');

    return (int) $DB->count_records_sql($sql);
}

/**
 * Global course completion rate.
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
                          JOIN {enrol} e
                            ON e.id = ue.enrolid
                          JOIN {course} c
                            ON c.id = e.courseid
                          JOIN {user} u
                            ON u.id = ue.userid
                         WHERE {$wheresql}
                   ) pairs";

    $completedsql = "SELECT COUNT(1)
                        FROM (
                             SELECT DISTINCT ue.userid, e.courseid
                               FROM {user_enrolments} ue
                               JOIN {enrol} e
                                 ON e.id = ue.enrolid
                               JOIN {course} c
                                 ON c.id = e.courseid
                               JOIN {user} u
                                 ON u.id = ue.userid
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
 * Average session time for all regular users, estimated from the last N days.
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
              JOIN {user} u
                ON u.id = l.userid
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
 * Global dashboard metrics.
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
                             JOIN {grade_items} gi
                               ON gi.id = gg.itemid
                             JOIN {user} u
                               ON u.id = gg.userid
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
              JOIN {grade_items} gi
                ON gi.id = gg.itemid
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
                      JOIN {enrol} e
                        ON e.id = ue.enrolid
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
 * Courses enrolled by selected users.
 */
function local_analitica_avanzada_get_user_courses(array $userids, array $scope = null): array {
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
              JOIN {enrol} e
                ON e.id = ue.enrolid
              JOIN {course} c
                ON c.id = e.courseid
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
 * Filtered and enriched users.
 */
function local_analitica_avanzada_get_filtered_users(array $filters, int $page = 0, int $perpage = 25, array $scope = null): array {
    global $DB;

    $scope = $scope ?? local_analitica_avanzada_get_dashboard_scope();
    $params = [];
    $joins = [];
    $where = [local_analitica_avanzada_regular_user_sql('u')];

    if (!empty($scope['restricted'])) {
        $userids = local_analitica_avanzada_get_scoped_user_ids($scope);
        $courseids = local_analitica_avanzada_get_scoped_course_ids($scope);
        if (empty($userids) || empty($courseids)) {
            return [
                'total' => 0,
                'users' => [],
            ];
        }

        [$userinsql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'scopeusr');
        $params += $userparams;
        $where[] = "u.id {$userinsql}";
    }

    if (!empty($filters['courseid'])) {
        if (!empty($scope['restricted']) && !in_array((int) $filters['courseid'], local_analitica_avanzada_get_scoped_course_ids($scope), true)) {
            return [
                'total' => 0,
                'users' => [],
            ];
        }

        $joins[] = "JOIN (
                        SELECT DISTINCT ue.userid
                          FROM {user_enrolments} ue
                          JOIN {enrol} e
                            ON e.id = ue.enrolid
                         WHERE ue.status = 0
                           AND e.status = 0
                           AND e.courseid = :filtercourseid
                    ) ec
                    ON ec.userid = u.id";
        $params['filtercourseid'] = (int) $filters['courseid'];
    }

    if (!empty($filters['inactiveonly'])) {
        $where[] = '(u.lastaccess = 0 OR u.lastaccess < :inactivecutoff)';
        $params['inactivecutoff'] = time() - (7 * DAYSECS);
    }

    if (!empty($filters['lowgradeonly'])) {
        $joins[] = "JOIN (
                        SELECT gg.userid,
                               AVG(
                                   CASE
                                       WHEN (gi.grademax - gi.grademin) > 0
                                       THEN (gg.finalgrade - gi.grademin) / (gi.grademax - gi.grademin)
                                       ELSE NULL
                                   END
                               ) AS avggrade
                          FROM {grade_grades} gg
                          JOIN {grade_items} gi
                            ON gi.id = gg.itemid
                          JOIN {user} ug
                            ON ug.id = gg.userid
                         WHERE gi.itemtype = 'course'
                           AND gg.finalgrade IS NOT NULL
                           AND " . local_analitica_avanzada_regular_user_sql('ug');
        if (!empty($scope['restricted'])) {
            [$gradecourseinsql, $gradecourseparams] = $DB->get_in_or_equal(local_analitica_avanzada_get_scoped_course_ids($scope), SQL_PARAMS_NAMED, 'lgcrs');
            $params += $gradecourseparams;
            $joins[count($joins) - 1] .= "
                           AND gi.courseid {$gradecourseinsql}";
        }
        $joins[count($joins) - 1] .= "
                      GROUP BY gg.userid
                    ) lg
                    ON lg.userid = u.id";
        $where[] = 'lg.avggrade < :lowgrademax';
        $params['lowgrademax'] = 0.5;
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
            . ')';

        $params['searchfull'] = $searchparam;
        $params['searchfirstname'] = $searchparam;
        $params['searchlastname'] = $searchparam;
        $params['searchemail'] = $searchparam;
    }

    $fromsql = '{user} u ' . implode(' ', $joins);
    $wheresql = implode(' AND ', $where);

    $countsql = "SELECT COUNT(DISTINCT u.id)
                   FROM {$fromsql}
                  WHERE {$wheresql}";
    $total = (int) $DB->count_records_sql($countsql, $params);

    $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.lastaccess
              FROM {$fromsql}
             WHERE {$wheresql}
          ORDER BY u.lastname ASC, u.firstname ASC";

    $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
    $users = array_values($records);
    $userids = array_map(static function($user) {
        return (int) $user->id;
    }, $users);

    $selectedcourseid = (int) ($filters['courseid'] ?? 0);
    $grades = local_analitica_avanzada_get_user_average_grades($userids, $scope, $selectedcourseid);
    $progress = local_analitica_avanzada_get_user_progress($userids, $selectedcourseid, $scope);
    $courses = local_analitica_avanzada_get_user_courses($userids, $scope);
    $sessions = local_analitica_avanzada_get_user_session_times($userids, time() - (30 * DAYSECS));

    foreach ($users as $user) {
        $userid = (int) $user->id;
        $user->avggrade = $grades[$userid] ?? null;
        $user->progress = $progress[$userid] ?? null;
        $user->courses = $courses[$userid] ?? [];
        $user->avgsession = $sessions[$userid] ?? 0;
    }

    return [
        'total' => $total,
        'users' => $users,
    ];
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
 * Most visited activities/resources from the last 30 days.
 */
function local_analitica_avanzada_get_top_resources(int $limit = 20, int $courseid = 0, array $scope = null): array {
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

    $topsql = "SELECT l.contextinstanceid AS cmid,
                      COUNT(*) AS monthviews,
                      COUNT(DISTINCT l.userid) AS monthusers
                 FROM {logstore_standard_log} l
                 JOIN {course_modules} cm
                   ON cm.id = l.contextinstanceid
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

    [$insql, $inparams] = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED, 'rescm');
    $metricparams = array_merge($inparams, [
        'metricctxmodule'   => CONTEXT_MODULE,
        'metricdaystart1'   => $daystart,
        'metricdaystart2'   => $daystart,
        'metricweekstart1'  => $weekstart,
        'metricweekstart2'  => $weekstart,
        'metricmonthstart1' => $monthstart,
        'metricmonthstart2' => $monthstart,
    ]);
    $metricconditions = [
        'l.contextlevel = :metricctxmodule',
        "l.contextinstanceid {$insql}",
        'l.anonymous = 0',
        'l.userid > 0',
        "l.crud = 'r'",
    ];

    if (!empty($scope['restricted'])) {
        [$metricuserinsql, $metricuserparams] = $DB->get_in_or_equal(local_analitica_avanzada_get_scoped_user_ids($scope), SQL_PARAMS_NAMED, 'metricusr');
        $metricparams += $metricuserparams;
        $metricconditions[] = "l.userid {$metricuserinsql}";
    }

    $metricsql = "SELECT l.contextinstanceid AS cmid,
                        SUM(CASE WHEN l.timecreated >= :metricdaystart1 THEN 1 ELSE 0 END) AS dayviews,
                        COUNT(DISTINCT CASE WHEN l.timecreated >= :metricdaystart2 THEN l.userid ELSE NULL END) AS dayusers,
                        SUM(CASE WHEN l.timecreated >= :metricweekstart1 THEN 1 ELSE 0 END) AS weekviews,
                        COUNT(DISTINCT CASE WHEN l.timecreated >= :metricweekstart2 THEN l.userid ELSE NULL END) AS weekusers,
                        SUM(CASE WHEN l.timecreated >= :metricmonthstart1 THEN 1 ELSE 0 END) AS monthviews,
                        COUNT(DISTINCT CASE WHEN l.timecreated >= :metricmonthstart2 THEN l.userid ELSE NULL END) AS monthusers
                    FROM {logstore_standard_log} l
                WHERE " . implode(' AND ', $metricconditions) . "
                GROUP BY l.contextinstanceid";

    $metrics = $DB->get_records_sql($metricsql, $metricparams);
    $cms = $DB->get_records_list('course_modules', 'id', $cmids, '', 'id,course,module');

    $moduleids = [];
    $courseids = [];
    foreach ($cms as $cm) {
        $moduleids[] = (int) $cm->module;
        $courseids[] = (int) $cm->course;
    }

    $moduleids = array_unique($moduleids);
    $courseids = array_unique($courseids);

    $modules = !empty($moduleids) ? $DB->get_records_list('modules', 'id', $moduleids, '', 'id,name') : [];
    $courses = !empty($courseids) ? $DB->get_records_list('course', 'id', $courseids, '', 'id,fullname') : [];

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

        $metric = $metrics[$cmid] ?? null;

        $results[] = [
            'name' => format_string($cmname),
            'course' => isset($courses[$courseidcurrent]) ? format_string($courses[$courseidcurrent]->fullname) : '—',
            'type' => $modules[$cm->module]->name ?? 'activity',
            'dayviews' => $metric ? (int) $metric->dayviews : 0,
            'dayusers' => $metric ? (int) $metric->dayusers : 0,
            'weekviews' => $metric ? (int) $metric->weekviews : 0,
            'weekusers' => $metric ? (int) $metric->weekusers : 0,
            'monthviews' => $metric ? (int) $metric->monthviews : (int) $record->monthviews,
            'monthusers' => $metric ? (int) $metric->monthusers : (int) $record->monthusers,
            'sharepct' => $totalmonthviews > 0 ? (((int) ($metric->monthviews ?? $record->monthviews)) / $totalmonthviews) * 100 : 0,
        ];
    }

    return $results;
}