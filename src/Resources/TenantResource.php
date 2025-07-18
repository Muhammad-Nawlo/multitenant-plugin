<?php

namespace MuhammadNawlo\MultitenantPlugin\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwareResource;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantResource extends Resource
{
    use TenantAwareResource;

    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    public static function getModel(): string
    {
        // Fallback if Tenant model doesn't exist
        if (! class_exists(Tenant::class)) {
            return \Illuminate\Database\Eloquent\Model::class;
        }

        return Tenant::class;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Tenant ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Tenant Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('domain')
                    ->label('Domain')
                    ->url()
                    ->maxLength(255),
                Forms\Components\Textarea::make('data')
                    ->label('Additional Data')
                    ->json()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Tenant ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tenant Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_domain')
                    ->label('Has Domain')
                    ->query(fn ($query) => $query->whereNotNull('domain')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('switch_tenant')
                    ->label('Switch to Tenant')
                    ->icon('heroicon-o-arrow-right')
                    ->action(function (Tenant $tenant) {
                        // Switch to tenant context
                        tenancy()->initialize($tenant);

                        return redirect()->back();
                    })
                    ->visible(fn () => ! tenancy()->initialized),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages\ListTenants::route('/'),
            'create' => \MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages\CreateTenant::route('/create'),
            'edit' => \MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages\EditTenant::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canViewAny(): bool
    {
        return true; // Allow access to everyone for testing
    }
}
