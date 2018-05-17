<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 23/04/18
 * Time: 14:13
 */

namespace Equinox\Models\Capsule;


use Equinox\Definitions\ColumnsDefinitions;
use Equinox\Exceptions\ValidationException;
use Equinox\Models\NonPersistentModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class Column
 * @package Equinox\Models\Capsule
 * @property string $dataType
 * @property string $name
 * @property array $extra
 * @property bool $allowNull
 * @property string $index
 */
class Column extends NonPersistentModel
{
    /**
     * The column config
     * @var array
     */
    protected $config;

    /**
     * The column type
     * @var string
     */
    protected $type;

    /**
     * Column constructor.
     * @param string $type
     * @param array $config
     */
    public function __construct(string $type, array $config)
    {
        $this->validateType($type)
            ->validateConfig($config)
            ->sanitizeConfig($config)
            ->save($type, $config);
    }

    /**
     * Function used to validate column type
     * @param string $type
     * @return Column
     * @throws ValidationException
     */
    protected function validateType(string $type): self
    {
        $rules = [
            'type' => Rule::in(ColumnsDefinitions::COLUMN_TYPES),
        ];

        $validator = Validator::make(
            [
                'type' => $type,
            ],
            $rules
        );

        if ($validator->fails()) {
            throw new ValidationException(ValidationException::VALIDATION_FAILED, $validator->errors()->toArray());
        }

        return $this;
    }

    /**
     * Function used to validate given column config
     * @param array $config
     * @return Column
     * @throws ValidationException
     */
    protected function validateConfig(array $config): self
    {
        $rules = $this->getConfigValidationRules();

        $validator = Validator::make(
            $config,
            $rules
        );

        if ($validator->fails()) {
            throw new ValidationException(ValidationException::VALIDATION_FAILED, $validator->errors()->toArray());
        }

        return $this;
    }

    /**
     * Function used to return validation rules
     * @return array
     */
    protected function getConfigValidationRules()
    {
        return [
            'name' => 'required|string',
            'data_type' => Rule::in(ColumnsDefinitions::DATA_TYPES),
            'extra' => 'nullable|array',
            'index' => ['nullable', 'string', Rule::in(ColumnsDefinitions::INDEXES)],
            'allow_null' => 'required|boolean',
        ];
    }

    /**
     * Function used to sanitize the config before saving it
     * @param array $config
     * @return Column
     */
    protected function sanitizeConfig(array &$config): self
    {
        $config = array_intersect_key($config, $this->getConfigValidationRules());

        return $this;
    }

    /**
     * @param string $type
     * @param array $config
     */
    protected function save(string $type, array $config)
    {
        $this->type = $type;
        $this->config = $config;
    }

    /**
     * Getter for "data_type" attribute
     * @return string
     */
    public function getDataTypeAttribute(): string
    {
        return $this->config['data_type'];
    }

    /**
     * Getter for "name" attribute
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->config['name'];
    }

    /**
     * Getter for "extra" attribute
     * @return array|null
     */
    public function getExtraAttribute()
    {
        return $this->config['extra'];
    }

    /**
     * Getter for "allow_null" attribute
     * @return bool
     */
    public function getAllowNullAttribute(): bool
    {
        return $this->config['allow_null'];
    }

    /**
     * Getter for "index" attribute
     * @return string|null
     */
    public function getIndexAttribute()
    {
        return $this->config['index'];
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        return [
            'config' => $this->config,
            'type' => $this->type,
        ];
    }
}