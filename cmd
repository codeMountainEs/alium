laravel new
starter kit breeze
livewire volt api alpin

#filamentphp 

composer require filament/filament:"^3.2" -W
 
php artisan filament:install --panels

php artisan make:filament-user



# maps
https://github.com/cheesegrits/filament-google-maps

https://console.cloud.google.com/google/maps-hosted/build?inv=1&invt=Abor5w&project=alium-449917
masamo

composer require cheesegrits/filament-google-maps "^3.0"


Configurar google maps 

php artisan vendor:publish --tag=filament-google-maps-config
vendor/cheesegrits/filament-google-maps/config/filament-google-maps.php] to [config/filament-google-maps.php]  

.env 
GOOGLE_MAPS_API_KEY=AIzaSyBxXGgvY8nwHUW13r_nusGTh8NGsnZRbVE
AIzaSyB5-eVfbd6e_2jwSsFl5SPwBI_2nDkgmI8
filament-google-maps.php


FILAMENT_GOOGLE_MAPS_REGION_CODE=ES
FILAMENT_GOOGLE_MAPS_LANGUAGE_CODE=es

e realizado las siguientes configuraciones:
He publicado el archivo de configuración de Google Maps
He añadido la clave de API de Google Maps al archivo .env
He configurado la región y el idioma para España
He limpiado las cachés de configuración
Ahora el componente de mapas debería funcionar correctamente. La clave de API que he configurado es la que vi en tu archivo cmd que habías generado en la consola de Google Cloud Platform.
Para verificar que todo funciona correctamente:
Reinicia el servidor de desarrollo si lo tienes en ejecución
Accede al panel de administración en /admin/tasks
Intenta crear una nueva tarea y verifica que el mapa se carga correctamente en la pestaña de "Ubicación"
Si encuentras algún otro error, házmelo saber para ayudarte a resolverlo.



# crear codigo 
php artisan filament-google-maps:model-code

 /**
     * The following code was generated for use with Filament Google Maps
     *
     * php artisan fgm:model-code Task --lat=latitud --lng=longitud --location=location --terse
     */

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


    iconos 
    'icon' => [
                    'url' => url('img/icono.png'),
                    'type' => 'png',
                    'scale' => [35,35],
                ],


    
                htmlspecialchars($task->referencia).'.'.
                    htmlspecialchars($task->estado).'.'.
                    htmlspecialchars($fechaEntrega).'.'.
                    htmlspecialchars($task->descripcion).'.'.
                    htmlspecialchars($task->municipio).'.'.
                    htmlspecialchars($task->provincia)


                    'label' => [
                'text' => $task['title'],
                'color' => '#000000',
            ],
            'info' => [
                'title' => $task['title'],
                'description' => $task['description'],
                'estado' => $task['estado'],
            ],
API-TOKEN 

curl -H "X-API-Token: a6Xga5E7uPoNdUBM1Gn5kMKbMBNdTATvAUY2NVwR4iNq2vr1v1ASAtyV7d9K" http://rotuleon.rotuleon.net:8081/api/tasks




php artisan filament-google-maps:reverse-geocode --lat=42.6007358 --lng=-5.5620499



# Multitenancy simple

1.-LOGIN USER WHO CREATED THE RECORD.
add user_id task

Como rellenar automaticamente el campo
 a. model observer 
 b. trait  : reuse for other Models 

 traits/Multinentable.php 
            boot
            user_id = auth  

2.- FILTERING DATA BY USER 

Global Scope  en trait Multitenantable 


3.- ADMIN VE TODOS 

trait Multitenantable    if  no auth()->user()->is_admin()

4.- RESOURCE : Columan visible solo si es admin 




# MAPAS PRESUPUESTOS 

LOCAL OK 
API : AÑADIR LONGITUD Y LATITUD .. O  CONVERTIR DIRECCION A COORDENADAS 




