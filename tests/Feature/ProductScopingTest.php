<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductScopingTest extends TestCase
{
    use RefreshDatabase;

    private $userA;
    private $userB;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::create([
            'name'     => 'User A',
            'email'    => 'usera@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->userB = User::create([
            'name'     => 'User B',
            'email'    => 'userb@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->category = Category::create([
            'name' => 'General Category',
        ]);
    }

    public function test_user_can_only_see_their_own_products(): void
    {
        // User A creates a product
        $productA = Product::create([
            'user_id' => $this->userA->id,
            'category_id' => $this->category->id,
            'title' => 'Product A',
            'price' => 10.00,
            'Budget_Range' => 'Medium',
        ]);

        // User B creates a product
        $productB = Product::create([
            'user_id' => $this->userB->id,
            'category_id' => $this->category->id,
            'title' => 'Product B',
            'price' => 20.00,
            'Budget_Range' => 'Medium',
        ]);

        // Authenticate as User A
        Sanctum::actingAs($this->userA);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Product A'])
                 ->assertJsonMissing(['title' => 'Product B']);
    }

    public function test_user_cannot_view_another_users_product(): void
    {
        $productB = Product::create([
            'user_id' => $this->userB->id,
            'category_id' => $this->category->id,
            'title' => 'Product B',
            'price' => 20.00,
            'Budget_Range' => 'Medium',
        ]);

        Sanctum::actingAs($this->userA);

        $response = $this->getJson("/api/products/{$productB->id}");

        $response->assertStatus(404);
    }

    public function test_user_cannot_update_another_users_product(): void
    {
        $productB = Product::create([
            'user_id' => $this->userB->id,
            'category_id' => $this->category->id,
            'title' => 'Product B',
            'price' => 20.00,
            'Budget_Range' => 'Medium',
        ]);

        Sanctum::actingAs($this->userA);

        $response = $this->postJson("/api/products/{$productB->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(404);
        $this->assertEquals('Product B', $productB->fresh()->title);
    }

    public function test_user_cannot_delete_another_users_product(): void
    {
        $productB = Product::create([
            'user_id' => $this->userB->id,
            'category_id' => $this->category->id,
            'title' => 'Product B',
            'price' => 20.00,
            'Budget_Range' => 'Medium',
        ]);

        Sanctum::actingAs($this->userA);

        $response = $this->deleteJson("/api/products/{$productB->id}");

        $response->assertStatus(404);
        $this->assertFalse($productB->fresh()->trashed());
    }

    public function test_user_cannot_add_another_users_product_to_cart(): void
    {
        $productB = Product::create([
            'user_id' => $this->userB->id,
            'category_id' => $this->category->id,
            'title' => 'Product B',
            'price' => 20.00,
            'Budget_Range' => 'Medium',
        ]);

        Sanctum::actingAs($this->userA);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $productB->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Product not found or access denied']);
    }
}
