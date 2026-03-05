<?php

namespace Tests\Feature\Opportunities;

use App\Livewire\Opportunities\Board as PipelineBoard;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\OpportunityStage;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class PipelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
    }

    private function createUserWithPermissions(array $permissions = []): User
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }

    private function createStagesForCompany(int $companyId): array
    {
        $open = OpportunityStage::factory()->create([
            'company_id' => $companyId,
            'name' => 'Prospecção',
            'color' => '#111111',
            'order' => 1,
        ]);

        $won = OpportunityStage::factory()->create([
            'company_id' => $companyId,
            'name' => 'Fechado - Ganho',
            'color' => '#22c55e',
            'order' => 2,
        ]);

        $lost = OpportunityStage::factory()->create([
            'company_id' => $companyId,
            'name' => 'Fechado - Perdido',
            'color' => '#ef4444',
            'order' => 3,
        ]);

        return [$open, $won, $lost];
    }

    public function test_guest_is_redirected_from_pipeline(): void
    {
        $this->get('/opportunities')->assertRedirect('/login');
    }

    public function test_user_without_view_permission_cannot_access_pipeline(): void
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user)
            ->get('/opportunities')
            ->assertForbidden();
    }

    public function test_user_with_view_permission_can_access_pipeline(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);

        $this->actingAs($user)
            ->get('/opportunities')
            ->assertOk();
    }

    public function test_admin_can_access_pipeline_without_explicit_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/opportunities')
            ->assertOk();
    }

    public function test_user_without_create_permission_cannot_open_create_modal(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        $this->createStagesForCompany($user->company_id);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('openCreateModal')
            ->assertStatus(403);
    }

    public function test_create_requires_valid_fields(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view', 'opportunities.create']);
        $this->createStagesForCompany($user->company_id);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->set('title', '')
            ->set('stage_id', '')
            ->set('value', '-10')
            ->set('expected_close_date', 'invalid-date')
            ->call('save')
            ->assertHasErrors(['title', 'stage_id', 'value', 'expected_close_date']);
    }

    public function test_user_with_create_permission_can_create_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view', 'opportunities.create']);
        [$open] = $this->createStagesForCompany($user->company_id);
        $client = Client::factory()->create(['company_id' => $user->company_id]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->set('title', 'Nova Oportunidade')
            ->set('client_id', (string) $client->id)
            ->set('stage_id', (string) $open->id)
            ->set('value', '1500')
            ->set('expected_close_date', Carbon::now()->format('Y-m-d'))
            ->set('notes', 'Observações')
            ->call('save');

        $this->assertDatabaseHas('opportunities', [
            'title' => 'Nova Oportunidade',
            'client_id' => $client->id,
            'stage_id' => $open->id,
            'company_id' => $user->company_id,
            'created_by' => $user->id,
        ]);
    }

    public function test_user_without_update_permission_cannot_edit_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);
        $opp = Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('openEditModal', $opp->id)
            ->assertStatus(403);
    }

    public function test_user_with_update_permission_can_edit_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view', 'opportunities.update']);
        [$open] = $this->createStagesForCompany($user->company_id);
        $opp = Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('openEditModal', $opp->id)
            ->set('title', 'Oportunidade Atualizada')
            ->set('value', '999')
            ->call('save');

        $this->assertDatabaseHas('opportunities', [
            'id' => $opp->id,
            'title' => 'Oportunidade Atualizada',
            'company_id' => $user->company_id,
        ]);
    }

    public function test_user_without_delete_permission_cannot_delete_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);
        $opp = Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('delete', $opp->id)
            ->assertStatus(403);
    }

    public function test_user_with_delete_permission_can_delete_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view', 'opportunities.delete']);
        [$open] = $this->createStagesForCompany($user->company_id);
        $opp = Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('delete', $opp->id);

        $this->assertSoftDeleted('opportunities', [
            'id' => $opp->id,
        ]);
    }

    public function test_user_without_update_permission_cannot_move_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open, $won] = $this->createStagesForCompany($user->company_id);
        $opp = Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('move', $opp->id, $won->id)
            ->assertStatus(403);
    }

    public function test_user_with_update_permission_can_move_opportunity(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view', 'opportunities.update']);
        [$open, $won] = $this->createStagesForCompany($user->company_id);
        $opp = Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('move', $opp->id, $won->id);

        $this->assertDatabaseHas('opportunities', [
            'id' => $opp->id,
            'stage_id' => $won->id,
        ]);
    }

    public function test_pipeline_list_is_scoped_to_company(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Opp Alpha',
        ]);

        Opportunity::factory()->create([
            'title' => 'Opp Beta',
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->assertSee('Opp Alpha')
            ->assertDontSee('Opp Beta');
    }

    public function test_filter_by_search(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Alpha',
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Beta',
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->set('search', 'Alpha')
            ->assertSee('Venda Alpha')
            ->assertDontSee('Venda Beta');
    }

    public function test_filter_by_client(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);
        $clientA = Client::factory()->create(['company_id' => $user->company_id]);
        $clientB = Client::factory()->create(['company_id' => $user->company_id]);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'client_id' => $clientA->id,
            'title' => 'Venda Alpha',
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'client_id' => $clientB->id,
            'title' => 'Venda Beta',
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->set('clientFilter', (string) $clientB->id)
            ->assertSee('Venda Beta')
            ->assertDontSee('Venda Alpha');
    }

    public function test_filter_by_stage(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open, $won] = $this->createStagesForCompany($user->company_id);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Alpha',
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $won->id,
            'title' => 'Venda Beta',
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->set('stageFilter', (string) $open->id)
            ->assertSee('Venda Alpha')
            ->assertDontSee('Venda Beta');
    }

    public function test_filter_by_status_open_closed(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open, $won, $lost] = $this->createStagesForCompany($user->company_id);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Alpha',
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $won->id,
            'title' => 'Venda Beta',
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $lost->id,
            'title' => 'Venda Gama',
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->set('statusFilter', 'open')
            ->assertSee('Venda Alpha')
            ->assertDontSee('Venda Beta')
            ->assertDontSee('Venda Gama')
            ->set('statusFilter', 'closed')
            ->assertSee('Venda Beta')
            ->assertSee('Venda Gama')
            ->assertDontSee('Venda Alpha');
    }

    public function test_filter_by_value_range(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Alpha',
            'value' => 500,
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Beta',
            'value' => 5000,
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Gama',
            'value' => 50,
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->set('valueMin', '1000')
            ->assertSee('Venda Beta')
            ->assertDontSee('Venda Alpha')
            ->assertDontSee('Venda Gama')
            ->set('valueMin', '')
            ->set('valueMax', '100')
            ->assertSee('Venda Gama')
            ->assertDontSee('Venda Alpha')
            ->assertDontSee('Venda Beta');
    }

    public function test_filter_by_date_range(): void
    {
        $user = $this->createUserWithPermissions(['opportunities.view']);
        [$open] = $this->createStagesForCompany($user->company_id);

        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Alpha',
            'expected_close_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);
        Opportunity::factory()->create([
            'company_id' => $user->company_id,
            'stage_id' => $open->id,
            'title' => 'Venda Beta',
            'expected_close_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
        ]);

        $this->actingAs($user);

        Livewire::test(PipelineBoard::class)
            ->call('clearFilters')
            ->set('dateFrom', Carbon::now()->addDays(10)->format('Y-m-d'))
            ->set('dateTo', Carbon::now()->addDays(30)->format('Y-m-d'))
            ->assertSee('Venda Beta')
            ->assertDontSee('Venda Alpha');
    }
}
