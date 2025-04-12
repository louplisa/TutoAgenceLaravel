<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Notifications\ContactRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_not_found_on_non_existent_property(): void
    {
        $response = $this->get('/biens/voluptatem-sint-quo-ipsum-inventore-quas-91');

        $response->assertStatus(404);
    }

    public function test_redirect_on_bad_slug_property(): void
    {
        /** @var Property $property */
        $property = Property::factory()->create();
        $response = $this->get('/biens/voluptatem-sint-quo-ipsum-inventore-quas-' . $property->id);

        $response->assertRedirectToRoute('property.show', ['property' => $property->id, 'slug' => $property->getSlug()]);
    }

    public function test_ok_on_property(): void
    {
        /** @var Property $property */
        $property = Property::factory()->create();
        $response = $this->get("/biens/{$property->getSlug()}-{$property->id}");
        $response->assertOk();
        $response->assertSee($property->title);
    }

    public function test_error_on_contact(): void
    {
        /** @var Property $property */
        $property = Property::factory()->create();
        $response = $this->post("/biens/{$property->id}/contact", [
            'firstname' => "John",
            'lastname' => "Doe",
            'email' => "doe",
            'phone' => "0000000000",
            'message' => "This is a message",
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasInput('email', 'doe');
    }

    public function test_ok_on_contact(): void
    {
        Notification::fake();
        /** @var Property $property */
        $property = Property::factory()->create();
        $response = $this->post("/biens/{$property->id}/contact", [
            'firstname' => "John",
            'lastname' => "Doe",
            'email' => "doe@demo.fr",
            'phone' => "0000000000",
            'message' => "This is a message",
        ]);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        Notification::assertCount(1);
        Notification::assertSentOnDemand(ContactRequestNotification::class);
        
    }
}
