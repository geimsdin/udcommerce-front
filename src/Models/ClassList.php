<?php

namespace Unusualdope\FrontLaravelEcommerce\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class ClassList extends Model
{
    // use Cachable;
    protected $table = 'class_list';

    protected $fillable = [
        'name',
        'fqcn',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only front controllers
     */
    public function scopeFrontControllers($query)
    {
        return $query->where('type', 'front_controller')->where('is_active', true);
    }

    /**
     * Scope to get only payment gateways
     */
    public function scopePaymentGateways($query)
    {
        return $query->where('type', 'payment_gateway')->where('is_active', true);
    }

    /**
     * Register or update a class in the list
     */
    public static function register(string $name, string $fqcn, string $type = 'front_controller'): self
    {
        return self::updateOrCreate(
            ['fqcn' => $fqcn],
            [
                'name' => $name,
                'type' => $type,
                'is_active' => true,
            ]
        );
    }
}
