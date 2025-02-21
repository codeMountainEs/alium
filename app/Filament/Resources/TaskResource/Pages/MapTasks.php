<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Filament\Widgets\TasksMapWidget;
use Filament\Resources\Pages\Page;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapWidget;

class MapTasks extends Page
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.task-resource.pages.map-tasks';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Mapa de Tareas';

    public function getHeaderWidgets(): array
    {
        return [
            TasksMapWidget::class
        ];
    }
} 