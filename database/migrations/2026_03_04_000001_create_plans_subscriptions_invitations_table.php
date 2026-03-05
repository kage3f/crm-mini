<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Free, Pro
            $table->string('slug')->unique(); // free, pro
            $table->integer('price_monthly'); // cents (0 = free)
            $table->integer('client_limit')->default(10); // -1 = unlimited
            $table->integer('user_limit')->default(3);    // -1 = unlimited
            $table->boolean('has_kanban')->default(false);
            $table->boolean('has_tasks')->default(false);
            $table->json('features')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained();
            $table->string('status')->default('active'); // active, cancelled, past_due
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('email');
            $table->string('token')->unique();
            $table->string('role')->default('member');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
