<?php

namespace AppBundle;

use As3\Modlr\Models\Model;

class CalculatedFields
{
    /**
     * Calculates the primary address field for customer models.
     *
     * @param   Model   $model
     * @return  string|null
     */
    public static function customerPrimaryAddress(Model $model)
    {
        $buildAddress = function(Model $model) {
            $fields = ['name', 'companyName', 'street', 'extra', 'city', 'regionCode', 'postalCode', 'countryCode'];
            $object = ['_id'   => $model->getId()];
            foreach ($fields as $key) {
                $object[$key] = $model->get($key);
            }
            return $object;
        };

        $primary = null;
        foreach ($model->get('addresses') as $address) {

            if (null === $primary) {
                // Use first address as primary, as a default.
                $primary = $buildAddress($address);
            }
            if (true === $address->get('isPrimaryMailing')) {
                $primary = $buildAddress($address);
                break;
            }
        }
        return $primary;
    }

    /**
     * Calculates the primary email field for a customer account model.
     *
     * @param   Model   $model
     * @return  string|null
     */
    public static function customerAccountPrimaryEmail(Model $model)
    {
        $primary = null;

        // Try verified emails first.
        foreach ($model->get('emails') as $email) {
            if (false === $email->get('verified')) {
                continue;
            }
            if (null === $primary) {
                // Use first email as primary, as a default.
                $primary = $email->get('value');
            }
            if (true === $email->get('isPrimary')) {
                $primary = $email->get('value');
                break;
            }
        }
        if (!empty($primary)) {
            return $primary;
        }

        // Try again with non-verified.
        foreach ($model->get('emails') as $email) {
            if (null === $primary) {
                // Use first email as primary, as a default.
                $primary = $email->get('value');
            }
            if (true === $email->get('isPrimary')) {
                $primary = $email->get('value');
                break;
            }
        }

        return $primary;
    }

    /**
     * Calculates the primary email field for customer models.
     *
     * @param   Model   $model
     * @return  string|null
     */
    public static function customerPrimaryPhone(Model $model)
    {
        $primary = null;
        foreach ($model->get('phones') as $phone) {
            $type = $phone->get('phoneType');
            if ('fax' === strtolower($type)) {
                continue;
            }
            if (null === $primary) {
                // Use first phone as primary, as a default.
                $primary = ['number' => $phone->get('number'), 'phoneType' => $phone->get('phoneType')];
            }
            if (true === $phone->get('isPrimary')) {
                $primary = ['number' => $phone->get('number'), 'phoneType' => $phone->get('phoneType')];
                break;
            }
        }
        return $primary;
    }

    /**
     * Calculates the username field for a customer account model.
     *
     * @param   Model   $model
     * @return  string|null
     */
    public static function customerAccountUsername(Model $model)
    {
        $credentials = $model->get('credentials');
        if (null === $credentials || null === $password = $credentials->get('password')) {
            return;
        }
        return $password->get('username');
    }
}