<?php

namespace Symple\database\adm;

use Symple\database\executor\Filter;
use Symple\database\executor\PDOExecutor;

/**
 * @author Lucas Ouwens
 * @version 1.0 (Alpha, non-optimized)
 */
class Model
{

    /**
     *
     * A variable which stores the table that this Model belongs to
     *
     * @var string
     */
    private $table;

    /**
     *
     * A variable which stores the primary key of the specified table.
     *
     * @var string
     */
    private $primaryKey;

    /**
     *
     * An associative which stores the datatype of the columns as such
     *      [column] => type
     *
     * @var array
     */
    private $types = array();

    /**
     * Model constructor.
     * @param $table string             The table which this model is for.
     * @throws \Exception               An unknown table was specified, so we cannot continue.
     */
    public function __construct($table)
    {
        if (($this->primaryKey = self::allowed($table)) !== false) {
            $this->table = $table;
            $this->types = self::allowed($table, true);
        } else {
            throw new \Exception('Unknown table specified, cannot continue.');
        }
    }


    /**
     * Check if the table exists and get related data from it
     * @param $table string             The table which we are checking for if it exists
     * @param $fetchTypes bool          A boolean to specify if we want to fetch the types or the primary key
     * @return array|bool               Returns false if it does not exist, an array if it exists and you set $fetchTypes to true, otherwise it will return the primary key
     */
    private static function allowed($table, $fetchTypes = false)
    {
        $config = require __DIR__ . '/../../config/config.php';

        $columns = PDOExecutor::execute(
            Filter::NONE,
            "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY FROM information_schema.columns WHERE table_schema = :dbname AND TABLE_NAME = :tablename",
            array(
                "dbname" => $config["dbname"],
                "tablename" => $table
            ),
            array(
                "dbname" => "string",
                "tablename" => "string"
            )
        );

        if (is_array($columns) && !(empty($columns))) {
            if ($fetchTypes) {
                $array = array();
                foreach ($columns as $column) {
                    $array[$column["COLUMN_NAME"]] = $column["DATA_TYPE"];
                }
                return $array;
            } else {
                foreach ((array)$columns as $column) {
                    if ($column["COLUMN_KEY"] === 'PRI') {
                        return $column["COLUMN_NAME"];
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get a new Model object and initialize it
     * @param $table string         The table of which you want to get a Model of.
     * @return Model                Returns the Model object for your table.
     */
    public static function get($table)
    {
        return new Model($table);
    }


    /**
     * Get an Entity(or child) object with the specified id as primary key
     * @param $id int               The primary key value of the row you want to select
     * @param $filter int           A Filter contstant to specify what action the executor should do
     * @return Entity|null          Returns an Entity or a registered child class depending on if one is registered or not
     */
    public function byId($id, $filter = Filter::NONE)
    {
        if (is_numeric($id)) {
            if (!(empty($this->primaryKey) && empty($this->types) && empty($this->table))) {
                $data = PDOExecutor::execute(
                    $filter,
                    "SELECT * FROM " . $this->table . " WHERE " . $this->primaryKey . " = :p_key",
                    array(
                        "p_key" => $id
                    ),
                    array(
                        "p_key" => "int"
                    ),
                    false
                );

                if (is_array($data) && !empty($data)) {
                    return $this->getRegisteredEntity($data);
                }

            }
        }

        return null;
    }

    /**
     * Get either the child if it is registered or the Entity object if it is not
     * @param $data array           An associative array containing the column => value notation for the fields
     * @return Entity               Returns either an Entity or a child of the Entity class, depending on if it is registered or not.
     */
    private function getRegisteredEntity($data)
    {
        $config = require __DIR__ . '/../../config/config.php';

        if (in_array($this->table, array_keys($config["defined_entities"]))) {
            $obj = new $config["defined_entities"][$this->table]($this, $data); # No idea if this works lmao
            if (is_object($obj)) {
                return $obj;
            }
        }

        return new Entity($this, $data); # create a new Entity which we are receiving by id
    }

    /**
     * Select an array of Entity(or children) objects, specified from the column & value given
     * @param $column string            The column which you want to check for the specified value
     * @param $value mixed              The value which you are looking for in the specified column
     * @return array|null               Returns an array if data has been retrieved, otherwise it will return null.
     */
    public function by($column, $value)
    {
        if (in_array($column, array_keys($this->types))) {
            if (!(empty($this->primaryKey) && empty($this->table))) {
                $data = PDOExecutor::execute(
                    Filter::NONE,
                    "SELECT * FROM " . $this->table . " WHERE " . $column . " = :col",
                    array(
                        "col" => $value
                    ),
                    array(
                        "col" => "ignore"
                    ),
                    true
                );

                if (is_array($data) && !(empty($data))) {
                    $entities = array();
                    foreach ($data as $objectFiller) {
                        array_push($entities, $this->getRegisteredEntity($objectFiller));
                    }

                    return $entities;
                }
            }

        }

        return null;
    }

    /**
     * Select all the Entity(or children) objects and return them as an array
     * @return array|null           Returns an array of Entities (or child class of the Entity) if successful, otherwise it will return null
     */
    public function all()
    {
        if (!(empty($this->primaryKey) && empty($this->table))) {
            $data = PDOExecutor::execute(
                Filter::NONE,
                "SELECT * FROM " . $this->table
            );
            if (is_array($data) && !(empty($data))) {
                $entities = array();
                foreach ($data as $objectFiller) {
                    array_push($entities, $this->getRegisteredEntity($objectFiller));
                }

                return $entities;
            }
        }

        return null;
    }

    /**
     * Insert a new row into the table related to this Model
     * @param $assoc array              An associative array consisting of [column] => [value] to insert into the table as a new row
     * @param $verify array             An associative array consisting of [column] => [datatype] to verify the authenticity of the above
     *                                    ( options are: int, float, double, string, bool and ignore )
     *
     * @param int $filter A filter constant to specify if it should remove all HTML, special char it or do nothing at all.
     * @return bool                     Returns true if the row was inserted, otherwise it will return false.
     */
    public function create($assoc, $verify, $filter = Filter::NONE)
    {
        return PDOExecutor::execute(
            $filter,
            "INSERT INTO " . $this->table . "(" . (implode(',', array_keys($assoc))) . ") VALUES (:" . (implode(', :',
                array_keys($assoc))) . ")",
            $assoc,
            $verify
        );
    }


    /**
     * Select the last row from the table related to this Model and return it as an Entity(or child) object
     * @return Entity|null      Returns an Entity object if the data can get selected, otherwise it will return null.
     */
    public function lastRow()
    {
        if (!(empty($this->primaryKey) && empty($this->table))) {
            $data = PDOExecutor::execute(
                Filter::NONE,
                "SELECT * FROM " . $this->table . " ORDER BY " . $this->getPrimaryKey() . " DESC LIMIT 1",
                false
            );

            if (is_array($data) && !(empty($data))) {
                return $this->getRegisteredEntity($data);
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return array|bool
     */
    public function getTypes()
    {
        return $this->types;
    }

}