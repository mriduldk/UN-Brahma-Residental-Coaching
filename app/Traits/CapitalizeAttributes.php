<?php

namespace App\Traits;

trait CapitalizeAttributes
{
    /**
     * Boot the trait to listen for saving events.
     */
    protected static function bootCapitalizeAttributes()
    {
        static::saving(function ($model) {
            $except = [
                'email',
                'password',
                'remember_token',
                'profile_photo_path',
                'signature_path',
                'file_path',
                'url',
                'slug',
                'token',
                'created_at',
                'updated_at',
                'email_verified_at',
                'user_id',
                'student_id',
                'application_form_id',
                'student_application_id',
                'id',
                // Add other specific fields to exclude
            ];

            // Add model-specific exceptions if defined
            if (property_exists($model, 'skipCapitalization')) {
                $except = array_merge($except, $model->skipCapitalization);
            }

            foreach ($model->getAttributes() as $key => $value) {
                if (in_array($key, $except)) {
                    continue;
                }

                if (is_string($value)) {
                    $model->{$key} = strtoupper($value);
                }
            }
        });
    }
}
