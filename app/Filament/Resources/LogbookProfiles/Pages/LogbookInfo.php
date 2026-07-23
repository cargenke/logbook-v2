<?php

namespace App\Filament\Resources\LogbookProfiles\Pages;

use App\Enums\LogBookStatusEnum;
use App\Filament\Resources\LogbookProfiles\LogbookProfileResource;
use App\Models\Logbook;
use App\Models\LogbookProfile;
use App\Models\LogbookRequest;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LogbookInfo extends Page
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = LogbookProfileResource::class;

    public ?array $data = [];

    public bool $canEdit = true;

    protected string $view = 'filament.resources.logbook-profiles.pages.logbook-info';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill([
            'ChasisNumber' => $this->record->chasisNumber,
            'RegNumber' => $this->record->regNumber,
            'LogBookFee' => $this->record->LogBookFee,
            'name1' => $this->record->CustomerName,
            'PinNo1' => $this->record->PinNo,
            'modeofpayment' => 'POI',
            'tel1' => $this->record->tel,
        ]);
        $logBookRequest = LogbookRequest::where('chasisNumber', $this->record->chasisNumber)
            ->first();

        if ($logBookRequest) {

            $this->canEdit = false;
            $this->form->fill([
                'ChasisNumber' => $this->record->chasisNumber,
                'RegNumber' => $this->record->regNumber,
                'LogBookFee' => $this->record->LogBookFee,
                'name1' => $logBookRequest?->name1 ?? $this->record->name1,
                'tel1' => $logBookRequest?->tel1,
                'tel2' => $logBookRequest?->tel2,
                'email' => $logBookRequest?->email,
                'PinNo1' => $logBookRequest?->PinNo1,
                'modeofpayment' => $logBookRequest?->modeofpayment,
                'name2' => $logBookRequest?->name2,
                'PinNo2' => $logBookRequest?->PinNo2,
                'PinNo3' => $logBookRequest?->PinNo3,
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {

        return $schema
            ->statePath('data')
            ->schema([

                Section::make('Logbook Profile')
                    ->schema([
                        TextInput::make('ChasisNumber')
                            ->readOnly(! $this->canEdit)
                            ->label('Chasis Number')
                            ->required(),

                        TextInput::make('RegNumber')
                            ->readOnly(! $this->canEdit)
                            ->label('Reg Number')
                            ->required(),

                        TextInput::make('LogBookFee')
                            ->readOnly(! $this->canEdit)
                            ->label('Logbook Fee')
                            ->required(),
                    ])->columns(3),

                Section::make('Logbook Request Details')
                    ->schema([

                        Section::make('First Owner Details')
                            ->schema([
                                TextInput::make('name1')
                                    ->label('Name')
                                    ->readOnly(! $this->canEdit)
                                    ->required(),

                                TextInput::make('tel1')
                                    ->label('Phone Number 1')
                                    ->readOnly(! $this->canEdit)
                                    ->required(),

                                TextInput::make('tel2')
                                    ->label('Phone Number 2')
                                    ->readOnly(! $this->canEdit)
                                    ->rules([
                                        'regex:/^\+\d{12}$/',
                                        'different:tel1',
                                    ])
                                    ->required(),

                                TextInput::make('PinNo1')
                                    ->label('KRA Pin No 1')
                                    ->readOnly(! $this->canEdit)
                                    ->rules([
                                        'string',
                                        'regex:/^[A-Za-z]\d{9}[A-Za-z]$/',
                                    ])
                                    ->required(),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->readOnly(! $this->canEdit)
                                    ->rules([
                                        'string',
                                        'regex:/^[A-Za-z]\d{9}[A-Za-z]$/',
                                    ])
                                    ->required(),

                                TextInput::make('modeofpayment')
                                    ->label('Mode of Payment'),
                            ])->columns(3),

                        Section::make('Other Owners Details')
                            ->schema([
                                TextInput::make('name2')
                                    ->readOnly(! $this->canEdit)
                                    ->label('Name'),

                                TextInput::make('PinNo2')
                                    ->readOnly(! $this->canEdit)
                                    ->label('KRA Pin No 2'),

                                TextInput::make('PinNo3')
                                    ->readOnly(! $this->canEdit)
                                    ->label('PIN Number 3'),

                            ])->columns(3),

                    ]),

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->visible($this->canEdit)
                ->action('save')
                ->requiresConfirmation(false),
        ];
    }

    public function save(): void
    {

        DB::beginTransaction();
        try {

            $data = $this->data;

            $logBookInfo = Logbook::where('id', $this->record->logbook_id)->first();
            $logBookInfo->update([
                'status' => LogBookStatusEnum::PROCESSING,
            ]);

            $request = LogbookRequest::firstOrCreate(
                [
                    'chasisNumber' => $data['ChasisNumber'],
                ],
                [
                    'logbook_id' => $this->record->logbook_id,
                    'regNumber' => $data['RegNumber'],
                    'name1' => $data['name1'],
                    'name2' => $data['name2'],
                    'email' => $data['email'],
                    'ntsaApplicationNumber' => Str::upper(Str::random(8)),
                    'tel1' => $data['tel1'],
                    'tel2' => $data['tel2'],
                    'PinNo1' => $data['PinNo1'],
                    'PinNo2' => $data['PinNo2'],
                    'PinNo3' => $data['PinNo3'],
                    'createdOn' => Carbon::now(),
                    'status' => LogBookStatusEnum::PROCESSING,
                    'createdBy' => Auth::user()->id,
                ]
            );

            $logoobookProfile = LogbookProfile::where('chasisNumber', $this->record->chasisNumber)->first();
            $logoobookProfile->update([
                'status' => LogBookStatusEnum::PROCESSING,
                'applicationNumber' => $request->ntsaApplicationNumber,
            ]);

            if ($logoobookProfile->DocDate >= '2026-07-01') {
                $request->update([
                    'is_instant_transfer' => true,
                ]);
            }

            Notification::make()
                ->success()
                ->title('Created Successfully')
                ->send();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            Notification::make()
                ->warning()
                ->title($th->getMessage())
                ->send();
        }
    }
}
