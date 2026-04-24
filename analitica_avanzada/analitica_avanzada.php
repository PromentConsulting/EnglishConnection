<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

require_login();

if (!local_analitica_avanzada_user_can_view()) {
    throw new required_capability_exception(
        context_system::instance(),
        'local/analitica_avanzada:view',
        'nopermissions',
        get_string('nopermission', 'local_analitica_avanzada')
    );
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/analitica_avanzada/analitica_avanzada.php'));
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('page_title', 'local_analitica_avanzada'));
$PAGE->set_heading(get_string('page_title', 'local_analitica_avanzada'));
$PAGE->requires->css(new moodle_url('/local/analitica_avanzada/styles.css'));

$search      = trim(optional_param('search', '', PARAM_TEXT));
$courseid    = optional_param('courseid', 0, PARAM_INT);
$inactiveonly = optional_param('inactiveonly', 0, PARAM_BOOL);
$lowgradeonly = optional_param('lowgradeonly', 0, PARAM_BOOL);
$statusfilter = optional_param('status', '', PARAM_ALPHA);
$groupfilter  = optional_param('groupfilter', '', PARAM_TEXT);
$page        = max(0, optional_param('page', 0, PARAM_INT));
$perpage     = 25;

// Resources section filters.
$rescourse   = optional_param('rescourse', 0, PARAM_INT);
$restype     = optional_param('restype', '', PARAM_ALPHANUMEXT);

$groupfilter = core_text::strtolower(trim($groupfilter));

// Export action.
$export      = optional_param('export', '', PARAM_ALPHA);

$filters = [
    'search'       => $search,
    'courseid'     => $courseid,
    'inactiveonly' => $inactiveonly,
    'lowgradeonly' => $lowgradeonly,
    'status'       => $statusfilter,
    'groupfilter'  => $groupfilter,
];

$scope = local_analitica_avanzada_get_dashboard_scope();
$courses = local_analitica_avanzada_get_courses_for_filter($scope);
if (!empty($scope['restricted']) && !empty($courseid) && !array_key_exists($courseid, $courses)) {
    $courseid = 0;
    $filters['courseid'] = 0;
}

// Handle CSV/Excel export before any output.
if (!empty($export) && in_array($export, ['csv', 'xlsx'], true)) {
    $allusers = local_analitica_avanzada_get_all_filtered_users($filters, $scope);

    $statuslabels = [
        'pending' => 'Pendiente',
        'active' => 'Activo',
        'finished' => 'Finalizado',
    ];

    $rows = [];
    $rows[] = ['Nombre', 'Apellidos', 'Usuario', 'Email', 'Última conexión', 'Estado', 'Fecha finalización', 'Calificación media', '% Progreso', 'Tiempo medio sesión', 'Curso'];

    foreach ($allusers as $user) {
        $lastaccess = !empty($user->lastaccess) ? userdate($user->lastaccess, '%d/%m/%Y %H:%M') : 'Nunca';
        $statuslabel = $statuslabels[$user->enrolstatus] ?? $user->enrolstatus;
        $timeend = !empty($user->timeend) ? userdate($user->timeend, '%d/%m/%Y') : '—';
        $avggrade = $user->avggrade !== null ? number_format($user->avggrade, 1, ',', '.') . '%' : '—';
        $progress = $user->progress !== null ? number_format($user->progress, 1, ',', '.') . '%' : '—';
        $avgsession = local_analitica_avanzada_format_duration((int) $user->avgsession);
        $coursefullname = !empty($user->coursefullname) ? $user->coursefullname : '—';

        $rows[] = [
            $user->firstname,
            $user->lastname,
            $user->username,
            $user->email,
            $lastaccess,
            $statuslabel,
            $timeend,
            $avggrade,
            $progress,
            $avgsession,
            $coursefullname,
        ];
    }

    if ($export === 'csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="analitica_usuarios_' . date('Ymd_His') . '.csv"');
        header('Pragma: no-cache');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel UTF-8.
        foreach ($rows as $row) {
            fputcsv($out, $row, ';');
        }
        fclose($out);
        exit;
    }

    // XLSX export using a simple XML-based SpreadsheetML format.
    $filename = 'analitica_usuarios_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    // Build SpreadsheetML XML.
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:x="urn:schemas-microsoft-com:office:excel">';
    $xml .= '<Styles>';
    $xml .= '<Style ss:ID="header"><Font ss:Bold="1"/><Interior ss:Color="#2D4FA4" ss:Pattern="Solid"/><Font ss:Color="#FFFFFF" ss:Bold="1"/></Style>';
    $xml .= '</Styles>';
    $xml .= '<Worksheet ss:Name="Analítica"><Table>';

    foreach ($rows as $i => $row) {
        $xml .= '<Row>';
        foreach ($row as $cell) {
            $style = ($i === 0) ? ' ss:StyleID="header"' : '';
            $escaped = htmlspecialchars((string) $cell, ENT_XML1, 'UTF-8');
            $xml .= "<Cell{$style}><Data ss:Type=\"String\">{$escaped}</Data></Cell>";
        }
        $xml .= '</Row>';
    }

    $xml .= '</Table></Worksheet></Workbook>';
    echo $xml;
    exit;
}

$metrics  = local_analitica_avanzada_get_global_metrics($scope);
$userdata = local_analitica_avanzada_get_filtered_users($filters, $page, $perpage, $scope);
$resources = local_analitica_avanzada_get_top_resources(20, $rescourse, $scope, $restype, '');
$moduletypes = local_analitica_avanzada_get_resource_module_types($rescourse, $scope, '');

$baseparams = [];
if ($search !== '') {
    $baseparams['search'] = $search;
}
if (!empty($courseid)) {
    $baseparams['courseid'] = $courseid;
}
if (!empty($inactiveonly)) {
    $baseparams['inactiveonly'] = 1;
}
if (!empty($lowgradeonly)) {
    $baseparams['lowgradeonly'] = 1;
}
if (!empty($statusfilter)) {
    $baseparams['status'] = $statusfilter;
}
if (!empty($groupfilter)) {
    $baseparams['groupfilter'] = $groupfilter;
}
$baseurl = new moodle_url('/local/analitica_avanzada/analitica_avanzada.php', $baseparams);

$from = $userdata['total'] > 0 ? ($page * $perpage) + 1 : 0;
$to   = min((($page * $perpage) + $perpage), $userdata['total']);

// Build course select options.
$courseoptions = [html_writer::tag('option', 'Todos los cursos', ['value' => 0])];
foreach ($courses as $id => $fullname) {
    $attributes = ['value' => $id];
    if ((int) $id === (int) $courseid) {
        $attributes['selected'] = 'selected';
    }
    $courseoptions[] = html_writer::tag('option', format_string($fullname), $attributes);
}

// Build resources course select options.
$rescourseptions = [html_writer::tag('option', 'Todos los cursos', ['value' => 0])];
foreach ($courses as $id => $fullname) {
    $attributes = ['value' => $id];
    if ((int) $id === (int) $rescourse) {
        $attributes['selected'] = 'selected';
    }
    $rescourseptions[] = html_writer::tag('option', format_string($fullname), $attributes);
}

// Build resources type select options.
$restypeoptions = [html_writer::tag('option', 'Todos los tipos', ['value' => ''])];
foreach ($moduletypes as $mtype) {
    $attributes = ['value' => $mtype];
    if ($mtype === $restype) {
        $attributes['selected'] = 'selected';
    }
    $restypeoptions[] = html_writer::tag('option', ucfirst($mtype), $attributes);
}

$specialgroups = local_analitica_avanzada_get_special_group_filters();
$groupfilteroptions = [html_writer::tag('option', 'Todos los grupos', ['value' => ''])];
foreach ($specialgroups as $specialgroupkey => $specialgrouplabel) {
    $attributes = ['value' => $specialgroupkey];
    if ($specialgroupkey === $groupfilter) {
        $attributes['selected'] = 'selected';
    }
    $groupfilteroptions[] = html_writer::tag('option', s($specialgrouplabel), $attributes);
}
$othergroupskey = local_analitica_avanzada_get_other_groups_filter_key();
$groupfilteroptions[] = html_writer::tag('option', 'Otros grupos', array_merge(
    ['value' => $othergroupskey],
    $groupfilter === $othergroupskey ? ['selected' => 'selected'] : []
));


echo $OUTPUT->header();

echo html_writer::start_div('aa-dashboard');

// Hero.
echo html_writer::start_div('aa-hero');
echo html_writer::tag('div', 'Dashboard personalizado Moodle', ['class' => 'aa-hero-kicker']);
echo html_writer::tag('h1', 'Analítica Global', ['class' => 'aa-hero-title']);
echo html_writer::tag('p', 'Visión global del rendimiento, progreso y uso de recursos de la plataforma.', ['class' => 'aa-hero-subtitle']);
echo html_writer::tag(
    'div',
    'Las métricas de sesión y recursos se estiman con los logs de los últimos 30 días. Solo se incluyen datos de estudiantes.',
    ['class' => 'aa-hero-pill']
);
echo html_writer::end_div();

// Global cards.
echo html_writer::start_div('aa-grid-cards');

$cards = [
    [
        'label' => 'Usuarios inactivos > 7 días',
        'value' => number_format($metrics['inactivecount'], 0, ',', '.'),
        'meta'  => local_analitica_avanzada_format_percent($metrics['inactivepct']) . ' del total de estudiantes',
    ],
    [
        'label' => 'Calificación media < 50%',
        'value' => number_format($metrics['lowgradecount'], 0, ',', '.'),
        'meta'  => local_analitica_avanzada_format_percent($metrics['lowgradepct']) . ' del total de estudiantes',
    ],
    [
        'label' => 'Tasa de finalización',
        'value' => local_analitica_avanzada_format_percent($metrics['completionrate']),
        'meta'  => 'Sobre matrículas activas en cursos con finalización habilitada',
    ],
    [
        'label' => 'Tiempo medio de la sesión',
        'value' => local_analitica_avanzada_format_duration($metrics['avgsession']),
        'meta'  => 'Promedio estimado de todas las sesiones (30 días)',
    ],
];

foreach ($cards as $card) {
    echo html_writer::start_div('aa-card aa-metric-card');
    echo html_writer::tag('div', $card['label'], ['class' => 'aa-card-label']);
    echo html_writer::tag('div', $card['value'], ['class' => 'aa-card-value']);
    echo html_writer::tag('div', $card['meta'], ['class' => 'aa-card-meta']);
    echo html_writer::end_div();
}

echo html_writer::end_div();

// Individual analytics.
echo html_writer::start_div('aa-card aa-section-card');
echo html_writer::tag('h2', 'Analítica individual', ['class' => 'aa-section-title']);
echo html_writer::tag('p', 'Listado de estudiantes con filtros, paginación y métricas individuales.', ['class' => 'aa-section-subtitle']);

echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => new moodle_url('/local/analitica_avanzada/analitica_avanzada.php'),
    'class'  => 'aa-filters',
]);

echo html_writer::start_div('aa-filter-field aa-filter-search');
echo html_writer::tag('label', 'Buscador', ['for' => 'aa-search']);
echo html_writer::empty_tag('input', [
    'type'        => 'text',
    'name'        => 'search',
    'id'          => 'aa-search',
    'value'       => $search,
    'placeholder' => 'Nombre, apellidos, email o usuario',
]);
echo html_writer::end_div();

echo html_writer::start_div('aa-filter-field');
echo html_writer::tag('label', 'Curso', ['for' => 'aa-courseid']);
echo html_writer::tag('select', implode('', $courseoptions), [
    'name' => 'courseid',
    'id'   => 'aa-courseid',
]);
echo html_writer::end_div();

echo html_writer::start_div('aa-filter-field');
echo html_writer::tag('label', 'Estado', ['for' => 'aa-status']);
$statusoptions = [
    html_writer::tag('option', 'Todos', ['value' => '']),
    html_writer::tag('option', 'Pendiente', array_merge(['value' => 'pending'], $statusfilter === 'pending' ? ['selected' => 'selected'] : [])),
    html_writer::tag('option', 'Activo', array_merge(['value' => 'active'], $statusfilter === 'active' ? ['selected' => 'selected'] : [])),
    html_writer::tag('option', 'Finalizado', array_merge(['value' => 'finished'], $statusfilter === 'finished' ? ['selected' => 'selected'] : [])),
];
echo html_writer::tag('select', implode('', $statusoptions), [
    'name' => 'status',
    'id'   => 'aa-status',
]);
echo html_writer::end_div();

echo html_writer::start_div('aa-filter-field');
echo html_writer::tag('label', 'Grupo', ['for' => 'aa-groupfilter']);
echo html_writer::tag('select', implode('', $groupfilteroptions), [
    'name' => 'groupfilter',
    'id'   => 'aa-groupfilter',
]);
echo html_writer::end_div();

echo html_writer::start_div('aa-filter-field aa-filter-checks');
echo html_writer::tag('span', 'Filtros rápidos', ['class' => 'aa-filter-label']);

echo html_writer::start_div('aa-check-row');
echo html_writer::empty_tag('input', [
    'type'    => 'checkbox',
    'name'    => 'inactiveonly',
    'id'      => 'aa-inactiveonly',
    'value'   => 1,
    'checked' => !empty($inactiveonly) ? 'checked' : null,
]);
echo html_writer::tag('label', 'Más de 7 días desconectado', ['for' => 'aa-inactiveonly']);
echo html_writer::end_div();

echo html_writer::start_div('aa-check-row');
echo html_writer::empty_tag('input', [
    'type'    => 'checkbox',
    'name'    => 'lowgradeonly',
    'id'      => 'aa-lowgradeonly',
    'value'   => 1,
    'checked' => !empty($lowgradeonly) ? 'checked' : null,
]);
echo html_writer::tag('label', 'Calificación media inferior al 50%', ['for' => 'aa-lowgradeonly']);
echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::start_div('aa-filter-actions');
echo html_writer::empty_tag('input', [
    'type'  => 'submit',
    'class' => 'btn btn-primary',
    'value' => 'Aplicar filtros',
]);
echo html_writer::link(
    new moodle_url('/local/analitica_avanzada/analitica_avanzada.php'),
    'Limpiar',
    ['class' => 'btn btn-secondary aa-reset-button']
);
echo html_writer::end_div();

echo html_writer::end_tag('form');

echo html_writer::start_div('aa-results-meta');
echo html_writer::tag(
    'div',
    'Mostrando ' . $from . '–' . $to . ' de ' . $userdata['total'] . ' estudiantes',
    ['class' => 'aa-results-count']
);
if ($userdata['total'] > $perpage) {
    echo $OUTPUT->paging_bar($userdata['total'], $page, $perpage, $baseurl);
}
echo html_writer::end_div();

echo html_writer::start_div('aa-table-wrap');
echo html_writer::start_tag('table', ['class' => 'aa-table']);
echo html_writer::start_tag('thead');
echo html_writer::tag('tr',
    html_writer::tag('th', 'Nombre') .
    html_writer::tag('th', 'Apellidos') .
    html_writer::tag('th', 'Email') .
    html_writer::tag('th', 'Última conexión') .
    html_writer::tag('th', 'Estado') .
    html_writer::tag('th', 'Fecha finalización') .
    html_writer::tag('th', 'Calificación media') .
    html_writer::tag('th', '% progreso') .
    html_writer::tag('th', 'Tiempo medio de sesión') .
    html_writer::tag('th', 'Curso')
);
echo html_writer::end_tag('thead');
echo html_writer::start_tag('tbody');

if (!empty($userdata['users'])) {
    $statuslabels = [
        'pending'  => 'Pendiente',
        'active'   => 'Activo',
        'finished' => 'Finalizado',
    ];

    foreach ($userdata['users'] as $user) {
        $lastaccess    = !empty($user->lastaccess) ? userdate($user->lastaccess, get_string('strftimedatetimeshort')) : 'Nunca';
        $gradeclass    = ($user->avggrade !== null && $user->avggrade < 50) ? 'aa-pill aa-pill-alert' : 'aa-pill';
        $progressclass = ($user->progress !== null && $user->progress < 50) ? 'aa-pill aa-pill-warning' : 'aa-pill';
        $timeend       = !empty($user->timeend) ? userdate($user->timeend, get_string('strftimedate')) : '—';

        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', s($user->firstname));
        echo html_writer::tag('td', s($user->lastname));
        echo html_writer::tag('td', s($user->email));
        echo html_writer::tag('td', $lastaccess);
        echo html_writer::tag('td', local_analitica_avanzada_render_status_badge($user->enrolstatus));
        echo html_writer::tag('td', $timeend);
        echo html_writer::tag('td', html_writer::tag('span', local_analitica_avanzada_format_percent($user->avggrade), ['class' => $gradeclass]));
        echo html_writer::tag('td', html_writer::tag('span', local_analitica_avanzada_format_percent($user->progress), ['class' => $progressclass]));
        echo html_writer::tag('td', html_writer::tag('span', local_analitica_avanzada_format_duration((int) $user->avgsession), ['class' => 'aa-pill']));
        echo html_writer::tag('td', s($user->coursefullname ?? '—'));
        echo html_writer::end_tag('tr');
    }
} else {
    echo html_writer::tag('tr', html_writer::tag('td', 'No se han encontrado estudiantes con los filtros aplicados.', ['colspan' => 10, 'class' => 'aa-empty-cell']));
}

echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');
echo html_writer::end_div();

// Export buttons.
$exportbaseparams = $baseparams;
$exportcsvurl = new moodle_url('/local/analitica_avanzada/analitica_avanzada.php', array_merge($exportbaseparams, ['export' => 'csv']));
$exportxlsxurl = new moodle_url('/local/analitica_avanzada/analitica_avanzada.php', array_merge($exportbaseparams, ['export' => 'xlsx']));

echo html_writer::start_div('aa-export-bar');
echo html_writer::tag('span', 'Descargar listado:', ['class' => 'aa-export-label']);
echo html_writer::link($exportcsvurl, '⬇ CSV', ['class' => 'btn btn-outline-secondary aa-export-btn']);
echo html_writer::link($exportxlsxurl, '⬇ Excel (.xlsx)', ['class' => 'btn btn-outline-secondary aa-export-btn']);
echo html_writer::end_div();

echo html_writer::end_div();

// Resources table.
echo html_writer::start_div('aa-card aa-section-card');
echo html_writer::tag('h2', 'Recursos más visitados', ['class' => 'aa-section-title']);
echo html_writer::tag('p', 'Top de actividades y recursos consultados durante los últimos 30 días (solo estudiantes).', ['class' => 'aa-section-subtitle']);

// Resources filters.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => new moodle_url('/local/analitica_avanzada/analitica_avanzada.php'),
    'class'  => 'aa-filters aa-res-filters',
]);

// Preserve individual analytics filters as hidden fields.
foreach ($baseparams as $bkey => $bval) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $bkey, 'value' => $bval]);
}
if ($page > 0) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'page', 'value' => $page]);
}

echo html_writer::start_div('aa-filter-field');
echo html_writer::tag('label', 'Curso', ['for' => 'aa-rescourse']);
echo html_writer::tag('select', implode('', $rescourseptions), [
    'name' => 'rescourse',
    'id'   => 'aa-rescourse',
]);
echo html_writer::end_div();

echo html_writer::start_div('aa-filter-field');
echo html_writer::tag('label', 'Tipo de recurso', ['for' => 'aa-restype']);
echo html_writer::tag('select', implode('', $restypeoptions), [
    'name' => 'restype',
    'id'   => 'aa-restype',
]);
echo html_writer::end_div();

echo html_writer::start_div('aa-filter-actions');
echo html_writer::empty_tag('input', [
    'type'  => 'submit',
    'class' => 'btn btn-primary',
    'value' => 'Filtrar recursos',
]);
if (!empty($rescourse) || !empty($restype)) {
    $clearresurl = new moodle_url('/local/analitica_avanzada/analitica_avanzada.php', $baseparams);
    echo html_writer::link($clearresurl, 'Limpiar', ['class' => 'btn btn-secondary aa-reset-button']);
}
echo html_writer::end_div();

echo html_writer::end_tag('form');

echo html_writer::start_div('aa-table-wrap');
echo html_writer::start_tag('table', ['class' => 'aa-table aa-resources-table']);
echo html_writer::start_tag('thead');
echo html_writer::tag('tr',
    html_writer::tag('th', 'Recurso') .
    html_writer::tag('th', 'Curso') .
    html_writer::tag('th', 'Tipo') .
    html_writer::tag('th', 'Visitas día') .
    html_writer::tag('th', 'Usuarios día') .
    html_writer::tag('th', 'Visitas semana') .
    html_writer::tag('th', 'Usuarios semana') .
    html_writer::tag('th', 'Visitas mes') .
    html_writer::tag('th', 'Usuarios mes') .
    html_writer::tag('th', '% tráfico mensual')
);
echo html_writer::end_tag('thead');
echo html_writer::start_tag('tbody');

if (!empty($resources)) {
    foreach ($resources as $resource) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', s($resource['name']));
        echo html_writer::tag('td', s($resource['course']));
        echo html_writer::tag('td', html_writer::tag('span', s($resource['type']), ['class' => 'aa-pill']));
        echo html_writer::tag('td', number_format($resource['dayviews'], 0, ',', '.'));
        echo html_writer::tag('td', number_format($resource['dayusers'], 0, ',', '.'));
        echo html_writer::tag('td', number_format($resource['weekviews'], 0, ',', '.'));
        echo html_writer::tag('td', number_format($resource['weekusers'], 0, ',', '.'));
        echo html_writer::tag('td', number_format($resource['monthviews'], 0, ',', '.'));
        echo html_writer::tag('td', number_format($resource['monthusers'], 0, ',', '.'));
        echo html_writer::tag('td', html_writer::tag('span', local_analitica_avanzada_format_percent($resource['sharepct']), ['class' => 'aa-pill aa-pill-info']));
        echo html_writer::end_tag('tr');
    }
} else {
    echo html_writer::tag('tr', html_writer::tag('td', 'No hay datos suficientes de acceso a recursos en el periodo analizado.', ['colspan' => 10, 'class' => 'aa-empty-cell']));
}

echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');
echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
