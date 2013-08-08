<?php


$options = getopt(
    '',
    array('module:', 'deleted', 'delete-sql')
);

if (!isset($argv[1]) || !isset($options['module'])) {

    echo 'usage: php ' . $_SERVER['SCRIPT_NAME'] . ' --module [MODULE_NAME]' . PHP_EOL . PHP_EOL;
    echo 'options:  --deleted       Do you want to include deleted workflows?' . PHP_EOL;
    echo '          --delete-sql    Do you want to include the DELETE sql statements?' . PHP_EOL;

    exit(1);
}

$module = $options['module'];

// include deleted ones?
$deleted = isset($options['deleted']) ? true : false;

// do we wnat to try and delete each record first?
// useful for when we are rec-creating everything
$run_delete_first = isset($options['delete-sql']) ? true : false;

echo 'Running for module:           ' . $module . PHP_EOL;
echo 'Including deleted workflows?: ' . ($deleted ? 'Yes' : 'No') . PHP_EOL;
echo 'Including DELETE sql?:        ' . ($run_delete_first ? 'Yes' : 'No') . PHP_EOL;
echo PHP_EOL . PHP_EOL;

chdir('..');
define('sugarEntry', true);
require_once('include/entryPoint.php');
global $current_user;
$current_user = new User();
$current_user->getSystemUser();

$_workflow_tables = array(
    'workflow_actionshells',
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
    global $db, $run_delete_first;
    $sql = "";
    if($run_delete_first === true) {
        $sql .= "DELETE FROM " . $table . " WHERE id = '" . $row['id'] . "';" . PHP_EOL;
    }

    // get the field name
    $keys = array_keys($row);

    // start the insert sql statement
    $sql .= 'INSERT INTO ' . $table . '(' . implode(",", $keys) . ') VALUES (';

    $_vals = array();

    foreach($keys as $k) {
        $_vals[] = $db->quote($row[$k]);
    }

    $sql .= '"' . implode('","', $_vals) . '");';

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

            if(strpos($table,"shells") !== false) {
                // we have one
                if($table == "workflow_triggershells") {
                    // we need the data from the expressions table based off this id
                    $_tab = "expressions";
                } else {
                    // we need the data from the related table based off this id
                    $_tab = str_replace("hells", "", $table);
                }

                $sql = "SELECT * FROM " . $_tab . " where parent_id = '" . $_ts['id'] . "';";
                $et = $db->query($sql);
                while($_et = $db->fetchByAssoc($et)) {
                    $strWorkflows .= convert_db_record_to_sql($_tab, $_et) . PHP_EOL;
                }
            }


        }
    }

    $strWorkflows .= PHP_EOL;
}

echo $strWorkflows;