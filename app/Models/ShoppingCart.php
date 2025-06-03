<?php

namespace App\Models;

class ShoppingCart
{
    public $items = [];
    public $subTotal = 0;
    public $total = 0;
    
    public function addItem($product, $quantity)
    {
        $existingItem = $this->findItem($product->id);
        
        if ($existingItem) {
            $existingItem['quantity'] += $quantity;
        } else {
            $this->items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->sell_price,
                'quantity' => $quantity,
                'image' => $product->image
            ];
        }
        
        $this->calculateTotals();
    }
    
    public function removeItem($productId)
    {
        $this->items = array_filter($this->items, function($item) use ($productId) {
            return $item['product_id'] != $productId;
        });
        
        $this->calculateTotals();
    }
    
    public function updateItem($product, $quantity)
    {
        $existingItem = $this->findItem($product->id);
        
        if ($existingItem) {
            $existingItem['quantity'] = $quantity;
            $this->calculateTotals();
        }
    }
    
    public function findItem($productId)
    {
        foreach ($this->items as &$item) {
            if ($item['product_id'] == $productId) {
                return $item;
            }
        }
        
        return null;
    }
    
    public function calculateTotals()
    {
        $this->subTotal = 0;
        
        foreach ($this->items as $item) {
            $this->subTotal += $item['price'] * $item['quantity'];
        }
        
        // Burada vergi, kargo gibi hesaplamalar yapılabilir
        $this->total = $this->subTotal;
    }
}