<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {

        $user = User::factory()->create();
        $products = Product::factory()->count(5)->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->get(route('categories.products.index', ['category' => $category->id, 'filter' => 'all']));

        $response->assertOk();
        $response->assertJsonStructure();

        $this->assertCount(5, $products);
        
    }

    public function test_store_saves()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post(route('categories.products.store', ['name' => 'testName', 'description' => 'testDesc', 'price' => '10000', 'category_id' => $category->id, 'user_id' => $user->id, 'category' => $category->id]));


        $products = Product::query()
            ->where('name', 'testName')
            ->where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->get();

        $this->assertCount(1, $products);

        $response->assertCreated();
        $response->assertJsonStructure();
    }

    public function test_show_behaves_as_expected()
    {

        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->get(route('categories.products.show', ['product' => $product, 'category' => $product->category_id]));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }

    public function test_update_behaves_as_expected()
    {

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $name = fake()->firstName();
        $description = fake()->sentence(1);
        $price = fake()->numberBetween(0, 10000);

        $response = $this->actingAs($user, 'sanctum')->put(route('categories.products.update', ['category' => $product->category_id, 'product' => $product->id]),  [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'user_id' => $user->id,
            'category_id' => $product->category_id
        ]);

        $product->refresh();

        $response->assertOk();
        $response->assertJsonStructure();

        $this->assertEquals($name, $product->name);
        $this->assertEquals($price, $product->price);
        $this->assertEquals($description, $product->description);
        $this->assertEquals($user->id, $product->user_id);
    }

    public function test_destroy_behaves_as_expected(){

        $user = User::factory()->create();
        $product = Product::factory()->create();

        $product->user_id = $user->id;

        $response = $this->actingAs($user, 'sanctum')->delete(route('categories.products.destroy', ['category' => $product->category_id, 'product' => $product->id]));

        $response->assertNoContent();
        $this->assertModelMissing($product);

    }

    public function test_index_invalid_filter(){

        $user = User::factory()->create();
        $products = Product::factory()->create();
        $category = Category::factory()->create();


        $response = $this->actingAs($user, 'sanctum')->get(route('categories.products.index', ['filter' => 'test', 'category' => $category->id]));

        $response->assertStatus(400);
        $response->assertJsonStructure();
    }

    public function test_store_empty_body(){

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post(route('categories.products.store', ['category' => $category->id]));

        $response->assertStatus(302);
        $response->assertRedirect();

    }

    public function test_wrong_show_id(){
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, "sanctum")->get(route('categories.products.show', ['category' => 10, 'product' => $product->id]));

        $response->assertStatus(404);

    }

    

}
