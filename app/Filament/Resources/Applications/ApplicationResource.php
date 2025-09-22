<?php

namespace App\Filament\Resources\Applications;

use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Filament\Resources\Applications\Tables\ApplicationsTable;
use App\Models\Application;
use BackedEnum;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ServerStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return ApplicationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplications::route('/'),
            'view' => ViewApplication::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Infolists\Components\TextEntry::make('name')
                    ->label('Application Name')
                    ->icon(Heroicon::Hashtag)
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('compose_file')
                    ->label('Compose File Path')
                    ->icon(Heroicon::DocumentText)
                    ->copyable()
                    ->copyMessage('Copied to clipboard')
                    ->copyMessageDuration(1500)
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->icon(Heroicon::Tag)
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'running' => 'success',
                        'stopped' => 'danger',
                        'exited' => 'warning',
                        'created' => 'info',
                        default => 'gray',
                    })
                    ->columnSpanFull(),
                Infolists\Components\RepeatableEntry::make('services')
                    ->label('Services')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Service Name')
                            ->icon(Heroicon::Hashtag)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('image')
                            ->label('Image')
                            ->icon(Heroicon::Cube)
                            ->formatStateUsing(fn(string $state): HtmlString => match ($state) {
                                'custom-build' => new HtmlString('<span class="italic text-gray-500">custom-build</span>'),
                                default => new HtmlString(
                                    sprintf(
                                        '%1$s <a href="https://hub.docker.com/r/%1$s" target="_blank" class="%2$s"><small>➜ DockerHub</small></a>',
                                        $state,
                                        'underline text-primary-600 hover:text-primary-700 visited:text-primary-600'
                                    )
                                ),
                            })
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('tag')
                            ->label('tag')
                            ->icon(Heroicon::Hashtag)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
