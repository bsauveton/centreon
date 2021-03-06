<?php
/**
 * Copyright 2005-2011 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */
$tpl = $this->getTemplate();

/* Load css */
$tpl->addCss('angled-headers.css');

/* Get Param */
$params = $this->getWidgetParams();

/* Get DB */
$dbM = $this->getMonitoringDb();
$db = $this->getConfigurationDb();

/* Init Params */
$data = array();

/* Get Data */
$query = "SELECT host_name, service_description, service_id, host_id, size.current_value AS size, used.current_value AS used, (used.current_value/size.current_value*100) AS percent FROM index_data, metrics used, metrics size WHERE service_description LIKE 'Disk%' AND used.index_id = id AND size.index_id = id AND size.metric_name = 'size' AND used.metric_name = 'used' ORDER BY percent DESC LIMIT 10";
$stmt = $dbM->prepare($query);
$stmt->execute();
while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $row["percent"] = round($row["percent"]);
    $unit = getUnit($row['size']);

    $row['used'] = formatData($row['used'], $unit);
    $row['size'] = formatData($row['size'], $unit);

    $data[] = $row;
}


function getUnit($value) {
    $unit = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po');
    $i = 0;
    while ($value > 1024) {
        $value = $value / 1024;
        $i++;
    }
    return array($i, $unit[$i]);
}

function formatData($value, $unit) {
    for ($i = 0; $i != $unit[0]; $i++) {
        $value /= 1024;
    }
    return round($value, 1)." ".$unit[1];
}

/* Assign infos */
$tpl->assign("data", $data);

/* Display */
$tpl->display('file:[CentreonTop10DiskWidget]console.tpl');
