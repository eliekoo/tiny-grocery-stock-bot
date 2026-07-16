<?php

namespace App\Services;

use App\Enums\MovementType;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        protected ProductSearchService $searchService
    ) {}


    public function addStock(
        string $keyword,
        float $quantity,
        ?string $remarks = null
    ) {
        $variant = $this->searchService->search($keyword);


        if (!$variant) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        if ($quantity <= 0) {

            return [
                'success' => false,
                'message' => 'Quantity must be greater than zero',
            ];
        }


        return DB::transaction(function () use (
            $variant,
            $quantity,
            $remarks
        ) {

            $inventory = Inventory::firstOrCreate(
                [
                    'variant_id' => $variant->id
                ],
                [
                    'quantity' => 0
                ]
            );


            $inventory->quantity += $quantity;

            $inventory->save();


            $movement = StockMovement::create([
                'variant_id' => $variant->id,
                'type' => MovementType::ADD->value,
                'quantity' => $quantity,
                'balance_after' => $inventory->quantity,
                'remarks' => $remarks,
            ]);


            return [
                'success' => true,
                'message' => 'Stock added successfully',
                'variant' => $variant,
                'quantity_added' => $quantity,
                'current_stock' => $inventory->quantity,
                'movement' => $movement,
            ];
        });
    }

    public function useStock(
        string $keyword,
        float $quantity,
        ?string $remarks = null
    ) {

        $variant = $this->searchService->search($keyword);


        if (!$variant) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        if ($quantity <= 0) {

            return [
                'success' => false,
                'message' => 'Quantity must be greater than zero',
            ];
        }


        return DB::transaction(function () use (
            $variant,
            $quantity,
            $remarks
        ) {

            $inventory = Inventory::firstOrCreate(
                [
                    'variant_id' => $variant->id
                ],
                [
                    'quantity' => 0
                ]
            );


            if ($inventory->quantity < $quantity) {

                return [
                    'success' => false,
                    'message' => 'Not enough stock',
                    'current_stock' => $inventory->quantity,
                ];
            }


            $inventory->quantity -= $quantity;

            $inventory->save();


            $movement = StockMovement::create([
                'variant_id' => $variant->id,
                'type' => MovementType::USE->value,
                'quantity' => $quantity,
                'balance_after' => $inventory->quantity,
                'remarks' => $remarks,
            ]);


            return [
                'success' => true,
                'message' => 'Stock used successfully',
                'variant' => $variant,
                'quantity_used' => $quantity,
                'current_stock' => $inventory->quantity,
                'movement' => $movement,
            ];
        });
    }

    public function listInventory()
    {
        return Inventory::query()
            ->with('variant.product')
            ->orderBy('variant_id')
            ->get()
            ->map(function ($inventory) {

                return [
                    'id' => $inventory->id,

                    'product' => $inventory
                        ->variant
                        ->product
                        ->name,

                    'variant' => $inventory
                        ->variant
                        ->name,

                    'quantity' => $inventory->quantity,

                    'unit' => $inventory
                        ->variant
                        ->unit,

                    'minimum_stock' => $inventory
                        ->variant
                        ->minimum_stock,
                ];
            });
    }

    public function adjustStock(
        string $keyword,
        float $newQuantity,
        ?string $remarks = null
    ) {

        $variant = $this->searchService->search($keyword);


        if (!$variant) {

            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }


        return DB::transaction(function () use (
            $variant,
            $newQuantity,
            $remarks
        ) {


            $inventory = Inventory::firstOrCreate(
                [
                    'variant_id' => $variant->id
                ],
                [
                    'quantity' => 0
                ]
            );


            $oldQuantity = $inventory->quantity;


            $difference = $newQuantity - $oldQuantity;


            $inventory->quantity = $newQuantity;

            $inventory->save();



            $movement = StockMovement::create([

                'variant_id' => $variant->id,

                'type' => MovementType::ADJUST->value,

                // store difference, not new total
                'quantity' => $difference,

                'balance_after' => $inventory->quantity,

                'remarks' => $remarks,

            ]);



            return [

                'success' => true,

                'message' => 'Stock adjusted successfully',

                'variant' => $variant,

                'old_quantity' => $oldQuantity,

                'new_quantity' => $newQuantity,

                'difference' => $difference,

                'current_stock' => $inventory->quantity,

                'movement' => $movement,

            ];
        });
    }
}
