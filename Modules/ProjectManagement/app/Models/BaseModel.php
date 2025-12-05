<?php

namespace Modules\ProjectManagement\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Get the core database connection
     */
    protected function getCoreConnection(): string
    {
        return config('core.database_connection', 'mysql');
    }

    /**
     * Get the core database name
     */
    protected function getCoreDatabase(): string
    {
        $connection = $this->getCoreConnection();
        return config("database.connections.{$connection}.database");
    }

    /**
     * Constructor
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set the connection dynamically
        $this->setConnection($this->getCoreConnection());
    }

    /**
     * Get the table name with database prefix
     */
    public function getTable()
    {
        if (!isset($this->table)) {
            return parent::getTable();
        }

        return $this->getCoreDatabase() . '.' . $this->table;
    }

    /**
     * Get the morph class name for polymorphic relations
     */
    public function getMorphClass()
    {
        return parent::getMorphClass();
    }
}
