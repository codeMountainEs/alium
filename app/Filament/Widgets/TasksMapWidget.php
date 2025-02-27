<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapWidget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Añade esta línea

class TasksMapWidget extends MapWidget
{
    protected static bool $isLivewire = true;
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Mapa de Tareas';

    protected function getData(): array
    {
        // Obtener tareas locales
        $localTasks = $this->getLocalTasks();
        
        // Obtener tareas de la API externa
        $apiTasks = $this->getApiTasks();


        Log::info('GET DATA localTasks', $localTasks,$apiTasks);

        Log::info('GET DATA apiTasks',$apiTasks);

        // Combinar ambos arrays
        //dd(array_merge( $apiTasks));
        return array_merge($localTasks, $apiTasks);

    }

    protected function getLocalTasks(): array
    {        Log::info('Iniciando petición a la getLocalTasks');

        return Task::all()->map(function ($task) {
            return $this->formatTask($task, 'local');
        })->toArray();
    }

    protected function getApiTasks(): array
    {
        try {
            Log::info('1 Iniciando petición a la API');

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'http://rotuleon.rotuleon.net:8081/api/tasks', [
                'headers' => [
                    'X-API-Token' => config('services.api.token')
                ]
            ]);
            //$coordinates = $this->generateRandomCoordinates();

            Log::info('2 Respuesta de la API', [
                //'coordinates' => $coordinates,
                //'status' => $response->getStatusCode()===200,
                //'body' => json_decode($response->getBody(), true)
            ]);
//{"dataa":{"id":4659,"title":"MERCADONA, S.A.","description":"MERCADONA 4639 IURRETA VIZCAYA","lat":"40,98887565","lng":"-5,67103938","status":"ACEPTADO","reference":null}} 

            if ($response->getStatusCode() === 200) {

                $data = json_decode($response->getBody(), true);

                Log::info('3 200 si collect', $data);

                return collect($data['data'])->map(function ($presupuesto,$coordinates) {

                    Log::info('3 200 si collect if ', [
                       
                         //'data' => $data['data'],
                     ]);
                   

                    if($presupuesto['latitud'] == null || $presupuesto['longitud'] == null){
                        
                        Log::info('4 200 si ', [
                            '$presupuesto[latitud] == null' => $presupuesto
                             //'data' => $data['data'],
                         ]);
                          // Componemos la dirección completa
                            $direccionCompleta = $this->formatearDireccion(
                                $presupuesto['direccion'] ?? '',
                                $presupuesto['codigo_postal'] ?? '',
                                $presupuesto['poblacion'] ?? '',
                                $presupuesto['provincia'] ?? ''
                            );
                  
                        //$coordinates = $this->geocodeAddress($direccionCompleta);
                    }else{

                       $presupuesto['texto'] = htmlspecialchars($presupuesto['titulo']).'-'.
                        htmlspecialchars($presupuesto['descripcion']);
                        

                        $coordinates = [
                            'lat' => floatval($presupuesto['latitud']),
                            'lng' => floatval($presupuesto['longitud']),
                        ];
                        Log::info('5 200 si ', [
                            'coordinates' => $coordinates,
                            'texto' => $presupuesto['texto'],
                             //'data' => $data['data'],
                         ]);
                    }

                 


                    return [
                      'location' => [
                        'lat' => $coordinates['lat'],
                        'lng' => $coordinates['lng'],
                      ],
                        'label' => 
                        htmlspecialchars($presupuesto['texto'])

                            
                        ,
                        'icon' => [
                            'path' => 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z',
                            'fillColor' => '#10B981', // Verde para presupuestos aceptados
                            'fillOpacity' => 0.9,
                            'strokeWeight' => 1,
                            'strokeColor' => '#000000',
                            'scale' => 1.5,
                        ],
                    ];
                })->toArray();

                Log::info('6 200 si ', [
                    'fin' => $data['data'],
                    
                ]);
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching API tasks: ' . $e->getMessage());
            return [];
        }
    }

    

  /*  protected function getApiTasksSinToken(): array
    {
        try {
            // Reemplaza esta URL con tu API real
            $response = Http::get('https://tu-api-externa.com/tasks');
            
            if ($response->successful()) {
                return collect($response->json())->map(function ($task) {
                    return $this->formatTask($task, 'api');
                })->toArray();
            }
            
            return [];
        } catch (\Exception $e) {
            // Log del error si es necesario
            return [];
        }
    }*/



    /**
     * Geocodifica una dirección para obtener sus coordenadas
     */
    protected function geocodeAddress(?string $direccion): ?array
    {
        try {
            if (empty($direccion)) {
                Log::warning('Dirección vacía, no se puede geocodificar');
                //return null;
                return $this->generateRandomCoordinates();
            }

            // Asegurarse de que la dirección incluya España
            if (!str_contains(strtolower($direccion), 'españa')) {
                $direccion .= ', España';
            }

            Log::info('Geocodificando dirección: ' . $direccion);

            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $direccion,
                //'key' => config('services.google.maps_api_key'),
                'key' => env('GOOGLE_MAPS_API_KEY'), // Usamos la variable del .env
                'region' => 'es'
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];

               
                
                Log::info('Geocodificación exitosa', [
                    'direccion' => $direccion,
                    'coordenadas' => $location
                ]);

                return [
                    'lat' => (float) $location['lat'],
                    'lng' => (float) $location['lng']
                ];
            }

            Log::warning('No se pudieron obtener coordenadas para: ' . $direccion, [
                'status' => $data['status'] ?? 'Unknown'
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Error en geocodificación: ' . $e->getMessage(), [
                'direccion' => $direccion
            ]);
            return null;
        }
    }


/**
 * Formatea la dirección completa a partir de sus componentes
 */
protected function formatearDireccion(?string $direccion, ?string $codigoPostal, ?string $poblacion, ?string $provincia): string
{
    $componentes = array_filter([
        $direccion,
        $codigoPostal,
        $poblacion,
        $provincia,
        'España' // Añadimos España al final
    ], function($valor) {
        return !empty(trim($valor));
    });

    Log::info('Componentes de dirección:', [
        'direccion' => $direccion,
        'cp' => $codigoPostal,
        'poblacion' => $poblacion,
        'provincia' => $provincia,
        'resultado' => implode(', ', $componentes)
    ]);

    return implode(', ', $componentes);
}

    /**
     * Genera coordenadas aleatorias dentro de un rango específico
     * 
     * @return array
     */
    protected function generateRandomCoordinates(): array
    {
        // Generamos un número aleatorio con 4 decimales
        $lat = number_format(rand(400000, 409999) / 10000, 4);
        $lng = number_format(rand(-50000, -59999) / 10000, 4);

        Log::info('Coordenadas generadas:', [
            'lat' => $lat,
            'lng' => $lng
        ]);

        return [
            'lat' => (float) $lat,
            'lng' => (float) $lng
        ];
    }




    protected function formatTask($task, string $source): array
    {
        // Diferentes iconos según la fuente
        $iconPath = $source === 'local' 
            ? 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z'  // Marcador estándar
            : 'M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2z';        // Círculo para API

        // Diferentes colores según el estado y la fuente
        $color = $this->getTaskColor($task, $source);

        // Formatear fecha según la fuente
        $fechaEntrega = $this->formatDate($task, $source);

        // Generar URL según la fuente
        $viewUrl = $source === 'local' 
            ? route('filament.admin.resources.tasks.edit', ['record' => $task->id])
            : '#'; // O la URL correspondiente para tareas de API

        return [
            'location' => [
                'lat' => (float) ($source === 'local' ? $task->latitud : $task->lat),
                'lng' => (float) ($source === 'local' ? $task->longitud : $task->lng),
            ],
            'label' => 
                $task['titulo'],
            
            'icon' => [
                'path' => $iconPath,
                'fillColor' => $color,
                'fillOpacity' => 0.9,
                'strokeWeight' => 1,
                'strokeColor' => '#000000',
                'scale' => 1.5,
            ],
        ];
    }

    protected function getTaskColor($task, string $source): string
    {
        if ($source === 'local') {
            return match($task->estado) {
                'abierto' => '#EF4444',    // rojo
                'curso' => '#F59E0B',      // amarillo
                'finalizado' => '#10B981', // verde
                default => '#6B7280'       // gris
            };
        }

        // Ajusta esto según los estados de tu API
        return match($task->estado) {
            'FINALIZADO' => '#EF4444',      // rojo
            //'en' => '#F59E0B', // amarillo
            'ACEPTADO' => '#10B981',   // verde
            default => '#6B7280'        // gris
        };
    }

    protected function formatDate($task, string $source): string
    {
        if ($source === 'local') {
            return $task->fecha_entrega 
                ? date('d/m/Y', strtotime($task->fecha_entrega))
                : 'No especificada local';
        }

        // Ajusta esto según el formato de fecha de tu API
        return $task->fecha_entrega 
            ? date('d/m/Y', strtotime($task->fecha_entrega))
            : 'No especificada ';
    }

    protected function getOptions(): array
    {
        return [
            'zoom' => 6,
            'center' => [
                'lat' => 40.4168,
                'lng' => -3.7038,
            ],
            'markers' => [
                'clickable' => true,
            ],
            'mapTypeId' => 'roadmap',
        ];
    }
} 