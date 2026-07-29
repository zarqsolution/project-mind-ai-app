<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed two testing projects for embedding and AI search verification.
     */
    public function run(): void
    {
        Project::query()->updateOrCreate(
            ['slug' => 'fleettrack-logistics-platform'],
            [
                'title' => 'FleetTrack Logistics Platform',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A real-time logistics and fleet management platform for a regional trucking company, handling shipment tracking, route optimization, and driver dispatching.</p>
<hr>
<h1>Client</h1>
<p>A mid-sized logistics company operating 200+ trucks across the region.</p>
<hr>
<h1>Problem</h1>
<p>Dispatchers relied on phone calls and spreadsheets to track shipments, causing late deliveries and poor fleet utilization.</p>
<hr>
<h1>Solution</h1>
<p>A centralized platform with live GPS tracking, automated route planning, delivery ETAs, and proof-of-delivery capture for drivers.</p>
<hr>
<h1>My Role</h1>
<p>Full-stack developer responsible for the dispatch dashboard, GPS ingestion pipeline, and driver mobile API.</p>
<hr>
<h1>Technologies</h1>
<p>Laravel, PostgreSQL, Redis, WebSockets, React, Google Maps API.</p>
<hr>
<h1>Features</h1>
<p>Live map with vehicle positions, route optimization, shipment lifecycle tracking, driver assignments, delay alerts, and daily performance reports.</p>
<hr>
<h1>Keywords</h1>
<p>logistics, fleet management, shipment tracking, route optimization, dispatching, GPS, trucking, supply chain</p>
HTML,
            ],
        );

        Project::query()->updateOrCreate(
            ['slug' => 'shopstream-ecommerce-store'],
            [
                'title' => 'ShopStream E-commerce Store',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A multi-vendor e-commerce storefront with product catalogs, cart and checkout, online payments, and an admin panel for order management.</p>
<hr>
<h1>Client</h1>
<p>A retail startup selling fashion and lifestyle products online.</p>
<hr>
<h1>Problem</h1>
<p>The client sold through social media DMs with no structured catalog, inventory control, or online payment flow.</p>
<hr>
<h1>Solution</h1>
<p>A complete online store with product management, stock tracking, discount coupons, Stripe checkout, and automated order confirmation emails.</p>
<hr>
<h1>My Role</h1>
<p>Lead developer building the storefront, checkout flow, payment integration, and admin dashboard.</p>
<hr>
<h1>Technologies</h1>
<p>Laravel, MySQL, Inertia.js, React, Tailwind CSS, Stripe.</p>
<hr>
<h1>Features</h1>
<p>Product catalog with filters, shopping cart, secure checkout, order tracking, coupon system, sales analytics, and inventory alerts.</p>
<hr>
<h1>Keywords</h1>
<p>e-commerce, online store, shopping cart, checkout, payments, Stripe, retail, product catalog, inventory</p>
HTML,
            ],
        );
    }
}
