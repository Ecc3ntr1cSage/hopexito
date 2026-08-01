<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->longText('bio')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('instagram')->nullable();
                $table->string('dribble')->nullable();
                $table->string('behance')->nullable();
                $table->string('pinterest')->nullable();
                $table->string('deviantart')->nullable();
                $table->string('tiktok')->nullable();
                $table->string('website')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('artists')) {
            foreach (DB::table('artists')->get() as $artist) {
                DB::table('profiles')->updateOrInsert(
                    ['user_id' => $artist->id],
                    [
                        'bio' => $artist->bio,
                        'facebook' => $artist->facebook,
                        'twitter' => $artist->twitter,
                        'instagram' => $artist->instagram,
                        'dribble' => $artist->dribble,
                        'behance' => $artist->behance,
                        'pinterest' => $artist->pinterest,
                        'deviantart' => $artist->deviantart,
                        'tiktok' => $artist->tiktok,
                        'website' => $artist->website ?? null,
                        'created_at' => $artist->created_at ?? now(),
                        'updated_at' => $artist->updated_at ?? now(),
                    ]
                );
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role_id'));
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (! Schema::hasColumn('products', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id');
                }
                if (! Schema::hasColumn('products', 'product_type')) {
                    $table->string('product_type')->default('shirt')->after('slug');
                }
                if (! Schema::hasColumn('products', 'visibility')) {
                    $table->string('visibility')->default('public')->after('product_type');
                }
                if (! Schema::hasColumn('products', 'commission_rate')) {
                    $table->decimal('commission_rate', 5, 4)->default(0.15)->after('price');
                }
            });

            if (Schema::hasColumn('products', 'artist_id')) {
                DB::statement('UPDATE products SET user_id = artist_id WHERE user_id IS NULL');
            }
            if (Schema::hasColumn('products', 'category')) {
                DB::table('products')->where('category', 'Oversized')->update(['product_type' => 'sweat']);
                DB::table('products')->where('category', 'Shirt')->update(['product_type' => 'shirt']);
            }
            if (Schema::hasColumn('products', 'commission')) {
                DB::statement('UPDATE products SET commission_rate = 0.15');
            }
        }

        if (! Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('color');
                $table->string('image_front_path');
                $table->string('image_back_path')->nullable();
                $table->timestamps();
                $table->unique(['product_id', 'color']);
            });
        }

        if (Schema::hasTable('products') && Schema::hasTable('product_variants')) {
            foreach (DB::table('products')->get() as $product) {
                if (DB::table('product_variants')->where('product_id', $product->id)->exists()) {
                    continue;
                }

                $color = Schema::hasColumn('products', 'color')
                    ? trim(explode(',', (string) $product->color)[0] ?? 'White')
                    : 'White';
                $front = Schema::hasColumn('products', 'image_front_path')
                    ? ($product->image_front_path ?: $product->product_image_path)
                    : null;
                $back = Schema::hasColumn('products', 'image_back_path')
                    ? ($product->image_back_path ?: $product->product_image_2_path)
                    : null;

                if ($front) {
                    DB::table('product_variants')->insert([
                        'product_id' => $product->id,
                        'color' => $color,
                        'image_front_path' => $front,
                        'image_back_path' => $back,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if (Schema::hasTable('products_collection')) {
            Schema::table('products_collection', function (Blueprint $table) {
                if (! Schema::hasColumn('products_collection', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id');
                }
            });
            foreach (DB::table('products_collection')->whereNull('user_id')->get() as $collection) {
                $userId = DB::table('users')->where('name', $collection->name)->value('id');
                if ($userId) {
                    DB::table('products_collection')->where('id', $collection->id)->update(['user_id' => $userId]);
                }
            }

            if (Schema::hasColumn('products_collection', 'name')) {
                Schema::table('products_collection', fn (Blueprint $table) => $table->dropColumn('name'));
            }
        }

        if (Schema::hasTable('product_orders')) {
            Schema::table('product_orders', function (Blueprint $table) {
                if (! Schema::hasColumn('product_orders', 'is_owner_purchase')) {
                    $table->boolean('is_owner_purchase')->default(false)->after('color');
                }
            });
        }

        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                foreach (['shopname', 'discount'] as $column) {
                    if (Schema::hasColumn('carts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        foreach (['role_has_permissions', 'model_has_roles', 'model_has_permissions', 'permissions', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::dropIfExists('artists');
        Schema::dropIfExists('products_template');
        Schema::dropIfExists('custom_products');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('products_metadata');

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                foreach ([
                    'artist_id', 'shopname', 'commission', 'discount', 'color', 'category', 'image_front', 'image_front_path',
                    'image_back', 'image_back_path', 'product_image', 'product_image_path',
                    'product_image_2', 'product_image_2_path',
                ] as $column) {
                    if (Schema::hasColumn('products', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // This revamp intentionally replaces disposable demo data and legacy role infrastructure.
    }
};
