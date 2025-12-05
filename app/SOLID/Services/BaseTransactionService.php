<?php

namespace App\SOLID\Services;

use Illuminate\Support\Facades\DB;

abstract class BaseTransactionService
{
    protected function beginTransaction()
    {
        DB::beginTransaction();
    }

    protected function commitTransaction()
    {
        DB::commit();
    }

    protected function rollbackTransaction()
    {
        DB::rollback();
    }

    public function runInTransaction(callable $callback)
    {
        $this->beginTransaction();

        try {
            // Execute the provided callback within the transaction
            $result = $callback();

            // Commit the transaction if successful
            $this->commitTransaction();

            return $result;
        } catch (\Exception $e) {
            // Rollback the transaction if an exception occurs
            $this->rollbackTransaction();

            // Re-throw the exception for the caller to handle
            throw $e;
        }
    }
}
