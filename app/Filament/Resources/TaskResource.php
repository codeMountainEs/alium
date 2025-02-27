<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Gestión de Tareas';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Tareas';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();
        
        if (!auth()->user()->is_admin) {
            $query->where('empresa_id', auth()->user()->empresa_id);
        }
        
        return $query->count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('empresa_id')
                ->default(fn () => auth()->user()->empresa_id),
            Tabs::make('Detalles de la Tarea')->tabs([
                Tabs\Tab::make('Información Básica')
                    ->icon('heroicon-o-information-circle')
            ->schema([
                        Section::make()->schema([
                            Select::make('empresa_id')
                            ->label('Empresa')
                            ->relationship('empresa', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn () => auth()->user()->is_admin),

                            Forms\Components\TextInput::make('titulo')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan('full'),
                            Forms\Components\TextInput::make('referencia')                             
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            Forms\Components\TextInput::make('empresa')
                                ->maxLength(255),
                            Forms\Components\Select::make('estado')
                                ->required()
                            ->options([
                                'abierto' => 'Abierto',
                                'curso' => 'En Curso',
                                'finalizado' => 'Finalizado',
                            ])
                            ->default('abierto'),
                            Forms\Components\DatePicker::make('fecha_prevista')
                                ->native(false),
                            Forms\Components\DatePicker::make('fecha_finalizado')
                                ->native(false)
                                ->visible(fn (Forms\Get $get) => $get('estado') === 'finalizado'),
                            Forms\Components\TextInput::make('precio')
                                ->numeric()
                                ->prefix('€')
                                ->maxValue(999999.99),
                            Forms\Components\RichEditor::make('descripcion')
                                ->columnSpan('full'),
                        ])->columns(2),
                    ]),

                Tabs\Tab::make('Ubicación')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Section::make()->schema([
                            Map::make('location')
                               // ->defaultLocation([40.4168, -3.7038]) // Madrid
                                ->defaultZoom(6)
                                ->autocomplete('direccion')
                                ->autocompleteReverse(true)
                                ->geolocate()
                                ->geolocateLabel('Mi ubicación')
                                ->draggable()
                                ->clickable()
                                ->defaultLocation([40.4168, -3.7038])
                                ->columnSpan('full')
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if (isset($state['lat']) && isset($state['lng'])) {
                                        $set('latitud', $state['lat']);
                                        $set('longitud', $state['lng']);
                                    }
                                }),
                            Forms\Components\TextInput::make('provincia'),
                            Forms\Components\TextInput::make('localidad'),
                            Forms\Components\TextInput::make('direccion')
                                
                                ->columnSpan('full'),
                            Forms\Components\TextInput::make('postal')
                                ,
                            Forms\Components\TextInput::make('pais')
                    ->default('España')
                                ,
                            Forms\Components\TextInput::make('latitud')
                                ->numeric()
                                ->disabled(),
                            Forms\Components\TextInput::make('longitud')
                                ->numeric()
                                ->disabled(),
                        ])->columns(2),
                    ]),

                Tabs\Tab::make('Contacto')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Section::make()->schema([
                            Forms\Components\TextInput::make('telefono')
                    ->tel()
                                ,
                            Forms\Components\TextInput::make('email')
                    ->email()
                                ,
                        ])->columns(2),
                    ]),

                Tabs\Tab::make('Archivos')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Section::make()->schema([
                            Forms\Components\FileUpload::make('fotos')
                                ->multiple()
                                ->image()
                                ->imageEditor()
                                ->directory('tareas/fotos')
                                ->columnSpan('full'),
                            Forms\Components\FileUpload::make('documentos')
                                ->multiple()
                                ->acceptedFileTypes(['application/pdf'])
                                ->directory('tareas/documentos')
                                ->columnSpan('full'),
                        ]),
                    ]),
            ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empresa.nombre')
                ->label('Empresa')
                ->searchable()
                ->sortable()
                ->toggleable()
                ->visible(fn () => auth()->user()->is_admin),

                Tables\Columns\TextColumn::make('referencia')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
              
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'danger' => 'abierto',
                        'warning' => 'curso',
                        'success' => 'finalizado',
                    ]),
                Tables\Columns\TextColumn::make('fecha_prevista')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provincia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('localidad')
                    ->searchable(),
               
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'curso' => 'En Curso',
                        'finalizado' => 'Finalizado',
                    ]),
                   ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'map' => Pages\MapTasks::route('/map'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->is_admin) {
            $query->where('empresa_id', auth()->user()->empresa_id);
        }

        return $query;
    }
}
