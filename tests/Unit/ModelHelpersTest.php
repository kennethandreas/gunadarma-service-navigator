<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Service;
use Tests\TestCase;

class ModelHelpersTest extends TestCase
{
    public function test_categories_and_services_bind_routes_by_slug(): void
    {
        $this->assertSame('slug', (new Category())->getRouteKeyName());
        $this->assertSame('slug', (new Service())->getRouteKeyName());
    }

    public function test_category_icon_has_a_specific_emoji_for_known_slugs(): void
    {
        $category = new Category(['slug' => 'wisuda']);

        $this->assertSame('🎓', $category->icon());
    }

    public function test_category_icon_falls_back_to_a_generic_icon_for_unknown_slugs(): void
    {
        $category = new Category(['slug' => 'kategori-baru-yang-belum-dikenal']);

        $this->assertSame('🔗', $category->icon());
    }
}
