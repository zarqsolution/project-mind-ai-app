<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PortfolioSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the complete portfolio, copying gallery images into public storage
     * and using the first image of each project as its thumbnail.
     */
    public function run(): void
    {
        foreach ($this->projects() as $data) {
            $slug = Str::slug($data['title']);

            if (Project::query()->where('slug', $slug)->exists()) {
                continue;
            }

            $imagePaths = $this->imagePaths($data['folder']);

            $project = Project::query()->create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'thumbnail' => $imagePaths === []
                    ? null
                    : $this->storeFile($imagePaths[0], 'projects/thumbnails'),
            ]);

            foreach ($imagePaths as $index => $path) {
                $project->images()->create([
                    'title' => $this->imageTitle(basename($path), $data['title'], $index),
                    'image' => $this->storeFile($path, 'projects/images'),
                    'sort_order' => $index,
                ]);
            }
        }
    }

    /**
     * Collect the image paths for a project folder in natural sort order,
     * with first-level files followed by any nested folder files.
     *
     * @return list<string>
     */
    protected function imagePaths(?string $folder): array
    {
        if ($folder === null) {
            return [];
        }

        $directory = public_path('project-images/'.$folder);

        if (! File::isDirectory($directory)) {
            return [];
        }

        $paths = $this->sortedFilePaths($directory);

        foreach (File::directories($directory) as $subdirectory) {
            $paths = [...$paths, ...$this->sortedFilePaths($subdirectory)];
        }

        return $paths;
    }

    /**
     * List the files directly inside a directory, naturally sorted.
     *
     * @return list<string>
     */
    protected function sortedFilePaths(string $directory): array
    {
        $paths = array_map(
            fn ($file): string => $file->getPathname(),
            File::files($directory),
        );

        usort($paths, 'strnatcasecmp');

        return $paths;
    }

    /**
     * Copy a local file onto the public disk and return its stored path.
     */
    protected function storeFile(string $path, string $directory): string
    {
        $stored = Storage::disk('public')->putFile($directory, new HttpFile($path));

        if ($stored === false) {
            throw new RuntimeException("Unable to store [{$path}].");
        }

        return $stored;
    }

    /**
     * Build a human-friendly image title from a screenshot filename.
     */
    protected function imageTitle(string $filename, string $projectTitle, int $index): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        if (preg_match('/^(Screenshot|unnamed)/i', $name) === 1) {
            return "{$projectTitle} screen ".($index + 1);
        }

        $name = (string) preg_replace('/^screencapture-/', '', $name);
        $name = (string) preg_replace('/-\d{4}-\d{2}-\d{2}-\d{2}_\d{2}_\d{2}$/', '', $name);
        $name = (string) preg_replace(
            '/^(127-0-0-1-8000|localhost-8000|tech-freightanywhere|pa-logistics-test|sshr-catalist-me|new-interloper|interloper|quickjha|hawk-sports)-?/',
            '',
            $name,
        );
        $name = (string) preg_replace('/^index-php-/', '', $name);
        $name = (string) preg_replace('/-\d+(?=-|$)/', '', $name);

        if ($name === '') {
            return 'Homepage';
        }

        $title = Str::headline(str_replace('_', ' ', $name));
        $title = str_ireplace(['Dahbaord', 'Dashbord'], 'Dashboard', $title);

        return Str::limit($title, 100, '');
    }

    /**
     * The portfolio projects to seed.
     *
     * @return list<array{title: string, description: string, folder: string|null}>
     */
    protected function projects(): array
    {
        return [
            [
                'title' => 'Interloper Ecommerce App',
                'folder' => 'interloper Ecommerce App',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A robust, scalable full-stack e-commerce application built with Laravel, Livewire, and PostgreSQL, giving the business complete backend control over frontend content and functionality.</p>
<hr>
<h1>Features</h1>
<p>Comprehensive product attribute management with support for complex product configurations, image fetching and synchronization from eBay to streamline product imports, menu and navigation management, dynamic page creation and content management, customer review moderation, order processing and management, product catalog management with categories, attributes, and media, and an administrative dashboard with centralized control over all website operations.</p>
<hr>
<h1>Technologies</h1>
<p>Laravel, Livewire, PostgreSQL.</p>
<hr>
<h1>Keywords</h1>
<p>e-commerce, Laravel, Livewire, PostgreSQL, product catalog, eBay sync, product attributes, CMS, orders, admin dashboard</p>
HTML,
            ],
            [
                'title' => 'UploadMyProducts',
                'folder' => 'UploadMyProducts',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A Unified Multi-Platform (UMP) product management solution that lets businesses upload product data once and automatically distribute it across multiple e-commerce marketplaces, eliminating repetitive manual uploads and ensuring data consistency across all connected channels.</p>
<hr>
<h1>Features</h1>
<p>Single product entry with multi-platform synchronization, smart category mapping and attribute alignment for platform compatibility, centralized image management and optimization with background processing, real-time inventory synchronization across channels, performance-optimized architecture with background queue processing, and a scalable integration framework that is easily extendable to new sales platforms.</p>
<hr>
<h1>Technologies</h1>
<p>Custom backend module development, API integration and webhook communication, database optimization for high-volume product data, asynchronous processing for large catalog uploads, marketplace integration architecture.</p>
<hr>
<h1>Keywords</h1>
<p>multi-channel e-commerce, marketplace integration, product feed, inventory sync, catalog management, automation, API integration, dropshipping</p>
HTML,
            ],
            [
                'title' => 'FreightAnywhere TMS',
                'folder' => 'freightanywhere',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>FreightAnywhere is a full-cycle, web-based Transportation Management System (TMS) for freight brokerages and carriers, covering the entire life of a load in one platform: quoting, order entry, carrier sourcing, dispatch, live GPS tracking, billing, carrier settlement, and invoice auditing.</p>
<hr>
<h1>Features</h1>
<p>Shipment board with multi-filter search, saved views, and one-click actions; order workspace with a visual lifecycle pipeline and built-in customer credit control; Rate Maker with Carrier Target Pay and Max Buy Rate for margin protection; carrier management and a Pallet Rate System; live tracking on Trimble Maps with color-coded statuses and automated tracking logs; complete accounting back office with AR aging, customer statements, carrier settlements with deductions, balance sheet, income statement, commission reports, and factoring support; and an email-native invoice auditing inbox with team assignment and status workflow, extended by AI-assisted Tech-AI Auditing.</p>
<hr>
<h1>Integrations</h1>
<p>DAT Load Board posting with synced status badges, Trimble Maps commercial truck routing and mileage calculation, one-click Send Load to EAL for instant carrier sourcing, and an open API Settings module for connecting external systems, load boards, and tracking providers.</p>
<hr>
<h1>Keywords</h1>
<p>TMS, freight brokerage, logistics, dispatch, shipment tracking, carrier settlement, invoice auditing, DAT load board, Trimble Maps, accounting, AI auditing</p>
HTML,
            ],
            [
                'title' => 'MultiFeedInventory',
                'folder' => 'MultiFeedInventory',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>An enterprise-level dropshipping integration and automation platform built for Interloper Inc., designed to connect distributors, normalize product data, and automate inventory and order workflows. It serves as a centralized ecosystem for managing supplier integrations and maintaining consistent product information across multiple data sources, built for scalability, security, and high-speed data processing.</p>
<hr>
<h1>Features</h1>
<p>Advanced admin dashboard with real-time business overview and system health monitoring, distributor management for onboarding and monitoring multiple supplier accounts, a data normalization engine that standardizes titles, attributes, pricing, and inventory formats, product lifecycle management with synchronization between distributors and the central database, reporting and analytics, a compare data module for validating distributor data against live inventory feeds, shipping dimension management, metadata and SEO tools, bulk CSV/Excel uploads, scheduled pull settings automation, a secure API integration layer, and role-based access control.</p>
<hr>
<h1>Keywords</h1>
<p>dropshipping, data normalization, distributor integration, inventory automation, API architecture, e-commerce automation, product feeds, enterprise platform</p>
HTML,
            ],
            [
                'title' => 'PA Transport Logistics TMS',
                'folder' => 'pa-logistics',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A comprehensive logistics and transportation management platform that streamlines load operations, dispatching, carrier management, invoicing, shipment tracking, financial reporting, and customer communication. It centralizes the complete freight lifecycle from load creation to payment collection while integrating with third-party logistics and tracking services.</p>
<hr>
<h1>Features</h1>
<p>Logistics dashboard with real-time KPIs, complete load management with multi-stop pickup and delivery, dispatch planning and shipment assignment, live shipment tracking with MacroPoint, carrier management with MyCarrierPackets (MCP), customer, carrier, and invoice management, automated accounts receivable tracking and payment reminders, an email reminder system with reply tracking and classification, financial reports, income sheets, profitability and expense management, multi-division reporting, n8n workflow automation, CSV/Excel import and export, and a secure role-based admin portal.</p>
<hr>
<h1>Banking &amp; Transaction Engine</h1>
<p>Plaid integration connects multiple bank accounts and syncs raw transactions via cron jobs, feeding a rules-based transaction categorization engine that skips, reviews, categorizes, splits amounts across expense categories, and tags transactions for special cases.</p>
<hr>
<h1>Portals &amp; AI Tools</h1>
<p>Fully separated customer and carrier portals: customers track shipment progress while carriers view loads and upload invoices, BOLs, rate confirmations, and payment proofs. The portal has its own admin section with user management and impersonation. An AI document verification feature checks carrier-uploaded invoices and PODs against system data and generates comprehensive reports, complemented by a document split and crop tool.</p>
<hr>
<h1>Keywords</h1>
<p>logistics, TMS, dispatch, freight, MacroPoint, MyCarrierPackets, Plaid, banking, transaction categorization, carrier portal, customer portal, AI document verification, n8n</p>
HTML,
            ],
            [
                'title' => 'Petrol Pump Management System',
                'folder' => 'Petrol Management System',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A Laravel-based gas station management web application that streamlines fuel station operations including pumps, meter readings, sales, pricing, shifts, customers, and financials, providing operational control, inventory reconciliation, and audit-ready reports.</p>
<hr>
<h1>Features</h1>
<p>Fuel and inventory tracking with receipts and stock movements, pump, nozzle, and meter reading management with daily pump records, price history with historical pricing per fuel type, sales, payments, fuel dispensing logs, and customer transactions, shift and attendance management with working hours, customer, driver, and vehicle records for fleet transactions, expenses, petty cash, and employee advances, exportable and PDF reports for reconciliation and audits, and role-based access for admin and operator roles.</p>
<hr>
<h1>Technologies</h1>
<p>PHP, Laravel, Eloquent ORM, Blade, Livewire, Vite, Laravel Fortify, DomPDF, SQLite, Pest/PHPUnit.</p>
<hr>
<h1>Links</h1>
<p>https://gas-station.zarqsolution.com</p>
<hr>
<h1>Keywords</h1>
<p>gas station, petrol pump, fuel management, meter readings, shifts, attendance, POS, inventory reconciliation, Laravel, Livewire</p>
HTML,
            ],
            [
                'title' => 'QuickJHA',
                'folder' => 'QuickJHA',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>QuickJHA is an AI-powered SaaS platform that generates professional Job Hazard Analysis (JHA), Activity Hazard Analysis (AHA), and Job Safety Analysis (JSA) documents in around 30 seconds, applying country-specific safety regulations such as OSHA, Cal/OSHA, USACE, HSWA, and HSE to produce compliant safety documentation.</p>
<hr>
<h1>Features</h1>
<p>Pay-per-document pricing with Stripe and PayPal, PDF and editable Word downloads, document preview before purchase, in-browser editing, RAC-based risk scoring, company logo and branded formatting, draft save and resume, professional review upsell with expert revision and secure review links with admin impersonation, user dashboard, Google OAuth and email/password authentication, role-based access, admin pricing control, admin AI prompt and template customization, blog CMS, email campaigns and newsletters, SEO manager, file attachments to improve AI context, email verification, and an admin dashboard with revenue, usage, document stats, and per-document AI token and cost tracking.</p>
<hr>
<h1>Links</h1>
<p>https://quickjha.com</p>
<hr>
<h1>Keywords</h1>
<p>AI SaaS, safety documents, JHA, AHA, JSA, OSHA compliance, Stripe, PayPal, document generation, prompt engineering, blog CMS, email campaigns</p>
HTML,
            ],
            [
                'title' => 'C-Promoter',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A field force automation platform (web and mobile) for managing promoters, retail store visits, attendance, and project execution across multiple regions. Administrators assign projects, monitor field activities in real time, track GPS-based visits, and generate detailed reports, while the mobile app lets promoters check in and out of assigned stores using GPS, upload photos, and submit visit reports instantly.</p>
<hr>
<h1>Features</h1>
<p>User login and role management, promoter dashboard, daily visit planning, GPS-based check-in/check-out, store visit tracking with photo uploads at start and end, live attendance monitoring, working hours calculation, route and location verification, project and client management, promoter assignment, store and retail chain management, visit status tracking, real-time reports and analytics, country/region/city filters, product and campaign management, exportable reports, and secure cloud-based data management.</p>
<hr>
<h1>Keywords</h1>
<p>field force automation, promoter management, GPS check-in, retail visits, attendance, mobile app, reporting, campaign management</p>
HTML,
            ],
            [
                'title' => 'StockPro Inventory & Sales Management',
                'folder' => 'StockPRO ',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A complete Inventory Management and Point of Sale (POS) system for retail and wholesale businesses, covering daily sales, purchases, stock control, customer and supplier accounts, expenses, returns, and business reports from one dashboard.</p>
<hr>
<h1>Features</h1>
<p>Point of Sale with invoice generation and bill printing, stock in (purchasing) and stock out (sales), product returns, product and category management, multi-unit support, batch and expiry tracking, low stock alerts, customer and supplier management with ledgers, expense management, profit, sales, and inventory reports, due payment handling, dashboard analytics, multi-language support, shop settings, and automated Google Drive cloud database backup.</p>
<hr>
<h1>Links</h1>
<p>https://pos.zarqsolution.com</p>
<hr>
<h1>Keywords</h1>
<p>POS, inventory management, billing, invoices, stock control, batch expiry, ledgers, profit reports, multi-language, cloud backup</p>
HTML,
            ],
            [
                'title' => 'Hawk Sports',
                'folder' => 'hawk-sports',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>Hawk Sports is a WordPress WooCommerce e-commerce website selling premium sports equipment, including boxing gear, sports balls, hockey equipment, martial arts products, and motorbike clothing. Customers can browse products, compare items, manage wishlists, and securely purchase online.</p>
<hr>
<h1>Features</h1>
<p>WooCommerce store, user login and registration, product categories and search, product variations for size and color, shopping cart and checkout, wishlist and compare, product reviews, discount and sale products, newsletter subscription, responsive design, and order management.</p>
<hr>
<h1>Payments</h1>
<p>PayPal, Stripe, Visa, Mastercard, Apple Pay, Google Pay.</p>
<hr>
<h1>Links</h1>
<p>https://hawk-sports.com</p>
<hr>
<h1>Keywords</h1>
<p>WooCommerce, WordPress, e-commerce, sports equipment, online store, Stripe, PayPal, product variations</p>
HTML,
            ],
            [
                'title' => 'Tech Logistics',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>Tech Logistics is a premier provider of innovative transportation management solutions, leveraging AI-powered auditing and seasoned carrier negotiation expertise to maximize freight cost savings for businesses worldwide. A complete freight management spend solution.</p>
<hr>
<h1>Links</h1>
<p>https://www.techlogistics.com</p>
<hr>
<h1>Keywords</h1>
<p>freight audit, transportation management, AI auditing, carrier negotiation, logistics, cost savings</p>
HTML,
            ],
            [
                'title' => 'College Bound Movers',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>New Hampshire's #1 rated full-service moving company website. For more than 30 years, College Bound Movers has been providing service beyond expectation to customers throughout New Hampshire, New England, and beyond.</p>
<hr>
<h1>Links</h1>
<p>https://collegeboundmovers.com</p>
<hr>
<h1>Keywords</h1>
<p>moving company, full-service movers, business website, New Hampshire, lead generation</p>
HTML,
            ],
            [
                'title' => 'Iowa Ortho Healthcare Platform',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A fully responsive, user-centric web platform built for a leading orthopedic care provider. It enhances patient experience with easy access to services, providers, locations, and healthcare resources, streamlines appointment booking, and improves navigation for patients of all age groups, while handling multiple providers, specialties, and locations with high performance and scalability.</p>
<hr>
<h1>Links</h1>
<p>https://www.iowaortho.com</p>
<hr>
<h1>Keywords</h1>
<p>healthcare, orthopedic, patient experience, appointment booking, providers, responsive web platform</p>
HTML,
            ],
            [
                'title' => 'Star Medical & Research Center',
                'folder' => 'star-medical',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A complete hospital management system web application designed to streamline hospital and clinic operations, managing patients, doctors, appointments, pharmacy, laboratory, radiology, billing, and employee records from a centralized dashboard. It also includes an online patient test report portal where patients can securely view, download, and print laboratory reports, plus a database backup and restore system.</p>
<hr>
<h1>Features</h1>
<p>Interactive admin dashboard, patient registration and management, OPD and IPD management, doctor, employee, and staff management, department and role management, appointment scheduling, patient medical history and logs, laboratory test management, radiology management, pharmacy and medicine inventory with categories, purchases, sales, stock tracking, expiry monitoring, and reorder alerts, invoice and billing management with cumulative reports, health services management, contact and inquiry management, database export, trash and recovery module, secure authentication, and reports and analytics.</p>
<hr>
<h1>Links</h1>
<p>https://www.starmedicalresearchcenter.com</p>
<hr>
<h1>Keywords</h1>
<p>hospital management system, HMS, healthcare, OPD, IPD, pharmacy inventory, laboratory, radiology, billing, patient portal</p>
HTML,
            ],
            [
                'title' => 'SpyCameraCCTV',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>SpyCameraCCTV is an online e-commerce platform offering CCTV systems, spy cameras, wireless security cameras, and surveillance accessories for homes, businesses, farms, and wildlife monitoring.</p>
<hr>
<h1>Features</h1>
<p>User login, product categories and search, shopping cart, CCTV and spy cameras, wireless CCTV systems, secure online payments, product reviews, buyer's guide, and help and customer support.</p>
<hr>
<h1>Payments</h1>
<p>Visa, Mastercard, PayPal, Google Pay, Apple Pay, American Express.</p>
<hr>
<h1>Links</h1>
<p>https://www.spycameracctv.com</p>
<hr>
<h1>Keywords</h1>
<p>e-commerce, CCTV, security cameras, surveillance, online store, secure payments</p>
HTML,
            ],
            [
                'title' => 'Shipcl',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>Shipcl is a next-generation logistics platform designed to simplify the complexities of global and domestic shipping — the future of intelligent freight and shipping management.</p>
<hr>
<h1>Keywords</h1>
<p>logistics, shipping, freight management, global shipping, platform</p>
HTML,
            ],
            [
                'title' => 'Triple J Furniture',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>Triple J Furniture is an online furniture store offering a wide range of living room, dining, bedroom, home office, and outdoor furniture. Customers can browse products, place orders securely, and enjoy Australia-wide delivery.</p>
<hr>
<h1>Features</h1>
<p>User login and registration, product categories and search, shopping cart and checkout, secure online payments, order management, product reviews, wishlist, contact and support, and newsletter subscription.</p>
<hr>
<h1>Payments</h1>
<p>Visa, Mastercard, American Express, PayPal, Zip Pay, Shop Pay.</p>
<hr>
<h1>Links</h1>
<p>https://triplejfurniture.com.au</p>
<hr>
<h1>Keywords</h1>
<p>furniture, e-commerce, online store, checkout, wishlist, Australia, secure payments</p>
HTML,
            ],
            [
                'title' => 'MarsTranslation',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>MarsTranslation is an online translation platform providing professional human translation, localization, interpretation, and AI-powered language services, helping businesses translate documents, websites, software, videos, and other digital content into 230+ languages.</p>
<hr>
<h1>Features</h1>
<p>User registration and login, online translation order system, document translation, website and software localization, video translation and subtitling, interpretation services, AI translation services, translation project management with real-time tracking, translation memory, quality assurance, online price calculator, multi-language support, and customer support and consultation.</p>
<hr>
<h1>Links</h1>
<p>https://www.marstranslation.com</p>
<hr>
<h1>Keywords</h1>
<p>translation, localization, language services, subtitling, interpretation, AI translation, project management</p>
HTML,
            ],
            [
                'title' => 'LiveWaves',
                'folder' => null,
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>LiveWaves is a social networking and live streaming platform delivered as both a web application and a mobile app, combining community features with real-time live video broadcasting.</p>
<hr>
<h1>Keywords</h1>
<p>social networking, live streaming, video broadcasting, web app, mobile app, community</p>
HTML,
            ],
            [
                'title' => 'Price History Application',
                'folder' => 'Price History Comparison',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A full-stack smart price intelligence platform that tracks Amazon, Flipkart, and other marketplace prices and helps users buy at the right time. It shows price history, deal scores, and buy-or-wait recommendations from real product data, with hot deals, search, price alerts, store comparison, an admin panel for product imports, Keepa API integration, and an affiliate earning strategy.</p>
<hr>
<h1>Features</h1>
<p>Product detail pages with gallery, pricing, ratings, and affiliate buy links, interactive price history charts with time ranges from 24 hours to max, a Deal Scanner with buy/wait/avoid gauge and price statistics, a Hot Deals page with category, price, discount, store, and search filters, category pages with sorting and sidebar filters, search with live suggestions and trending queries, cross-store price comparison, wishlist and user dashboard, price alerts with email and WhatsApp notifications via UltraMsg, alert presets (6-month low, 10% drop, 20% drop), Google/Facebook login with 2FA and security settings, Keepa Excel bulk import via queued background jobs, Keepa API sync for stale prices, a price history engine, deal scoring logic, an admin panel for products, categories, brands, marketplaces, and SEO, auto-generated SEO meta content and XML sitemaps, analytics for page views and affiliate clicks, and a fully responsive homepage with hot deals, top price drops, and a live price ticker.</p>
<hr>
<h1>Links</h1>
<p>https://price-history.zarqsolution.com</p>
<hr>
<h1>Keywords</h1>
<p>price tracking, price history, deal score, Keepa API, Amazon, Flipkart, price alerts, WhatsApp notifications, affiliate marketing, SEO, analytics</p>
HTML,
            ],
            [
                'title' => 'SSHR – HR Management System',
                'folder' => 'SSHR',
                'description' => <<<'HTML'
<h1>Overview</h1>
<p>A Smart Human Resource (SSHR) system that streamlines HR operations by managing employee profiles, payroll, attendance, check-in/check-out, leave requests, insurance requests, and document records in one platform. It includes a web-based admin panel and a mobile application, letting employees access HR services and record attendance from anywhere.</p>
<hr>
<h1>Features</h1>
<p>Payroll management, attendance tracking with check-in/check-out, leave management with requests and approvals, loan requests with installment tracking, insurance requests and management, employee documents, announcements and notices, HR and employee dashboards, detailed reports and analytics including attendance, leave, and payroll reports, salary slip requests, user profile management, mobile application, admin backend panel, notifications and alerts, department and designation management, employee search and filters, and secure authentication and access control.</p>
<hr>
<h1>Keywords</h1>
<p>HR management, HRMS, payroll, attendance, leave management, loans, insurance, mobile app, employee self-service</p>
HTML,
            ],
        ];
    }
}
