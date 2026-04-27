<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user  = User::factory()->create(['role' => 'player']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function un_joueur_peut_créer_un_ticket()
    {
        $response = $this->actingAs($this->user)->post(route('tickets.store'), [
            'title'       => 'Bug dans le jeu',
            'description' => 'Le bouton de clic ne répond plus après 1000 clics.',
            'category'    => 'bug',
        ]);

        $response->assertRedirect(route('tickets.index'));
        $this->assertDatabaseHas('tickets', [
            'user_id' => $this->user->id,
            'title'   => 'Bug dans le jeu',
            'status'  => 'open',
        ]);
    }

    /** @test */
    public function un_joueur_ne_peut_pas_voir_les_tickets_des_autres()
    {
        $autreUser = User::factory()->create();
        $ticket    = Ticket::factory()->create(['user_id' => $autreUser->id]);

        $this->actingAs($this->user)
             ->get(route('tickets.show', $ticket))
             ->assertForbidden();
    }

    /** @test */
    public function un_admin_peut_voir_tous_les_tickets()
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin)
             ->get(route('tickets.show', $ticket))
             ->assertOk();
    }

    /** @test */
    public function un_admin_peut_fermer_un_ticket()
    {
        $ticket = Ticket::factory()->create(['user_id' => $this->user->id, 'status' => 'open']);

        $this->actingAs($this->admin)->put(route('tickets.update', $ticket), [
            'status'      => 'closed',
            'admin_reply' => 'Problème résolu en version 1.2.',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id'     => $ticket->id,
            'status' => 'closed',
        ]);
    }

    /** @test */
    public function un_ticket_fermé_ne_peut_pas_être_modifié_par_un_joueur()
    {
        $ticket = Ticket::factory()->create([
            'user_id' => $this->user->id,
            'status'  => 'closed',
        ]);

        $this->actingAs($this->user)
             ->put(route('tickets.update', $ticket), [
                 'title'       => 'Nouveau titre',
                 'description' => 'Nouvelle description pour ce ticket.',
                 'category'    => 'bug',
             ])
             ->assertForbidden();
    }

    /** @test */
    public function la_description_doit_avoir_au_moins_10_caractères()
    {
        $this->actingAs($this->user)->post(route('tickets.store'), [
            'title'       => 'Titre valide',
            'description' => 'Trop court',
            'category'    => 'bug',
        ])->assertSessionHasErrors('description');
    }

    /* ── API TESTS ── */

    /** @test */
    public function api_open_tickets_retourne_les_tickets_ouverts()
    {
        Ticket::factory()->count(3)->create(['status' => 'open']);
        Ticket::factory()->count(2)->create(['status' => 'closed']);

        $this->getJson('/api/tickets/open')
             ->assertOk()
             ->assertJsonPath('count', 3);
    }

    /** @test */
    public function api_stats_retourne_les_statistiques()
    {
        Ticket::factory()->count(5)->create(['status' => 'open']);
        Ticket::factory()->count(2)->create(['status' => 'closed']);

        $response = $this->getJson('/api/tickets/stats');
        $response->assertOk()
                 ->assertJsonPath('total', 7)
                 ->assertJsonPath('open', 5)
                 ->assertJsonPath('closed', 2);
    }

    /** @test */
    public function api_user_tickets_retourne_les_tickets_par_email()
    {
        Ticket::factory()->count(4)->create(['user_id' => $this->user->id]);

        $this->getJson('/api/users/' . $this->user->email . '/tickets')
             ->assertOk()
             ->assertJsonPath('count', 4)
             ->assertJsonPath('user.email', $this->user->email);
    }
}
