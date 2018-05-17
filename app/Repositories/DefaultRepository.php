<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 08/06/17
 * Time: 11:55
 */

namespace Equinox\Repositories;

use Illuminate\Support\Facades\DB;

abstract class DefaultRepository
{

    /**
     * Function used to start a transaction on the "data" connection
     */
    public function startDataTransaction()
    {
        DB::beginTransaction();
    }

    /**
     * Function used to commit a transaction on the "data" connection
     */
    public function endDataTransaction()
    {
        DB::commit();
    }

    /**
     * Function used to rollback a transaction on the "data" connection
     */
    public function rollbackDataTransaction()
    {
        DB::rollBack();
    }
}