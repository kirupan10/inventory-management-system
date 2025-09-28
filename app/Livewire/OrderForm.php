<?php

namespace App\Livewire;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OrderForm extends Component
{
    public $cart_instance;

    private $product;

    #[Validate('Required')]
    public int $taxes = 0;

    public array $invoiceProducts = [];

    public array $searchFocused = [];

    #[Validate('required', message: 'Please select products')]
    public Collection $allProducts;

    public function mount($cartInstance): void
    {
        $this->cart_instance = $cartInstance;

        $this->allProducts = Product::all();

        // Initialize searchFocused array
        $this->searchFocused = [];

        //$cart_items = Cart::instance($this->cart_instance)->content();
    }

    public function render(): View
    {
        $total = 0;

        foreach ($this->invoiceProducts as $invoiceProduct) {
            if ($invoiceProduct['is_saved'] && $invoiceProduct['product_price'] && $invoiceProduct['quantity']) {
                $total += $invoiceProduct['product_price'] * $invoiceProduct['quantity'];
            }
        }

        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.order-form', [
            'subtotal' => $total,
            'total' => $total * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100),
            'cart_items' => $cart_items,
        ]);
    }

    public function addProduct(): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'This line must be saved before creating a new one.');

                return;
            }
        }

        $newIndex = count($this->invoiceProducts);

        $this->invoiceProducts[] = [
            'product_id' => '',
            'product_search' => '',
            'quantity' => 1,
            'is_saved' => false,
            'product_name' => '',
            'product_price' => 0,
        ];

        // Initialize focus state for new product
        $this->searchFocused[$newIndex] = false;
    }

    public function selectProduct($index, $productId, $productName): void
    {
        $product = $this->allProducts->find($productId);

        if ($product) {
            $this->invoiceProducts[$index]['product_id'] = $productId;
            $this->invoiceProducts[$index]['product_search'] = $productName;
            $this->invoiceProducts[$index]['product_name'] = $productName;
            $this->invoiceProducts[$index]['product_price'] = $product->selling_price;

            // Clear focus state after selection
            $this->searchFocused[$index] = false;
        }
    }

    public function focusSearch($index): void
    {
        $this->searchFocused[$index] = true;
    }

    public function blurSearch($index): void
    {
        // Use a small delay to allow click events to register before hiding
        $this->dispatch('hide-search-results', ['index' => $index]);
        $this->searchFocused[$index] = false;
    }

    public function getFilteredProducts($index)
    {
        // If no search term or empty search, show first 5 products
        if (!isset($this->invoiceProducts[$index]['product_search']) ||
            strlen($this->invoiceProducts[$index]['product_search']) < 1) {
            return $this->allProducts->take(5);
        }

        $searchTerm = strtolower($this->invoiceProducts[$index]['product_search']);

        return $this->allProducts->filter(function($product) use ($searchTerm) {
            return str_contains(strtolower($product->name), $searchTerm) ||
                   str_contains(strtolower($product->code), $searchTerm);
        })->take(8); // Limit to 8 results for better performance
    }

    public function updatedInvoiceProducts($value, $key)
    {
        // This method will be called when any invoiceProducts property changes
        // Clear product_id when search term changes to avoid conflicts
        if (str_contains($key, 'product_search')) {
            $parts = explode('.', $key);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $index = $parts[0];
                // Clear product_id when search changes
                if (isset($this->invoiceProducts[$index])) {
                    $this->invoiceProducts[$index]['product_id'] = '';
                }
            }
        }
    }

    public function highlightSearch($text, $search)
    {
        if (empty($search) || empty($text)) {
            return $text;
        }

        $highlighted = preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark class="bg-warning">$1</mark>', $text);
        return $highlighted;
    }

    public function editProduct($index): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'This line must be saved before editing another.');

                return;
            }
        }

        $this->invoiceProducts[$index]['is_saved'] = false;
    }

    public function saveProduct($index): void
    {
        $this->resetErrorBag();

        $product = $this->allProducts
            ->find($this->invoiceProducts[$index]['product_id']);

        $this->invoiceProducts[$index]['product_name'] = $product->name;
        $this->invoiceProducts[$index]['product_price'] = $product->buying_price;
        $this->invoiceProducts[$index]['is_saved'] = true;

        //
        $cart = Cart::instance($this->cart_instance);

        $exists = $cart->search(function ($cartItem) use ($product) {
            return $cartItem->id === $product['id'];
        });

        if ($exists->isNotEmpty()) {
            session()->flash('message', 'Product exists in the cart!');

            // not working correctly
            //unset($this->invoiceProducts[$index]);

            return;
        }

        $cart->add([
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['buying_price'],
            'qty' => $this->invoiceProducts[$index]['quantity'], //form field
            'weight' => 1,
            'options' => [
                'code' => $product['code'],
            ],
        ]);
    }

    public function removeProduct($index): void
    {
        unset($this->invoiceProducts[$index]);

        $this->invoiceProducts = array_values($this->invoiceProducts);

        //
        //Cart::instance($this->cart_instance)->remove($index);
    }
}
