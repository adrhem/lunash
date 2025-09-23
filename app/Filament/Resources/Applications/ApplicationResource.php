<?php

namespace App\Filament\Resources\Applications;

use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Filament\Resources\Applications\Tables\ApplicationsTable;
use App\Models\Application;
use BackedEnum;
use Filament\Infolists;
use Filament\Infolists\View\InfolistsIconAlias;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use stdClass;

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
                        // 'image_id', 'name', 'repository', 'tag', 'platform', 'size',
                        Infolists\Components\TextEntry::make('image_id')
                            ->label('Image ID')
                            ->icon(Heroicon::Identification)
                            ->copyable()
                            ->copyMessage('Copied to clipboard')
                            ->copyMessageDuration(1500)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Service Name')
                            ->icon(Heroicon::Hashtag)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('repository_json')
                            ->label('Image')
                            ->icon(Heroicon::Cube)
                            ->formatStateUsing(function (string $state): HtmlString {
                                $state = json_decode($state, true);
                                return match ($state['label']) {
                                    'custom-build', null => new HtmlString('<span class="italic text-gray-500">custom-build</span>'),
                                    default => new HtmlString(
                                        sprintf(
                                            '%1$s <a href="%2$s" target="_blank" class="%3$s"><small>➜ DockerHub</small></a>',
                                            $state['label'],
                                            $state['url'],
                                            'underline text-primary-600 hover:text-primary-700 visited:text-primary-600'
                                        )
                                    ),
                                };
                            })
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('tag')
                            ->label('tag')
                            ->icon(Heroicon::Hashtag)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('platform')
                            ->label('Platform')
                            ->icon(Heroicon::CubeTransparent)
                            ->visible(fn(?string $state): bool => !empty($state))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('size')
                            ->label('Size')
                            ->icon(Heroicon::ArrowsPointingOut)
                            ->formatStateUsing(fn(?int $state): string => $state ? sprintf('%.2f MB', $state / (1024 * 1024)) : 'N/A')
                            ->visible(fn(?string $state): bool => !empty($state))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
