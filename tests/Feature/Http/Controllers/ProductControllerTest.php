<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProductController
 */
final class ProductControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductController::class,
            'store',
            \App\Http\Requests\ProductStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = fake()->name();
        $description = fake()->text();
        $price = fake()->numberBetween(-10000, 10000);
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('products.store'), [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        $products = Product::query()
            ->where('name', $name)
            ->where('description', $description)
            ->where('price', $price)
            ->where('category_id', $category->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $products);
        $product = $products->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProductController::class,
            'update',
            \App\Http\Requests\ProductUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $product = Product::factory()->create();
        $name = fake()->name();
        $description = fake()->text();
        $price = fake()->numberBetween(-10000, 10000);
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('products.update', $product), [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        $product->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $product->name);
        $this->assertEquals($description, $product->description);
        $this->assertEquals($price, $product->price);
        $this->assertEquals($category->id, $product->category_id);
        $this->assertEquals($user->id, $product->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertNoContent();

        $this->assertModelMissing($product);
    }
}
