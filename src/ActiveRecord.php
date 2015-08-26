<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity
 */

namespace BeeBot\Entity;

use ReflectionClass;
use ReflectionProperty;

/**
 * Global canvas for all models define property setter and getter logic
 * @package BeeBot\Entity
 * @method boolean isDated()
 * @method boolean isChild()
 * @method boolean isFactory()
 * @method boolean isJsonSerializable()
 * @method boolean isSerializable()
 */
abstract class ActiveRecord implements \IteratorAggregate
{
    /**
     * Cache property for loaded properties
     * When a new entity type is built, its property meta data are extracted
     * @var array
     */
    private static $CACHE = [];

    /**
     * The current connection
     * @var Connections\ConnectionInterface
     */
    private static $CONNECTION;

    /**
     * List of all PHP interface that are considered behaviours
     * @var array
     */
    private static $BEHAVIOUR_INTERFACE = [
        "serializable" => "Serializable",
        "jsonserializable" => "JsonSerializable"
    ];

    /**
     * Property collection used in the current entity
     * @var array
     */
    private $properties;

    /**
     * Behaviour collection for the current entity
     * Behaviours are defined by traits and extend Entity capacities
     * @var array
     */
    private $behaviours;

    /**
     * Read object properties with ReflectionClass
     * Only public, protected and private are extracted, not static
     */
    public function __construct()
    {
        self::preload();
        $this->init();
    }

    /**
     * Generate the entity meta data cache for the given ActiveRecordModel
     * @param string|\BeeBot\Entity\ActiveRecord $model
     */
    protected static function generateCache($model)
    {
        $meta = new \stdClass();
        $class = new ReflectionClass($model);
        $meta->properties = $meta->behaviours = [];

        //Extract properties except static ones
        $properties = $class->getProperties(
            ReflectionProperty::IS_PUBLIC|
            ReflectionProperty::IS_PROTECTED|
            ReflectionProperty::IS_PRIVATE
        );
        foreach ($properties as $property) {
            //Static are not considered as entity props because of their global scope
            if ($property->isStatic()) {
                continue;
            }
            $item = new Property($property);
            $meta->properties[$property->getName()] = $item;
        }

        //Extract interfaces
        $meta->interfaces = $class->getInterfaceNames();

        //Extract traits
        $meta->traits = $class->getTraitNames();
        while (($class = $class->getParentClass()) instanceof ReflectionClass) {
            $meta->traits = array_merge($class->getTraitNames(), $meta->traits);
        }

        //Extract behaviours from traits
        foreach ($meta->traits as $trait) {
            $parts = [];
            if (preg_match('/Behaviours.([A-Za-z]*)Entity$/', $trait, $parts) === 1) {
                $b = strtolower($parts[1]);
                $meta->behaviours[$b] = isset(self::$BEHAVIOUR_INTERFACE[$b])?false:true;
            }
        }

        //Combine trait behaviours with interface ones
        $behaviourInterfaces = array_intersect(
            self::$BEHAVIOUR_INTERFACE,
            $meta->interfaces
        );
        $meta->behaviours = array_merge(
            $meta->behaviours,
            array_fill_keys(array_keys($behaviourInterfaces), true)
        );


        $tmp = explode("\\", get_called_class());
        $meta->type = strtolower(array_pop($tmp));

        //Put meta in cache
        self::$CACHE[get_called_class()] = $meta;
    }

    /**
     * Try to boot ActiveRecord from data source name
     * A callback can be defined to execute action after boot
     * @param string $dsn
     * @param \Closure $callback
     */
    final public static function boot($dsn, \Closure $callback = null)
    {
        $connection = Connections\ConnectionFactory::build($dsn);
        self::setConnection($connection);

        if (is_callable($callback)) {
            $callback($connection);
        }
    }

    /**
     * Define directly with an object the adapter to be used within ActiveRecord
     * @param \BeeBot\Entity\Connections\ConnectionInterface $conn
     */
    final public static function setConnection(
        Connections\ConnectionInterface $conn
    ) {
        self::$CONNECTION = $conn;
    }

    /**
     * Access the current adapter or throw error if no adapter loaded
     * @throws \RuntimeException
     * @return Connections\ConnectionInterface
     */
    final public static function getConnection()
    {
        if (self::$CONNECTION === null) {
            throw new \RuntimeException(
                "There is no active Connection"
            );
        }
        return self::$CONNECTION;
    }

    /**
     * Retrieve current type. This is used as table/index name and
     * must be overidden for specific behaviours
     * @return string
     */
    public static function getType()
    {
        $name = self::preload();
        return self::$CACHE[$name]->type;
    }

    /**
     * Generic wrapper to emulate behaviour detection:
     *   isDated, isChild, isFactory, ...
     * @param string $name
     * @param array $arguments
     * @return boolean
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        $parts = [];
        if (preg_match('/^is([A-Za-z]+)/', $name, $parts) === 1) {
            if (count($arguments) > 0) {
                throw new \InvalidArgumentException("This method does not required any argument: ".$name);
            }

            return self::is(strtolower($parts[1]));
        }

        throw new \BadMethodCallException(get_called_class().'::'.$name.' method does not exists!');
    }

    /**
     * Check if the current entity has a given behaviour (behaviour exists with true)
     * @param string $behaviour
     * @return boolean
     */
    protected static function is($behaviour)
    {
        $name = self::preload();
        return
            isset(self::$CACHE[$name]->behaviours[$behaviour]) &&
            self::$CACHE[$name]->behaviours[$behaviour] === true;
    }

    /**
     * Preload class meta cache and return current class name
     * @return string
     */
    protected static function preload()
    {
        $name = get_called_class();
        if (!isset(self::$CACHE[$name])) {
            self::generateCache($name);
        }

        return $name;
    }

    /**
     * Initialize current instance context
     */
    protected function init()
    {
        $this->properties = self::$CACHE[get_called_class()]->properties;
        $this->behaviours = self::$CACHE[get_called_class()]->behaviours;
    }

    /**
     * Add a new property to the current entity
     * @param string $name property name
     * @param mixed $value value to be set
     */
    public function __set($name, $value)
    {
        if (!isset($this->properties[$name])) {
            throw new \InvalidArgumentException(
                'Property name given does not exists in the current object: '.$name
            );
        }

        return $this->properties[$name]->set($value, $this);
    }

    /**
     * Return property value
     * @param string $name Property name
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->properties[$name])) {
            throw new \InvalidArgumentException(
                'Property name given does not exists in the current object: '.$name
            );
        }

        return $this->properties[$name]->get($this);
    }

    /**
     * Check if the current property exists and is not null
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        if (!isset($this->properties[$name]) || $this->properties[$name]->get($this) === null) {
            return false;
        }
        return true;
    }

    /**
     * Unset the given attribute value
     * @param string $name
     */
    public function __unset($name)
    {
        if (isset($this->properties[$name])) {
            $this->properties[$name]->set(null, $this);
        }
    }

    /**
     * Allow to crawl active record instance and retrieve object properties
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $props = [];
        foreach ($this->properties as $name => $prop) {
            if ($prop->isReadable()) {
                $props[$name] = $prop->get($this);
            }
        }
        return new \ArrayIterator($props);
    }
}
