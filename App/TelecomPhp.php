<?php

namespace App;

use App\Services\OutputService;
use App\Services\DatabaseService;

/**
 * Upload docs
 */
$doc = new Documents;
// Return array of privs with values is bool
$privs = ["DOC_TREE_1" => true, "DOC_TREE_2" => true, "DOC_TREE_3" => true, "DOC_SEE_ALL" => true];

$infoStr = '';

$databaseService = new DatabaseService("localhost", "root", "root", "root");

if (isset($_POST["operation"])) {
    if (isset($_POST["el_id"]) && isset($_POST["actor_id"]) && $privs['DOC_SEE_ALL']) {
        $in = ["el_id" => $_POST["el_id"], "actor_id" => $_POST["actor_id"]];
        if ($_POST["operation"] == "check_attach") {

            $res = $databaseService->executeRawQuery("SELECT * from documents where us_id=:actor_id AND id=:el_id", $in, 'select');
            if (!$res) {
                $infoStr = 'Not attached';
            } else {
                $infoStr = 'attached';
            }
        } elseif ($_POST["operation"] == "change_status") {
            $in["status_id"] = $_POST["status_id"];

            $res = $databaseService->executeRawQuery("UPDATE documents set id=:el_id, status_id=:status_id, us_id=:actor_id", $in, 'update');
            if (!$res) {
                $infoStr = "Not CHANGED";
            } else {
                $infoStr = 'changed';
            }
        }
    }
    if ($_POST["operation"] == 'upload_doc') {
        if (!$_FILES['upload_doc']['tmp_name']) {
            $infoStr = 'Выберите файл';
            OutputService::echoInfo($infoStr);
        }

        $in = [
            "p_us_id" => auth('us_id'),
            "p_doc_name" => $_POST['upload_type'],
            "p_file_name" => $_FILES['upload_doc']['name'],
            "data_BLOB" => file_get_contents($_FILES['upload_doc']['tmp_name'])];

        $res = $databaseService->executeRawQuery(
            "INSERT INTO documents (us_id, doc_name, file_name, data_blob, status_id) VALUES (:p_us_id, :p_doc_name, :p_file_name, :data_BLOB, 'new')",
            $in,
            'insert');
        if (!$res) {
            $infoStr = 'Error upload doc';
        } else {
            $infoStr = 'uploaded';
        }
    }
}

if ($infoStr != '') {
    OutputService::echoInfo($infoStr);
}