<?php

namespace Admin\Api\Models;

use mysqli;

class DbConnector
{
    private const DB_SERVER = 'db';
    private const DB_USERNAME = 'root';
    private const DB_PASSWORD = 'haslohaslo123';
    private const DB_NAME = 'PressPlus';

    CONST SHORT = 0;
    CONST SHORT_ARRAY = 1;
    CONST SHORT_ARRAY_ASSOC = 2;
    CONST FULL_ARRAY = 3;

    /**
     * @var mysqli|void
     */
    public $db;

    public function __construct()
    {
        $this->db = $this->connectWithDb();
    }

    public function select(
        int $type,
        array $columns,
        string $table,
        array $where = [],
        int $limit = 0,
        array $sortArray = ['sortBy' => '']
    ) {
        return $this->selectFromDB($type, $columns, $table, $where, $limit, $sortArray);
    }

    public function insert(string $table, array $columnsAndData): void
    {
        $columns = implode(',', array_keys($columnsAndData));
        $values = implode("','", $columnsAndData);

        $sql = <<<SQL
INSERT INTO {$table} ({$columns}) VALUES ('{$values}')
SQL;

        $this->db->query($sql);
    }

    public function update(string $table, array $columnsAndData, array $whereCondition = []): void
    {
        $dataArray = [];

        foreach ($columnsAndData as $key=>$value) {
            $dataArray[] = $key . "=" . "'" . $value . "'";
        }
        $whereToSql = !empty($whereCondition) ? self::createWhere($whereCondition) : '';

        $sql = "UPDATE {$table} SET " . implode(',', $dataArray) . " " . $whereToSql . " LIMIT 1";

        $this->db->query($sql);
    }


    public function delete(string $table, array $whereCondition = [])
    {
        $whereToSql = self::createWhere($whereCondition);

        $sql = <<<SQL
DELETE FROM {$table} {$whereToSql}
SQL;
        $this->db->query($sql);
    }

    public function selectDataByType(int $type, string $sql)
    {
        switch ($type) {
            case self::SHORT:
                $dbData = $this->fetchOne($sql);
                break;
            case self::SHORT_ARRAY:
                $dbData = $this->fetchCol($sql);
                break;
            case self::SHORT_ARRAY_ASSOC:
                $dbData = $this->fetchRow($sql);
                break;
            case self::FULL_ARRAY:
                $dbData = $this->fetchArray($sql);
                break;
            default:
                $dbData = false;
        }

        return $dbData;
    }

    private function connectWithDb()
    {
        $db = new mysqli(self::DB_SERVER, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

        if ($db->connect_errno) {
            echo 'Error: ' . $db->connect_errno;
            die();
        }

        return $db;
    }

    protected function connectToDB()
    {
        $link = mysqli_connect(self::DB_SERVER, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

        if($link === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        return $link;
    }

    private function createAndConnectDbToolsDb($localDbName)
    {
        $this->systimDb->query('CREATE DATABASE IF NOT EXISTS dbTools');
        $db = new mysqli(self::LOCAL_HOST, self::LOCAL_USER, self::LOCAL_PASS, $localDbName);
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS history (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    db_name char(50) DEFAULT NULL,
    windows_time char(50) DEFAULT NULL,
    execution_time char(50) DEFAULT NULL,
    execute_date_server timestamp NULL DEFAULT current_timestamp(),
    execute_info char(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
SQL;
        $db->query($sql);

        return $db;
    }

    private function selectFromDB(int $type, array $columns, string $table, array $where, int $limit, array $sortArray)
    {
        $columnsToSql = implode(',', $columns);
        $whereToSql = isset($where) ? self::createWhere($where) : '';
        $sort = $this->createSortBy($sortArray);
        $limitSql = $this->createLimit($limit);

        $sql = <<<SQL
SELECT
    {$columnsToSql}
FROM
    {$table}
{$whereToSql}
{$sort}
{$limitSql}
SQL;

        return $this->selectDataByType($type, $sql);
    }

    private function createWhere(array $where): string
    {
        $whereToSql = '';

        if (!empty($where)) {
            $part = 0;
            $whereToSql = 'WHERE';
            foreach ($where as $key => $whereVal) {
                foreach ($whereVal as $dbColumn => $value) {
                    if ($part) {
                        $whereToSql .= ' AND ';
                    }
                    $whereToSql .= " " . $dbColumn . " " . $key .  " '" . $this->esc($value) . "' " ;
                    $part++;
                }
            }
        }

        return $whereToSql;
    }

    private function createSortBy(array $sortArray): string
    {
        $sort = '';

        if (!empty($sortArray['sortBy'])) {
            $sort = 'ORDER BY ' . $sortArray['sortBy'];
        }
        if (!empty($sortArray['sortOrder']) && !empty($sort)) {
            $sort .= ' ' . $sortArray['sortOrder'];
        }

        return $sort;
    }


    private function createLimit(int $limit): string
    {
        $limitSql = '';

        if ($limit > 0) {
            $limitSql = 'LIMIT ' . $limit;
        }

        return $limitSql;
    }


    private function fetchOne(string $sql)
    {
        $result = $this->db->query($sql);

        if($result===false || $this->numRows($result)==0) {
            return false;
        }

        $row = $result->fetch_row();;

        if ($row === false) {
            return false;
        }

        return $row[0];
    }

    private function fetchCol(string $sql)
    {
        $data = [];

        $result = $this->db->query($sql);

        if($result && $this->numRows($result) > 0) {
            while ($row = $result->fetch_array()) {
                $data[] = $row[0];
            }
        }

        return $data;
    }

    private function fetchRow(string $sql)
    {
        $result = $this->db->query($sql);

        if ($result === false || $this->numRows($result) == 0) {
            return false;
        }

        $row = $result->fetch_assoc();

        if($row === false) {
            return false;
        }

        return $row;
    }

    private function fetchArray(string $sql)
    {
        $data = [];
        $result = $this->db->query($sql);

        if($result && $this->numRows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!empty($row['id']) && !isset($data[$row['id']])) {
                    $data[$row['id']] = $row;
                } else {
                    $data[] = $row;
                }
            }
        }

        return $data;
    }

    private function esc(string $string)
    {
        return $this->db->real_escape_string($string);
    }

    private function numRows($res)
    {
        return $res->num_rows;
    }
}