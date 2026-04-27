<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_payment_appartient_à_un_user()
    {
        $user    = User::factory()->create();
        $payment = Payment::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $payment->user->id);
    }

    /** @test */
    public function scope_completed_filtre_les_paiements_complétés()
    {
        Payment::factory()->count(3)->create(['status' => 'completed']);
        Payment::factory()->count(2)->create(['status' => 'refunded']);

        $this->assertEquals(3, Payment::completed()->count());
    }

    /** @test */
    public function scope_refunded_filtre_les_paiements_remboursés()
    {
        Payment::factory()->count(2)->create(['status' => 'refunded']);
        Payment::factory()->count(4)->create(['status' => 'completed']);

        $this->assertEquals(2, Payment::refunded()->count());
    }

    /** @test */
    public function un_remboursement_crée_un_ticket_automatiquement()
    {
        $user    = User::factory()->create();
        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'status'  => 'completed',
        ]);

        $this->actingAs($user)->post(route('payments.refund', $payment));

        $payment->refresh();
        $this->assertEquals('refunded', $payment->status);
        $this->assertDatabaseHas('tickets', [
            'user_id'  => $user->id,
            'category' => 'refund',
        ]);
    }
}
