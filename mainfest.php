<?PHP

$manifest = array(
    'acceptable_sugar_versions' => array(
        'regex_matches' => array(
            "6\.*",
        ),
    ),
    'acceptable_sugar_flavors' => array(
        0 => 'OS',
        1 => 'PRO',
        2 => 'ENT',
        3 => 'CE',
    ),
    'readme' => '',
    'name' => 'Workflow Exporter',
    'description' => 'Export Workflows As A Module Loadable Package',
    'author' => 'Jon Whitcraft <jwhitcraft at sugarcrm.com>',
    'published_date' => '2/26/2012',
    'version' => '.1',
    'type' => 'module',
    'is_uninstallable' => true,
);

$installdefs = array(
    'id' => 'c2940c77a2a8d78faf30af72eeb5454b', // md5 of 'jwhitcraft-workflow-exporter'
    'language' => array(
        array(
            'from' => '<basepath>/Extensions/WorkFlow/Ext/Language/en_us.exportworkflow.php',
            'to_module' => 'Workflow',
            'language' => 'en_us'
        )
    ),
    'mkdir' => array(
        array('path' => 'custom/Extension'),
        array('path' => 'custom/Extension/WorkFlow'),
        array('path' => 'custom/Extension/WorkFlow/Ext'),
        array('path' => 'custom/Extension/WorkFlow/Ext/ActionViewMap'),
        array('path' => 'custom/Extension/WorkFlow/Ext/Language'),
        array('path' => 'custom/Extension/WorkFlow/Ext/Menus'),
        array('path' => 'custom/modules/WorkFlow'),
    ),
    'copy' => array(
        array('from' => '<basepath>/Extensions/WorkFlow/Ext/ActionViewMap/',
            'to' => 'custom/Extension/WorkFlow/Ext/ActionViewMap',
        ),
        array('from' => '<basepath>/Extensions/WorkFlow/Ext/Menus/',
            'to' => 'custom/Extension/WorkFlow/Ext/Menus',
        ),
        array('from' => '<basepath>/Modules/WorkFlow/',
            'to' => 'custom/modules/WorkFlow/')
    ),
);
