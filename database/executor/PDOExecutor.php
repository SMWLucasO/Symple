<?php

namespace Symple\database\executor;


use Symple\database\DatabaseConnection;

/**
 * @author Lucas Ouwens
 * @version 0.4
 */
class PDOExecutor
{

    # bind and execute


    /**
     * Execute the specified query with a sanitization option
     *
     * @param $filter integer           A specified filter from the Filter object
     * @param $query string             The query string
     * @param $bindings array           The bindings of the array (associative array) (
     * @param $typeVerification array   Verification of each placeholder binding (what datatype it should be)
     * @param $fetchAll bool            True if you want to fetch everything, false if you want one record.
     * @return array|bool               True or array if the query was successfully executed, false if it failed.
     */
    public static function execute($filter, $query, $bindings = array(), $typeVerification = array(), $fetchAll = true)
    {
        $db = DatabaseConnection::getInstance()->getConnection();

        # Type verification for now
        foreach ($typeVerification as $key => $value) {
            $value = strtolower($value);
            if ($value === 'int' || $value === 'float' || $value === 'double') {
                if (!is_numeric($bindings[$key])) {
                    return false;
                }
            } else {
                if ($value === 'string') {
                    if (!is_string($bindings[$key])) {
                        return false;
                    }
                } else {
                    if ($value === 'bool') {
                        if (!is_bool($bindings[$key])) {
                            return false;
                        }
                    } else {
                        if ($value !== 'ignore') {
                            return false;
                        }
                    }
                }
            }
        }

        $statement = $db->prepare($query);

        # Sanitizing is automated :D
        if ($filter === Filter::NO_HTML_ALLOWED) {
            foreach ($bindings as $key => $value) {
                $bindings[$key] = strip_tags($value);
            }
        } else {
            if ($filter === Filter::CONVERT_HTML_CHARACTERS) {
                foreach ($bindings as $key => $value) {
                    $bindings[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }
        }

        foreach ((array)$bindings as $key => $value) {
            $statement->bindValue($key, $value);
        }

        if ($statement->execute()) {

            $type = strtolower(explode(' ', $query)[0]);
            if ($type === 'select') {
                $data = ($fetchAll ? $statement->fetchAll() : $statement->fetch());
                if (!empty($data)) {
                    return $data;
                } else {
                    return false;
                }
            } else {
                return $statement->rowCount() >= 1;
            }
        }

        return false;
    }

}