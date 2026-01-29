<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');

            $table->string('status', 20)
                ->default('Open')
                ->comment('Ticket status: Open, Resolved');

            $table->string('category', 50)
                ->nullable()
                ->comment('AI classified category: Technical, Billing, General');

            $table->string('sentiment', 20)
                ->nullable()
                ->comment('AI sentiment enum: Positive, Neutral, Negative');

            $table->string('urgency', 20)
                ->nullable()
                ->comment('AI urgency enum: Low, Normal, High');

            $table->text('suggested_reply')
                ->nullable()
                ->comment('AI suggested response to the user');

            $table->timestamps();

            // Optional but nice
            $table->index(['status']);
            $table->index(['category']);
            $table->index(['sentiment']);
            $table->index(['urgency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
