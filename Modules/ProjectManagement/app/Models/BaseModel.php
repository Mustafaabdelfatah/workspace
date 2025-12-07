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
        return config('core.database_connection', 'core');
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
     * Get the table name - removed database prefix to avoid double prefixing
     */
    public function getTable()
    {
        // Just return the table name without database prefix
        // Laravel will handle the database connection properly
        return $this->table ?? parent::getTable();
    }

    /**
     * Get the morph class name for polymorphic relations
     */
    public function getMorphClass()
    {
        return parent::getMorphClass();
    }
}
