<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 14:39
 */

namespace Equinox\Repositories;


use Illuminate\Support\Facades\Schema;

class CapsuleRepository extends DefaultRepository
{
    /**
     * Function used to create a new capsule given the capsule name and capsule generator function
     * @param string $capsuleName
     * @param \Closure $capsuleGeneratorClosure
     */
    public function createCapsuleFromClosure(string $capsuleName, \Closure $capsuleGeneratorClosure)
    {
        Schema::create($capsuleName, $capsuleGeneratorClosure);
    }

    /**
     * Function used to drop a capsule if exists
     * @param string $capsuleName
     */
    public function dropCapsuleIfExists(string $capsuleName)
    {
        Schema::dropIfExists($capsuleName);
    }
}