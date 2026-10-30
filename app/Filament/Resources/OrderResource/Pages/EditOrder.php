<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected string $previousStatus = '';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Pesanan'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = User::current();

        if ($user && $user->isStoreAdmin()) {
            $data['store_id'] = $user->store_id;
        }

        $record = $this->record;
        $this->previousStatus = $record instanceof Order ? (string) $record->status : '';

        return $data;
    }

    protected function afterSave(): void
    {
        $order = $this->record;
        if (! $order instanceof Order) {
            return;
        }

        // Kembalikan stok jika status berubah menjadi CANCELLED
        if ($order->status === Order::STATUS_CANCELLED && $this->previousStatus !== Order::STATUS_CANCELLED) {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

