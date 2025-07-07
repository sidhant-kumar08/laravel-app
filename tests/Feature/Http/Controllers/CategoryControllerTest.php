<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


/**
 * @see \App\Http\Controllers\CategoryController
 */
final class CategoryControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $categories = Category::factory()->count(3)->create();
        $user = User::factory()->create();



        $response = $this->actingAs($user, 'sanctum')->get(route('categories.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }



    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CategoryController::class,
            'store',
            \App\Http\Requests\CategoryStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post(route('categories.store'), [
            'name' => 'product name',
            'description' => fake()->sentence(1),
        ]);

        $categories = Category::query()
            ->where('name', 'product name')
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $categories);
        $category = $categories->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->get(route('categories.show', $category));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CategoryController::class,
            'update',
            \App\Http\Requests\CategoryUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $category = Category::factory()->create();
        $name =  fake()->firstName();
        $description =  fake()->sentence(1);
        $user = User::factory()->create();
        $user->id = $category->user_id;

        $response = $this->actingAs($user, 'sanctum')->put(route('categories.update', $category), [
            'name' => $name,
            'description' => $description,
            'user_id' => $user->id,
        ]);

        $category->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $category->name);
        $this->assertEquals($description, $category->description);
        $this->assertEquals($user->id, $category->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $user->id = $category->user_id;

        $response = $this->actingAs($user, 'sanctum')->delete(route('categories.destroy', $category));

        $response->assertNoContent();

        $this->assertModelMissing($category);
    }
}
