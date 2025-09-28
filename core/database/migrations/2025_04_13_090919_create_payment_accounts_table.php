<?php

use App\Constants\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_type_id')->default(0);
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->decimal('balance',28,8)->default(0);
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->dateTime('deleted_at')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
