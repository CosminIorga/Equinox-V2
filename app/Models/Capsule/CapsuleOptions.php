<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/04/18
 * Time: 12:19
 */

namespace Equinox\Models\Capsule;


use Equinox\Models\NonPersistentModel;

/**
 * Class CapsuleOptions
 * @package Equinox\Models\Capsule
 * @property bool $skipColumns
 */
class CapsuleOptions extends NonPersistentModel
{
    /**
     * Capsule options
     * @var array
     */
    protected $options;

    /**
     * CapsuleOptions constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (empty($options)) {
            $options = $this->applyDefaults();
        }

        $this->options = $options;
    }

    /**
     * Function used to apply defaults if capsule options is empty
     * @return array
     */
    protected function applyDefaults(): array
    {
        return [
            'skipColumns' => false,
        ];
    }

    /**
     * Getter for skipColumns attribute
     * @return bool
     */
    protected function getSkipColumnsAttribute()
    {
        return $this->options['skip_columns'];
    }

    /**
     * Setter for skipColumns attribute
     * @param $value
     * @return CapsuleOptions
     */
    protected function setSkipColumnsAttribute($value): self
    {
        $this->options['skip_columns'] = (bool) $value;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }
}