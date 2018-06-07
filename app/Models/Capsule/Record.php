<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 04/06/18
 * Time: 11:59
 */

namespace Equinox\Models\Capsule;


use Equinox\Models\NonPersistentModel;

class Record extends NonPersistentModel
{

    protected const HASH_PIVOT_AGGREGATOR = '__';

    /**
     * The record structure
     * @var array
     */
    protected $data;

    /**
     * Setter for hash key
     * @param string $hashKey
     * @return Record
     */
    public function setHashKey(string $hashKey): self
    {
        $this->data['hash'][$hashKey] = null;

        return $this;
    }

    /**
     * Setter for hash value
     * @param array $pivots
     * @return Record
     */
    public function setHashValue(array $pivots): self
    {
        ksort($pivots);

        $hashValue = md5(implode(self::HASH_PIVOT_AGGREGATOR, $pivots));

        $this->data['hash'][key($this->data['hash'])] = $hashValue;

        return $this;
    }

    /**
     * Setter for pivot key
     * @param string $pivotKey
     * @return Record
     */
    public function setPivotKey(string $pivotKey): self
    {
        $this->data['pivots'][$pivotKey] = null;

        return $this;
    }

    /**
     * Setter for pivot value
     * @param string $pivotKey
     * @param string $pivotValue
     * @return Record
     */
    public function setPivotValue(string $pivotKey, string $pivotValue): self
    {
        if (array_key_exists($pivotKey, $this->data['pivots'])) {
            $this->data['pivots'][$pivotKey] = $pivotValue;
        }

        return $this;
    }

    /**
     * Setter for interval key
     * @param string $intervalKey
     * @return Record
     */
    public function setIntervalKey(string $intervalKey): self
    {
        $this->data['intervals'][$intervalKey] = null;

        return $this;
    }

    /**
     * Setter for interval key
     * @param string $intervalKey
     * @param $intervalValue
     * @return Record
     */
    public function setIntervalValue(string $intervalKey, $intervalValue): self
    {
        if (array_key_exists($intervalKey, $this->data['intervals'])) {
            $this->data['intervals'][$intervalKey] = $intervalValue;
        }

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->data['hash'],
            $this->data['pivots'],
            $this->data['intervals']
        );
    }

    /**
     * Stringify data
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(', ', $this->toArray());
    }
}