<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'referencia',
        'fotos',
        'documentos',
        'precio',
        'descripcion',
        'estado',
        'fecha_prevista',
        'fecha_finalizado',
        'provincia',
        'localidad',
        'direccion',
        'postal',
        'pais',
        'latitud',
        'longitud',
        'telefono',
        'email',
        'location',
    ];

    protected $casts = [
        'fotos' => 'array',
        'documentos' => 'array',
        'precio' => 'decimal:2',
        'fecha_prevista' => 'date',
        'fecha_finalizado' => 'date',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];
    protected $appends = [
        'location',
    ];

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->latitud,
            "lng" => (float)$this->longitud,
        ];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['latitud'] = $location['lat'];
            $this->attributes['longitud'] = $location['lng'];
            unset($this->attributes['location']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitud',
            'lng' => 'longitud',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'location';
    }






}
