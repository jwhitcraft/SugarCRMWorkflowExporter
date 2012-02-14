<?php
chdir('..');
define('sugarEntry', true);
require_once('include/entryPoint.php');
global $current_user;
$current_user = new User();
$current_user->getSystemUser();

// which module do we want to export
$module = "Leads";

// include deleted ones?
$deleted = false;

$_workflow_tables = array(
    'workflow_actions',
    'workflow_actionshells',
    'workflow_alerts',
    'workflow_alertshells',
    'workflow_schedules',
    'workflow_triggershells',
);

$sql = "SELECT * FROM workflow WHERE base_module = '" . $module . "'";
if($deleted === false) {
    $sql .= " and deleted=0";
}
$sql .= ";";

$db = DBManagerFactory::getInstance();

function convert_db_record_to_sql($table, $row) {
    global $db;
    $sql = 'INSERT INTO ' . $table . '(';
    $keys = array_keys($row);

    $val = ') VALUES (';

    $sql .= implode(",", $keys);

    $_vals = array();

    foreach($keys as $k) {
        $_vals[] = $db->quote($row[$k]);
    }

    $sql .= $val . '"' . implode('","', $_vals) . '");';

    return $sql;
}

$workflows = $db->query($sql);

$strWorkflows = '';

while($workflow = $db->fetchByAssoc($workflows)) {
    $strWorkflows .= convert_db_record_to_sql('workflow', $workflow) . PHP_EOL;

    $id = $workflow['id'];
    foreach($_workflow_tables as $table) {
        $sql = "SELECT * FROM " . $table . " WHERE parent_id = '" . $id . "';";
        $_table_sql = $db->query($sql);
        while($_ts = $db->fetchByAssoc($_table_sql)) {
            $strWorkflows .= convert_db_record_to_sql($table, $_ts) . PHP_EOL;

            if($table == "workflow_alertshells" && $_ts['source_type'] == "Custom Template" && !empty($_ts['custom_template_id'])) {
                // fetch a custom template
                $sql = "SELECT * FROM email_templates where id = '" . $_ts['custom_template_id'] . "';";
                $et = $db->query($sql);
                while($_et = $db->fetchByAssoc($et)) {
                    $strWorkflows .= convert_db_record_to_sql('email_templates', $_et) . PHP_EOL;
                }
            }
        }
    }

    $strWorkflows .= PHP_EOL;
}

echo $strWorkflows;