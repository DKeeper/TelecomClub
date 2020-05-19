<?php

namespace model;

use components\DBwrapper;

/**
 * @package model
 */
abstract class Model
{
    /** @var DBwrapper */
    private $db;

    private $pk = 'id';

    private $errors = [];

    protected $fields = [];

    protected $tableName = '';

    /**
     * @var string[]
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param $db DBwrapper
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Model
     */
    public function addError(string $name, string $value): self
    {
        $this->errors[$name] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getPk(): string
    {
        return $this->pk;
    }

    /**
     * @param string $pk
     *
     * @return Model
     */
    public function setPk(string $pk): self
    {
        $this->pk = $pk;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return DBwrapper
     */
    public function getDb(): DBwrapper
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param $name
     *
     * @return string|null
     */
    public function getAttribute($name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Model
     */
    public function setAttribute(string $name, string $value = ''): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param array $attr
     *
     * @return Model
     */
    public function setAttributes(array $attr): self
    {
        $this->attributes = $attr;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function load(array $data = []): self
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $this->fields, true)) {
                $this->attributes[$name] = trim($value);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $this->errors = [];
        foreach ($this->rules as $attr => $rules) {
            foreach ($rules as $rule) {
                $err = null;

                switch ($rule['type']) {
                    case 'required':
                        $err = validateRequire($this->attributes[$attr], $rule);
                        break;
                    case 'regExp':
                        $err = validateRegExp($this->attributes[$attr], $rule);
                        break;
                    case 'length':
                        $err = validate($this->attributes[$attr], $rule);
                        break;
                    case 'file':
                        $err = validateFile($this->attributes[$attr], $rule);
                        break;
                    default:
                }

                if (!is_null($err)) {
                    $this->errors[$attr] = $err;
                    break;
                }
            }
        }

        return empty($this->errors);
    }
}
