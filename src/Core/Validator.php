<?php
// src/Core/Validator.php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function required(string $field, $value): self
    {
        if (empty($value)) {
            $this->errors[$field] = ucfirst($field) . ' est obligatoire.';
        }
        return $this;
    }

    public function maxLength(string $field, string $value, int $max): self
    {
        if (strlen($value) > $max) {
            $this->errors[$field] = ucfirst($field) . " ne doit pas dépasser $max caractères.";
        }
        return $this;
    }

    public function numeric(string $field, $value): self
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = ucfirst($field) . ' doit être un nombre.';
        }
        return $this;
    }

    public function positive(string $field, $value): self
    {
        if ((int)$value <= 0) {
            $this->errors[$field] = ucfirst($field) . ' doit être un nombre positif.';
        }
        return $this;
    }

    public function exists(string $field, $value, callable $callback): self
    {
        if (!empty($value) && !$callback((int)$value)) {
            $this->errors[$field] = ucfirst($field) . " sélectionné(e) n'existe pas.";
        }
        return $this;
    }

    public function email(string $field, $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = ucfirst($field) . ' doit être une adresse email valide.';
        }
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
