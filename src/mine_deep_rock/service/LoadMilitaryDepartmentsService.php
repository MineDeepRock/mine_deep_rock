<?php


namespace mine_deep_rock\service;


use mine_deep_rock\adapter\MilitaryDepartmentJsonAdapter;
use mine_deep_rock\DataFolderPath;

class LoadMilitaryDepartmentsService
{
    /**
     * @return array
     */
    static function execute(): array {
        $militaryDepartment = [];
        $dh = opendir(DataFolderPath::MilitaryDepartment);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::MilitaryDepartment . $fileName) === "file") {
                $data = json_decode(file_get_contents(DataFolderPath::MilitaryDepartment . $fileName), true);
                $militaryDepartment[] = MilitaryDepartmentJsonAdapter::decode($data);
            }
        }
        closedir($dh);

        return $militaryDepartment;
    }
}