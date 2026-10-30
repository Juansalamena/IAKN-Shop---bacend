<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::current();

        if ($user && $user->isStoreAdmin()) {
            $data['store_id'] = $user->store_id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $order = $this->record;
        if (! $order instanceof Order) {
            return;
        }

        $total = 0;
        foreach ($order->items as $item) {
            if (empty($item->product_name) && $item->product_id) {
                $prod = Product::find($item->product_id);
                if ($prod) {
                    $item->product_name = $prod->name;
                    $item->save();
                }
            }
            $total += (float) $item->subtotal;
        }

        if ($total > 0) {
            $order->update(['total_amount' => $total]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

