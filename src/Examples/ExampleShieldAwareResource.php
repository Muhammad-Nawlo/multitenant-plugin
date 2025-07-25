<?php

namespace MuhammadNawlo\MultitenantPlugin\Examples;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwareResource;

/**
 * Example resource demonstrating tenant permissions
 *
 * This resource shows how to use TenantAwareResource trait
 * to combine tenancy with Spatie Laravel Permission.
 */
class ExampleTenantAwareResource extends Resource
{
    use TenantAwareResource;

    protected static ?string $model = \App\Models\Example::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Examples';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->label('Content')
                    ->required()
                    ->maxLength(1000),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Under Review',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\BooleanColumn::make('is_published'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn () => static::canView(null)),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => static::canEdit(null)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => static::canDelete(null)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::canDelete(null)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamples::route('/'),
            'create' => Pages\CreateExample::route('/create'),
            'view' => Pages\ViewExample::route('/{record}'),
            'edit' => Pages\EditExample::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Override to add custom permission logic
     */
    public static function canViewAny(): bool
    {
        $instance = new static;

        // Check if user has permission to view any records
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();

            return auth()->user()->can('view_any_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }

        return auth()->user()->can('view_any_' . static::getSlug());
    }

    /**
     * Override to add custom permission logic for viewing specific records
     */
    public static function canView($record): bool
    {
        $instance = new static;

        // Check if user has permission to view this specific record
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();

            return auth()->user()->can('view_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }

        return auth()->user()->can('view_' . static::getSlug());
    }

    /**
     * Override to add custom permission logic for creating records
     */
    public static function canCreate(): bool
    {
        $instance = new static;

        // Check if user has permission to create records
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();

            return auth()->user()->can('create_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }

        return auth()->user()->can('create_' . static::getSlug());
    }

    /**
     * Override to add custom permission logic for editing records
     */
    public static function canEdit($record): bool
    {
        $instance = new static;

        // Check if user has permission to edit this specific record
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();

            return auth()->user()->can('update_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }

        return auth()->user()->can('update_' . static::getSlug());
    }

    /**
     * Override to add custom permission logic for deleting records
     */
    public static function canDelete($record): bool
    {
        $instance = new static;

        // Check if user has permission to delete this specific record
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();

            return auth()->user()->can('delete_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }

        return auth()->user()->can('delete_' . static::getSlug());
    }
}
