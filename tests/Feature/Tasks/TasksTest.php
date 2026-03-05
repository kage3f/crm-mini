<?php

namespace Tests\Feature\Tasks;

use App\Livewire\Tasks\Index as TasksIndex;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\OpportunityStage;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class TasksTest extends TestCase
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

    private function createOpportunityForCompany(int $companyId): Opportunity
    {
        $stage = OpportunityStage::factory()->create([
            'company_id' => $companyId,
            'name' => 'Prospecção',
            'color' => '#111111',
            'order' => 1,
        ]);

        return Opportunity::factory()->create([
            'company_id' => $companyId,
            'stage_id' => $stage->id,
        ]);
    }

    public function test_guest_is_redirected_from_tasks_page(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }

    public function test_user_without_view_permission_cannot_access_tasks_page(): void
    {
        $user = $this->createUserWithPermissions([]);

        $this->actingAs($user)
            ->get('/tasks')
            ->assertForbidden();
    }

    public function test_user_with_view_permission_can_access_tasks_page(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        $this->actingAs($user)
            ->get('/tasks')
            ->assertOk();
    }

    public function test_admin_can_access_tasks_without_explicit_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/tasks')
            ->assertOk();
    }

    public function test_user_without_create_permission_cannot_open_create_modal(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('openCreateModal')
            ->assertStatus(403);
    }

    public function test_create_requires_valid_fields(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.create']);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('title', '')
            ->set('status', 'invalid')
            ->set('due_date', 'not-a-date')
            ->set('client_id', 'abc')
            ->set('opportunity_id', 'xyz')
            ->set('assigned_to', 'zzz')
            ->call('save')
            ->assertHasErrors(['title', 'status', 'due_date', 'client_id', 'opportunity_id', 'assigned_to']);
    }

    public function test_user_with_create_permission_can_create_task(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.create']);
        $client = Client::factory()->create(['company_id' => $user->company_id]);
        $opportunity = $this->createOpportunityForCompany($user->company_id);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('title', 'Ligar para cliente')
            ->set('description', 'Confirmar proposta')
            ->set('client_id', (string) $client->id)
            ->set('opportunity_id', (string) $opportunity->id)
            ->set('status', 'pending')
            ->set('due_date', Carbon::now()->format('Y-m-d'))
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Ligar para cliente',
            'client_id' => $client->id,
            'opportunity_id' => $opportunity->id,
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_without_update_permission_cannot_edit_task(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);
        $task = Task::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('openEditModal', $task->id)
            ->assertStatus(403);
    }

    public function test_user_with_update_permission_can_edit_task(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.update']);
        $task = Task::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('openEditModal', $task->id)
            ->set('title', 'Tarefa Atualizada')
            ->set('status', 'in_progress')
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tarefa Atualizada',
            'status' => 'in_progress',
            'company_id' => $user->company_id,
        ]);
    }

    public function test_user_without_delete_permission_cannot_delete_task(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);
        $task = Task::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('delete', $task->id)
            ->assertStatus(403);
    }

    public function test_user_with_delete_permission_can_delete_task(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.delete']);
        $task = Task::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('delete', $task->id);

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_without_assign_permission_cannot_set_assignee(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.create']);
        $assignee = User::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('title', 'Tarefa sem permissão de atribuição')
            ->set('assigned_to', (string) $assignee->id)
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarefa sem permissão de atribuição',
            'assigned_to' => null,
            'company_id' => $user->company_id,
        ]);
    }

    public function test_user_with_assign_permission_can_set_assignee(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.create', 'tasks.assign']);
        $assignee = User::factory()->create([
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('title', 'Tarefa atribuída')
            ->set('assigned_to', (string) $assignee->id)
            ->call('save');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarefa atribuída',
            'assigned_to' => $assignee->id,
            'company_id' => $user->company_id,
        ]);
    }

    public function test_user_with_update_permission_can_mark_done(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view', 'tasks.update']);
        $task = Task::factory()->create([
            'company_id' => $user->company_id,
            'status' => 'pending',
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('markDone', $task->id);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }

    public function test_user_without_update_permission_cannot_mark_done(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);
        $task = Task::factory()->create([
            'company_id' => $user->company_id,
            'status' => 'pending',
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->call('markDone', $task->id)
            ->assertStatus(403);
    }

    public function test_tasks_list_is_scoped_to_company(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);
        $otherCompany = \App\Models\Company::factory()->create();

        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Tarefa Alpha',
        ]);
        Task::factory()->create([
            'company_id' => $otherCompany->id,
            'title' => 'Tarefa Beta',
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->assertSee('Tarefa Alpha')
            ->assertDontSee('Tarefa Beta');
    }

    public function test_status_filter_returns_only_matching_tasks(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Tarefa Pendente',
            'status' => 'pending',
        ]);
        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Tarefa Concluída',
            'status' => 'done',
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('statusFilter', 'done')
            ->assertSee('Tarefa Concluída')
            ->assertDontSee('Tarefa Pendente');
    }

    public function test_search_filter_returns_only_matching_tasks(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Enviar proposta',
        ]);
        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Ligar para suporte',
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('search', 'proposta')
            ->assertSee('Enviar proposta')
            ->assertDontSee('Ligar para suporte');
    }

    public function test_date_filter_today_returns_only_today_tasks(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Hoje',
            'due_date' => Carbon::today()->format('Y-m-d'),
        ]);
        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Amanhã',
            'due_date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('dateFilter', 'today')
            ->assertSee('Hoje')
            ->assertDontSee('Amanhã');
    }

    public function test_date_filter_week_returns_only_this_week_tasks(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Esta semana',
            'due_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
        ]);
        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Próxima semana',
            'due_date' => Carbon::today()->addDays(10)->format('Y-m-d'),
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('dateFilter', 'week')
            ->assertSee('Esta semana')
            ->assertDontSee('Próxima semana');
    }

    public function test_date_filter_overdue_returns_only_overdue_tasks(): void
    {
        $user = $this->createUserWithPermissions(['tasks.view']);

        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Atrasada',
            'status' => 'pending',
            'due_date' => Carbon::today()->subDay()->format('Y-m-d'),
        ]);
        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'Concluída ontem',
            'status' => 'done',
            'due_date' => Carbon::today()->subDay()->format('Y-m-d'),
        ]);
        Task::factory()->create([
            'company_id' => $user->company_id,
            'title' => 'No prazo',
            'status' => 'pending',
            'due_date' => Carbon::today()->addDay()->format('Y-m-d'),
        ]);

        $this->actingAs($user);

        Livewire::test(TasksIndex::class)
            ->set('dateFilter', 'overdue')
            ->assertSee('Atrasada')
            ->assertDontSee('Concluída ontem')
            ->assertDontSee('No prazo');
    }
}
