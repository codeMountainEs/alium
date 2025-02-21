<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapWidget;

class TasksMapWidget1 extends MapWidget
{
    //protected static bool $isLivewire = true;

    
    
    // Añade estas propiedades para el ancho máximo
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Mapa de Tareas';

    //protected static bool $isLivewire = true;

    protected function getData(): array
    {
        $tasks = Task::all();

        return $tasks->map(function ($task) {
            $color = match($task->estado) {
                'abierto' => '#EF4444', // rojo
                'curso' => '#F59E0B',   // amarillo
                'finalizado' => '#10B981', // verde
                default => '#6B7280'     // gris
            };

            $fechaEntrega = $task->fecha_entrega 
                ? date('d/m/Y', strtotime($task->fecha_entrega))
                : 'No especificada';

            return [
                'location' => [
                    'lat' => (float) $task->latitud,
                    'lng' => (float) $task->longitud,
                ],
                'label' => 
                   
                    htmlspecialchars($task->referencia).'.'.
                    htmlspecialchars($task->estado).'.'.
                    htmlspecialchars($fechaEntrega).'.'.
                    htmlspecialchars($task->descripcion).'.'.
                    htmlspecialchars($task->municipio).'.'.
                    htmlspecialchars($task->provincia)
                ,
                'icon' => [
                    'path' => 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z',
                    'fillColor' => $color,
                    'fillOpacity' => 0.9,
                    'strokeWeight' => 1,
                    'strokeColor' => '#000000',
                    'scale' => 1.5,
                ],
            ];
        })->toArray();
    }

    protected function getOptions(): array
    {
        return [
            'zoom' => 6,
            'center' => [
                'lat' => 40.4168,
                'lng' => -3.7038,
            ],
        ];
    }
} 