<?php

namespace Symple\database\adm;

use Symple\database\executor\Filter;
use Symple\database\executor\PDOExecutor;

/**
 * @author Lucas Ouwens
 * @version 1.0 (Alpha, non-optimized)
 */
class Entity
{

    /**
     *
     * A variable which holds an associative array of [column] => [value]
     *
     * @var $fields array
     */
    private $fields;

    /**
     *
     * Holds the primary key of the table which this Entity(or child) belongs to
     *
     * @var $primaryKey String
     *
     * Holds the value of the primary key, this value is always an integer.
     *
     * @var $primaryValue int
     *
     */
    private $primaryKey, $primaryValue;

    /**
     *
     * The model (table) which this Entity(or child) belongs to
     *
     * @var $model Model
     */
    private $model;


    /**
     *
     * A boolean which specifies if the table should be automatically updated when setting a variable from the '$fields' array
     *
     * @var $autoUpdate bool
     */
    private $autoUpdate = false;

    /**
     * Entity constructor.
     * @param $model Model          The Model object related to this Entity
     * @param $data array           An associative array of [column] => [value] to fill the fields with
     * @throws \Exception           gets thrown if the Entity cannot be initialized or the specified model isn't an instance of the Model class
     */
    public function __construct($model, $data)
    {
        if ($model instanceof Model) {
            $this->model = $model;
            $this->fields = $data;
            if (!($this->_init())) {
                throw new \Exception("Unable to initialize entity, make sure the primary key and value aren't empty.");
            }
        } else {
            throw new \Exception("Invalid object type given for model, this needs to be a 'Model' object.");
        }
    }

    /**
     * Initialize the Entity(or child) by setting the primary key and primary value.
     * @return bool         Returns true if initialized, otherwise false.
     */
    protected function _init()
    {
        if (!(empty($this->getModel()->getPrimaryKey()))) {
            if (!(empty($this->fields[$this->getModel()->getPrimaryKey()]))) {
                $this->primaryKey = $this->getModel()->getPrimaryKey();
                $this->primaryValue = $this->fields[$this->primaryKey];
                return true;
            }
        }

        return false;
    }

    /**
     * Update the row related to this Entity(or child) by specifying what you want to update
     * @param $assoc array          An associative array of [column] => [value] to specify the update
     * @param $verify array         An associative array of [column] => [datatype] to specify what the datatype the value should be
     *                                  (datatypes are: int, float, double, string, bool and ignore)
     * @param $filter int           A Filter constant to specify if there should be an extra action or not
     * @return bool                 Returns true if the row was successfully updated, otherwise false
     */
    public function update($assoc, $verify, $filter = Filter::NONE)
    {
        $setString = "";

        foreach ((array)$assoc as $key => $value) {
            if (in_array($key, array_keys($this->getModel()->getTypes()))) {
                $setString .= $key . "= :" . $key . ",";
            }
        }

        $setString = rtrim($setString, ',');

        $assoc["p_key"] = $this->getPrimaryValue();
        $verify["p_key"] = "int";

        return PDOExecutor::execute(
            $filter,
            "UPDATE " . $this->getModel()->getTable() . " SET " . $setString . " WHERE " . $this->getPrimaryKey() . " = :p_key",
            $assoc,
            $verify
        );

    }

    /**
     * Delete the object specified to this row
     * @return bool             Returns true if deleted, otherwise false.
     */
    public function delete()
    {
        return PDOExecutor::execute(
            Filter::NONE,
            "DELETE FROM " . $this->getModel()->getTable() . " WHERE " . $this->getPrimaryKey() . " = :p_key",
            array(
                "p_key" => $this->getPrimaryValue()
            ),
            array(
                "p_key" => "int"
            )
        );
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return int
     */
    public function getPrimaryValue()
    {
        return $this->primaryValue;
    }

    /**
     * A magic method to get the variables stored in the fields array if the variable exists
     * @param $name string          The variable name
     * @return mixed|null           Returns the value of the variable or null if it does not exist.
     */
    public function __get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        return null;
    }

    /**
     * Update the variable in the fields array if the variable exists, otherwise nothing'll happen
     *  if autoUpdate is true, it will also update in the table itself
     * @param $name string          The variable name stored in the fields array
     * @param $value mixed          The new value to be stored in the fields array under the specified 'name' key
     */
    public function __set($name, $value)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name] = $value;
            if ($this->isAutoUpdate()) {
                $this->update([$name => $value], [$name => "ignore"]);
            }
        }
    }

    /**
     * @return bool
     */
    public function isAutoUpdate()
    {
        return $this->autoUpdate;
    }

    /**
     * When set to true, updates the set values in the row itself too.
     * @param $autoUpdate bool
     */
    public function setAutoUpdate($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
    }

}