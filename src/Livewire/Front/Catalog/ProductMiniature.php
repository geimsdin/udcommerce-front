<?php

namespace Unusualdope\FrontLaravelEcommerce\Livewire\Front\Catalog;

use Livewire\Component;
use Livewire\Livewire;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Language;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\SpecificPrice;

class ProductMiniature extends Component
{
    public $product;
    public $language;

    public $currency;

    public $selectedImageId = null;

    public $selectedVariationId = null;

    public $selectedVariants = []; // [variantGroupId => variantId]

    public $quantity = 1;

    public function mount($product)
    {
        $this->language = Language::where('iso_code', app()->getLocale())->first();
        $this->product = $product->load('languages');
        $this->currency = Currency::getCurrentCurrency();
        $this->quantity = $this->getMinQuantity();
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getSelectedVariation()
    {
        $product = $this->getProduct();
        if (! $product || $product->type !== 'variable') {
            return null;
        }

        if ($this->selectedVariationId) {
            return $product->variations->firstWhere('id', $this->selectedVariationId);
        }

        // Try to find variation based on selected variants
        if (! empty($this->selectedVariants)) {
            foreach ($product->variations as $variation) {
                $variationVariantIds = $variation->variants->pluck('id')->toArray();
                $selectedVariantIds = array_values($this->selectedVariants);

                if (count($variationVariantIds) === count($selectedVariantIds) &&
                    empty(array_diff($variationVariantIds, $selectedVariantIds))) {
                    $this->selectedVariationId = $variation->id;

                    return $variation;
                }
            }
        }

        return null;
    }

    public function getFormattedPrice()
    {
        $product = $this->getProduct();
        if (! $product || ! $this->currency) {
            return '0,00';
        }

        $variation = $this->getSelectedVariation();
        $basePrice = $variation && $variation->price > 0
            ? $variation->price
            : ($product->price ?? 0);

        // Calculate specific price in base currency
        $price = $this->calculatePrice($product, $variation, $basePrice);

        // Apply currency exchange rate (prices stored in default currency)
        $rate = $this->currency->exchange_rate ?? 1;
        $convertedPrice = $price * $rate;

        $symbol = $this->currency->symbol ?? '€';
        $formatted = number_format((float) $convertedPrice, 2, ',', '.');

        return $symbol.' '.$formatted;
    }

    protected function calculatePrice($product, $variation, $basePrice)
    {
        $quantity = $this->quantity;
        $currencyId = $this->currency->id ?? null;
        $customer = $this->getCurrentCustomer();
        $customerId = $customer ? $customer->id : null;
        $clientGroupId = null;
        
        if ($customer && $customer->groups()->count() > 0) {
            $clientGroupId = $customer->groups()->first()->id;
        }

        // Get specific prices for this product
        $specificPrices = SpecificPrice::where('id_product', $product->id)
            ->where(function($query) use ($currencyId) {
                $query->where('id_currency', $currencyId)
                      ->orWhere('id_currency', 0)
                      ->orWhereNull('id_currency');
            })
            ->where(function($query) use ($clientGroupId) {
                if ($clientGroupId) {
                    $query->where('id_client_type', $clientGroupId)
                          ->orWhere('id_client_type', 0)
                          ->orWhereNull('id_client_type');
                } else {
                    $query->where('id_client_type', 0)
                          ->orWhereNull('id_client_type');
                }
            })
            ->where(function($query) use ($customerId) {
                if ($customerId) {
                    $query->where('id_customer', $customerId)
                          ->orWhereNull('id_customer');
                } else {
                    $query->where('id_customer', 0)
                    ->orWhereNull('id_customer');
                }
            })
            ->where(function($query) use ($quantity) {
                $query->where('from_quantity', '<=', $quantity)
                      ->orWhereNull('from_quantity')
                      ->orWhere('from_quantity', 0);
            })
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('from', '<=', now())
                      ->orWhereNull('from');
                })
                ->where(function($q) {
                    $q->where('to', '>=', now())
                      ->orWhereNull('to');
                });
            })
            ->orderBy('from_quantity', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $bestPrice = $basePrice;
        foreach ($specificPrices as $specificPrice) {
            $calculatedPrice = $basePrice;

            // Apply specific price or reduction
            if ($specificPrice->price && $specificPrice->price > 0) {
                $calculatedPrice = $specificPrice->price;
            } elseif ($specificPrice->reduction && $specificPrice->reduction > 0) {
                if ($specificPrice->reduction_type === 'percentage') {
                    $calculatedPrice = $basePrice * (1 - ($specificPrice->reduction / 100));
                } else {
                    $calculatedPrice = $basePrice - $specificPrice->reduction;
                }
            }

            // Use the best (lowest) price
            if ($calculatedPrice < $bestPrice) {
                $bestPrice = $calculatedPrice;
            }
        }

        return max(0, $bestPrice);
    }

    protected function getCurrentCustomer()
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();
        
        // Get client associated with user
        return Client::where('user_id', $user->id)->first();
    }

    public function getProductFeatures()
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        // Load features with their groups
        $features = $product->features()->with(['featureGroup.currentLanguage', 'currentLanguage'])->get();
        
        // Group by feature group
        $groupedFeatures = [];
        foreach ($features as $feature) {
            $groupId = $feature->feature_group_id;
            $groupName = $feature->featureGroup ? $feature->featureGroup->name : 'Other';
            
            if (!isset($groupedFeatures[$groupId])) {
                $groupedFeatures[$groupId] = [
                    'id' => $groupId,
                    'name' => $groupName,
                    'features' => [],
                ];
            }
            
            $groupedFeatures[$groupId]['features'][] = [
                'id' => $feature->id,
                'name' => $feature->name,
            ];
        }

        return array_values($groupedFeatures);
    }

    public function getCurrentStock()
    {
        $product = $this->getProduct();
        if (! $product) {
            return 0;
        }

        $variation = $this->getSelectedVariation();
        if ($variation) {
            return $variation->quantity ?? 0;
        }

        return $product->quantity ?? 0;
    }

    public function getMinQuantity()
    {
        $product = $this->getProduct();
        if (!$product) {
            return 1;
        }

        // Use product's minimal_quantity, default to 1
        $minQuantity = $product->minimal_quantity ?? 1;
        return max(1, (int)$minQuantity);
    }

    public function getMaxQuantity()
    {
        return $this->getCurrentStock();
    }

    public function getLowStockAlert()
    {
        $product = $this->getProduct();
        if (!$product) {
            return null;
        }

        // Use product's low_stock_alert
        return $product->low_stock_alert ?? null;
    }

    public function isLowStock()
    {
        $stock = $this->getCurrentStock();
        $lowStockAlert = $this->getLowStockAlert();
        
        if ($lowStockAlert === null) {
            return false;
        }

        return $stock <= (int)$lowStockAlert;
    }

    public function updatedQuantity()
    {
        $min = $this->getMinQuantity();
        $max = $this->getMaxQuantity();

        // Ensure quantity is within bounds
        if ($this->quantity < $min) {
            $this->quantity = $min;
        } elseif ($max > 0 && $this->quantity > $max) {
            $this->quantity = $max;
        }
    }

    public function incrementQuantity()
    {
        $max = $this->getMaxQuantity();
        if ($max > 0 && $this->quantity < $max) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        $min = $this->getMinQuantity();
        if ($this->quantity > $min) {
            $this->quantity--;
        }
    }

    public function getProductUrl()
    {
        return url()->current();
    }

    public function selectImage($imageId)
    {
        $this->selectedImageId = $imageId;
    }

    public function selectVariant($variantGroupId, $variantId)
    {
        $groupId = (int)$variantGroupId;
        $varId = (int)$variantId;
        
        $this->selectedVariants[$groupId] = $varId;
        $this->selectedVariationId = null; // Reset to recalculate
        
        // Reset quantity to new min quantity when variation changes
        $this->quantity = $this->getMinQuantity();
        
        // Find the variation and auto-select its image if it exists
        $product = $this->getProduct();
        if ($product && $product->type === 'variable') {
            // Find matching variation
            foreach ($product->variations as $variation) {
                $variationVariantIds = $variation->variants->pluck('id')->toArray();
                $selectedVariantIds = array_values($this->selectedVariants);
                
                if (count($variationVariantIds) === count($selectedVariantIds) && 
                    empty(array_diff($variationVariantIds, $selectedVariantIds))) {
                    // Found matching variation
                    if ($variation->image) {
                        // Variation has its own image, select it
                        $this->selectedImageId = $variation->image->id;
                    } else {
                        // Variation has no image, select main product image
                        $mainProductImage = $this->getMainProductImage();
                        $this->selectedImageId = $mainProductImage ? $mainProductImage->id : null;
                    }
                    return;
                }
            }
        }
        
        // If no matching variation found, select main product image
        $mainProductImage = $this->getMainProductImage();
        $this->selectedImageId = $mainProductImage ? $mainProductImage->id : null;
    }

    public function getVariationsByGroup()
    {
        $product = $this->getProduct();
        if (! $product || $product->type !== 'variable' || ! $product->variations) {
            return [];
        }

        $groups = [];
        $languageId = $this->language->id ?? 1;

        // First, collect all variants by group
        foreach ($product->variations as $variation) {
            foreach ($variation->variants as $variant) {
                $group = $variant->variantGroup;
                $groupLang = $group->getSpecificLanguage($languageId);
                $groupName = ($groupLang && $groupLang->name) ? $groupLang->name : $group->getNameAttribute();
                $variantName = $variant->getNameCurrentLanguage($languageId) ?: $variant->getNameAttribute();

                if (! isset($groups[$group->id])) {
                    $groups[$group->id] = [
                        'id' => $group->id,
                        'name' => $groupName,
                        'variants' => [],
                        'is_color' => $group->type === 'color',
                    ];
                }

                // Add variant if not already added
                if (! isset($groups[$group->id]['variants'][$variant->id])) {
                    $groups[$group->id]['variants'][$variant->id] = [
                        'id' => $variant->id,
                        'name' => $variantName,
                        'color' => $variant->color ?? null,
                    ];
                }
            }
        }

        // Now filter variants based on selected variants
        // Only show variants that are part of valid variations
        foreach ($groups as $groupId => &$group) {
            if (empty($this->selectedVariants)) {
                // No selections yet, show all variants
                continue;
            }

            // Get valid variant IDs for this group based on current selections
            $validVariantIds = $this->getValidVariantIdsForGroup($product, $groupId);
            
            // Filter variants to only show valid ones
            if (!empty($validVariantIds)) {
                $group['variants'] = array_filter($group['variants'], function($variant) use ($validVariantIds) {
                    return in_array($variant['id'], $validVariantIds);
                });
            } else {
                // No valid variants found, show empty (or could show all as fallback)
                $group['variants'] = [];
            }
        }

        return $groups;
    }

    protected function getValidVariantIdsForGroup($product, $groupId)
    {
        $validVariantIds = [];
        
        // Find all variations that match current selections (excluding the current group)
        foreach ($product->variations as $variation) {
            // Check if this variation matches all selected variants (except the current group)
            $matches = true;
            foreach ($this->selectedVariants as $selectedGroupId => $selectedVariantId) {
                if ($selectedGroupId == $groupId) {
                    // Skip the current group we're filtering for
                    continue;
                }
                
                // Check if this variation has the selected variant for this group
                $hasVariant = $variation->variants->contains(function($variant) use ($selectedGroupId, $selectedVariantId) {
                    return $variant->variant_group_id == $selectedGroupId && $variant->id == $selectedVariantId;
                });
                
                if (!$hasVariant) {
                    $matches = false;
                    break;
                }
            }
            
            // If variation matches, add its variant for the current group
            if ($matches) {
                $variantForGroup = $variation->variants->firstWhere('variant_group_id', $groupId);
                if ($variantForGroup) {
                    $validVariantIds[] = $variantForGroup->id;
                }
            }
        }
        
        return array_unique($validVariantIds);
    }

    public function getAllImages()
    {
        $product = $this->getProduct();
        if (!$product) {
            return collect();
        }

        // Return all product images (including variation-specific ones)
        return $product->images;
    }

    public function getMainProductImage()
    {
        $product = $this->getProduct();
        if (!$product) {
            return null;
        }

        // Get first product image that's not variation-specific
        $mainImage = $product->images->where('variation_id', null)->first();
        
        // If no non-variation image, get first image overall
        return $mainImage ?? $product->images->first();
    }

    public function getMainImage()
    {
        $product = $this->getProduct();
        if (!$product) {
            return null;
        }
        
        // If user manually selected an image, use that
        if ($this->selectedImageId) {
            $selected = $product->images->firstWhere('id', $this->selectedImageId);
            if ($selected) {
                return $selected;
            }
        }

        // If variation is selected and has an image, use that
        $variation = $this->getSelectedVariation();
        if ($variation && $variation->image) {
            return $variation->image;
        }

        // If variation is selected but has no image, use main product image
        if ($variation) {
            return $this->getMainProductImage();
        }

        // Otherwise, use main product image
        return $this->getMainProductImage();
    }

    public function getImageUrl($image)
    {
        if (!$image) {
            return 'https://placehold.co/800x800?text=No+Image';
        }
        return asset('storage/' . $image->image);
    }

    public function isImageSelected($image)
    {
        if (!$image) {
            return false;
        }
        
        $mainImage = $this->getMainImage();
        return $mainImage && $mainImage->id === $image->id;
    }

    public function getShortDescription()
    {
        $product = $this->getProduct();
        if (!$product) {
            return '';
        }

        if ($product->description_short) {
            return $product->description_short;
        }

        if ($product->description_long) {
            $description = strip_tags($product->description_long);
            return strlen($description) > 200 ? substr($description, 0, 200) . '...' : $description;
        }

        return '';
    }

    public function isVariantSelected($groupId, $variantId)
    {
        return isset($this->selectedVariants[$groupId]) && 
               $this->selectedVariants[$groupId] == $variantId;
    }

    public function getProductSchema()
    {
        $product = $this->getProduct();
        if (!$product) {
            return null;
        }

        $variation = $this->getSelectedVariation();
        $basePrice = $variation && $variation->price > 0 
            ? $variation->price 
            : ($product->price ?? 0);
        
        // Calculate price with specific prices
        $price = $this->calculatePrice($product, $variation, $basePrice);
        
        $stock = $this->getCurrentStock();
        $availability = $stock > 0 
            ? 'https://schema.org/InStock' 
            : 'https://schema.org/OutOfStock';

        $currency = $this->currency;
        $currencyCode = $currency ? ($currency->iso_code ?? 'EUR') : 'EUR';

        // Get all product images
        $images = [];
        foreach ($this->getAllImages() as $image) {
            $images[] = $this->getImageUrl($image);
        }

        // Build schema
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => strip_tags($product->description_long ?? $product->description_short ?? ''),
            'image' => !empty($images) ? $images : null,
            'url' => url($this->getProductUrl()),
            'sku' => $product->sku ?? null,
            'mpn' => $product->mpn ?? null,
            'gtin' => $product->ean ?? $product->upc ?? $product->isbn ?? null,
        ];

        // Add brand if available
        if ($product->brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand->name,
            ];
        }

        // Add manufacturer if brand exists (using brand as manufacturer)
        if ($product->brand) {
            $schema['manufacturer'] = [
                '@type' => 'Organization',
                'name' => $product->brand->name,
            ];
        }

        // Add offers
        $schema['offers'] = [
            '@type' => 'Offer',
            'price' => number_format((float)$price, 2, '.', ''),
            'priceCurrency' => $currencyCode,
            'availability' => $availability,
            'url' => url($this->getProductUrl()),
            'priceValidUntil' => date('Y-m-d', strtotime('+1 year')),
        ];

        // Add itemCondition if available
        $schema['offers']['itemCondition'] = 'https://schema.org/NewCondition';

        // Add category if available
        if ($product->categories && $product->categories->count() > 0) {
            $category = $product->categories->first();
            $schema['category'] = $category->name ?? null;
        }

        // Remove null values
        $schema = array_filter($schema, function($value) {
            return $value !== null && $value !== '';
        });

        // Clean up nested arrays
        if (isset($schema['image']) && is_array($schema['image']) && empty($schema['image'])) {
            unset($schema['image']);
        }

        return $schema;
    }

    public function addToCart()
    {
        $product = $this->getProduct();
        if (!$product) {
            session()->flash('status', __('front-ecommerce::products.product_not_found'));
            return;
        }
        
        
        $variation = $this->getSelectedVariation();
        if (!$variation) {
            session()->flash('status', __('front-ecommerce::products.no_variation_selected'));
            return;
        }
        Cart::addToCart($product->id, $variation->id, $this->quantity, $this->language->id, $product->name);
        $this->dispatch('updateCart');
        session()->flash('status', __('front-ecommerce::products.product_added_to_cart'));
        // return redirect()->route('front.cart.index');
    }

    public function render()
    {
        return view('front-ecommerce::livewire.front.catalog.product-miniature', [
            'product' => $this->getProduct(),
            'formattedPrice' => $this->getFormattedPrice(),
            'productUrl' => $this->getProductUrl(),
            'selectedVariation' => $this->getSelectedVariation(),
            'currentStock' => $this->getCurrentStock(),
            'variationsByGroup' => $this->getVariationsByGroup(),
            'allImages' => $this->getAllImages(),
            'mainImage' => $this->getMainImage(),
            'productSchema' => $this->getProductSchema(),
            'productFeatures' => $this->getProductFeatures(),
        ]);
    }
}
