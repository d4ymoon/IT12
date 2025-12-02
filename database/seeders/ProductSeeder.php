<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now(); 

        // 1. Get Category IDs and Prefixes (Assumes CategorySeeder ran first)
        $categories = DB::table('categories')->pluck('id', 'sku_prefix')->toArray();

        // 2. Get Supplier IDs (Assumes SupplierSeeder ran first)
        $default_supplier_id = 1;

        $products = [
            // --- FASTENERS (FSTNR - ID 1) ---
            [
                'sku' => 'FSTNR-00001',
                'name' => 'Wood Screw 1 inch (per piece)',
                'description' => 'Flat head wood screws, 1-inch length, zinc coating.',
                'category_id' => $categories['FSTNR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 1000,
            ],
            [
                'sku' => 'FSTNR-00002',
                'name' => 'Hex Bolt M6 x 20mm',
                'description' => 'Standard M6 hexagonal bolt, 20mm length.',
                'category_id' => $categories['FSTNR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 500,
            ],
            [
                'sku' => 'FSTNR-00003',
                'name' => 'Drywall Anchor Medium',
                'description' => 'Plastic drywall anchors for medium weight items.',
                'category_id' => $categories['FSTNR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 800,
            ],
            [
                'sku' => 'FSTNR-00004',
                'name' => 'Wing Nut M8',
                'description' => 'M8 wing nuts for hand-tightened applications.',
                'category_id' => $categories['FSTNR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 300,
            ],
            [
                'sku' => 'FSTNR-00005',
                'name' => 'Concrete Screw 3/16 x 2 inch',
                'description' => 'Blue concrete screws with hex head, 2-inch length.',
                'category_id' => $categories['FSTNR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 400,
            ],

            // --- HAND TOOLS (HNDTL - ID 2) ---
            [
                'sku' => 'HNDTL-00001',
                'name' => 'Measuring Tape 5 Meter',
                'description' => 'Retractable steel measuring tape, 5 meter length.',
                'category_id' => $categories['HNDTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 10,
            ],
            [
                'sku' => 'HNDTL-00002',
                'name' => 'Claw Hammer 16 oz',
                'description' => 'Fiberglass handle claw hammer, 16 ounce head.',
                'category_id' => $categories['HNDTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 15,
            ],
            [
                'sku' => 'HNDTL-00003',
                'name' => 'Screwdriver Set 6-Piece',
                'description' => 'Set of 6 screwdrivers with various head types.',
                'category_id' => $categories['HNDTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 8,
            ],
            [
                'sku' => 'HNDTL-00004',
                'name' => 'Adjustable Wrench 10 inch',
                'description' => 'Chromium-vanadium steel adjustable wrench.',
                'category_id' => $categories['HNDTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 12,
            ],
            [
                'sku' => 'HNDTL-00005',
                'name' => 'Utility Knife Heavy Duty',
                'description' => 'Heavy duty utility knife with retractable blade.',
                'category_id' => $categories['HNDTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 20,
            ],

            // --- POWER TOOLS (PWRTL - ID 3) ---
            [
                'sku' => 'PWRTL-00001',
                'name' => 'Cordless Drill 18V',
                'description' => '18V cordless drill with 2 batteries and charger.',
                'category_id' => $categories['PWRTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 5,
            ],
            [
                'sku' => 'PWRTL-00002',
                'name' => 'Angle Grinder 4.5 inch',
                'description' => '4.5 inch angle grinder with safety guard.',
                'category_id' => $categories['PWRTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 4,
            ],
            [
                'sku' => 'PWRTL-00003',
                'name' => 'Circular Saw 7-1/4 inch',
                'description' => '7-1/4 inch circular saw with laser guide.',
                'category_id' => $categories['PWRTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 3,
            ],
            [
                'sku' => 'PWRTL-00004',
                'name' => 'Random Orbital Sander',
                'description' => '5-inch random orbital sander with dust collection.',
                'category_id' => $categories['PWRTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 6,
            ],
            [
                'sku' => 'PWRTL-00005',
                'name' => 'Rotary Hammer Drill',
                'description' => 'Heavy duty rotary hammer drill for concrete.',
                'category_id' => $categories['PWRTL'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 2,
            ],

            // --- PLUMBING (PLMB - ID 4) ---
            [
                'sku' => 'PLMB-00001',
                'name' => 'PVC Pipe 1/2 inch x 10ft',
                'description' => 'Schedule 40 PVC pipe, 1/2 inch diameter, 10ft length.',
                'category_id' => $categories['PLMB'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 25,
            ],
            [
                'sku' => 'PLMB-00002',
                'name' => 'PVC Elbow Fitting 90°',
                'description' => '90 degree PVC elbow fitting for 1/2 inch pipe.',
                'category_id' => $categories['PLMB'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 50,
            ],
            [
                'sku' => 'PLMB-00003',
                'name' => 'Ball Valve 3/4 inch',
                'description' => 'Brass ball valve for 3/4 inch pipe.',
                'category_id' => $categories['PLMB'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 15,
            ],
            [
                'sku' => 'PLMB-00004',
                'name' => 'Pipe Thread Seal Tape',
                'description' => 'PTFE thread seal tape for plumbing connections.',
                'category_id' => $categories['PLMB'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 30,
            ],
            [
                'sku' => 'PLMB-00005',
                'name' => 'Basin Wrench',
                'description' => 'Telescoping basin wrench for tight spaces.',
                'category_id' => $categories['PLMB'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 8,
            ],

            // --- ELECTRICAL (ELEC - ID 5) ---
            [
                'sku' => 'ELEC-00001',
                'name' => 'Electrical Wire 14 AWG (per meter)',
                'description' => 'Solid copper 14 gauge electrical wire, sold by the meter.',
                'category_id' => $categories['ELEC'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 100,
            ],
            [
                'sku' => 'ELEC-00002',
                'name' => 'Wall Outlet Switch (Single)',
                'description' => 'Basic single gang wall outlet switch, white.',
                'category_id' => $categories['ELEC'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 20,
            ],
            [
                'sku' => 'ELEC-00003',
                'name' => 'Circuit Breaker 20A',
                'description' => '20 amp single pole circuit breaker.',
                'category_id' => $categories['ELEC'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 10,
            ],
            [
                'sku' => 'ELEC-00004',
                'name' => 'LED Bulb 9W Warm White',
                'description' => '9W LED bulb equivalent to 60W incandescent.',
                'category_id' => $categories['ELEC'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 40,
            ],
            [
                'sku' => 'ELEC-00005',
                'name' => 'Wire Connectors Assorted',
                'description' => 'Assorted pack of wire connectors (wire nuts).',
                'category_id' => $categories['ELEC'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 25,
            ],

            // --- LUMBER & WOOD (LMBR - ID 6) ---
            [
                'sku' => 'LMBR-00001',
                'name' => 'Plywood Sheet 4x8 1/2 inch',
                'description' => 'Standard plywood sheet 4x8 feet, 1/2 inch thickness.',
                'category_id' => $categories['LMBR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 5,
            ],
            [
                'sku' => 'LMBR-00002',
                'name' => '2x4 Lumber 8ft',
                'description' => 'Standard 2x4 lumber, 8 feet length.',
                'category_id' => $categories['LMBR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 20,
            ],
            [
                'sku' => 'LMBR-00003',
                'name' => 'Wood Dowel 1/2 inch',
                'description' => 'Hardwood dowel rods, 1/2 inch diameter.',
                'category_id' => $categories['LMBR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 50,
            ],
            [
                'sku' => 'LMBR-00004',
                'name' => 'Crown Molding 8ft',
                'description' => 'Primed wood crown molding, 8 feet length.',
                'category_id' => $categories['LMBR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 15,
            ],
            [
                'sku' => 'LMBR-00005',
                'name' => 'Particle Board 4x8',
                'description' => 'Standard particle board sheet 4x8 feet.',
                'category_id' => $categories['LMBR'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 8,
            ],

            // --- PAINTS & SUPPLIES (PNT - ID 7) ---
            [
                'sku' => 'PNT-00001',
                'name' => 'Interior Latex Paint White 1 Gallon',
                'description' => 'Premium interior latex paint, white, 1 gallon.',
                'category_id' => $categories['PNT'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 10,
            ],
            [
                'sku' => 'PNT-00002',
                'name' => 'Paint Brush 3 inch',
                'description' => 'Professional 3-inch angled paint brush.',
                'category_id' => $categories['PNT'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 25,
            ],
            [
                'sku' => 'PNT-00003',
                'name' => 'Paint Roller Kit',
                'description' => 'Complete paint roller kit with tray and covers.',
                'category_id' => $categories['PNT'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 12,
            ],
            [
                'sku' => 'PNT-00004',
                'name' => 'Painter\'s Tape 1 inch',
                'description' => '1-inch blue painter\'s tape for clean edges.',
                'category_id' => $categories['PNT'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 30,
            ],
            [
                'sku' => 'PNT-00005',
                'name' => 'Drop Cloth 9x12',
                'description' => 'Canvas drop cloth 9x12 feet for floor protection.',
                'category_id' => $categories['PNT'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 8,
            ],

            // --- HARDWARE (HARDW - ID 8) ---
            [
                'sku' => 'HARDW-00001',
                'name' => 'Cabinet Hinge Standard',
                'description' => 'Standard overlay cabinet hinge, nickel finish.',
                'category_id' => $categories['HARDW'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 40,
            ],
            [
                'sku' => 'HARDW-00002',
                'name' => 'Door Knob Passage',
                'description' => 'Passage door knob set, bronze finish.',
                'category_id' => $categories['HARDW'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 15,
            ],
            [
                'sku' => 'HARDW-00003',
                'name' => 'Drawer Slide 14 inch',
                'description' => 'Side mount drawer slides, 14 inch length.',
                'category_id' => $categories['HARDW'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 20,
            ],
            [
                'sku' => 'HARDW-00004',
                'name' => 'Cabinet Pull 3 inch',
                'description' => 'Modern cabinet pull, 3 inch center-to-center.',
                'category_id' => $categories['HARDW'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 25,
            ],
            [
                'sku' => 'HARDW-00005',
                'name' => 'Deadbolt Lock',
                'description' => 'Single cylinder deadbolt lock with keys.',
                'category_id' => $categories['HARDW'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 10,
            ],

            // --- ADHESIVES & CHEMICALS (CHEM - ID 9) ---
            [
                'sku' => 'CHEM-00001',
                'name' => 'Wood Glue 16 oz',
                'description' => 'PVA wood glue, 16 ounce bottle.',
                'category_id' => $categories['CHEM'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 20,
            ],
            [
                'sku' => 'CHEM-00002',
                'name' => 'Silicone Caulk White',
                'description' => 'Waterproof silicone caulk, white, 10.1 oz.',
                'category_id' => $categories['CHEM'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 25,
            ],
            [
                'sku' => 'CHEM-00003',
                'name' => 'Super Glue 3-pack',
                'description' => 'Instant bond super glue, 3-pack.',
                'category_id' => $categories['CHEM'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 30,
            ],
            [
                'sku' => 'CHEM-00004',
                'name' => 'Construction Adhesive',
                'description' => 'Heavy duty construction adhesive, 10 oz.',
                'category_id' => $categories['CHEM'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 15,
            ],
            [
                'sku' => 'CHEM-00005',
                'name' => 'WD-40 Lubricant',
                'description' => 'Multi-use lubricant and penetrant, 11 oz.',
                'category_id' => $categories['CHEM'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 18,
            ],

            // --- SAFETY & APPAREL (SAFE - ID 10) ---
            [
                'sku' => 'SAFE-00001',
                'name' => 'Safety Glasses Clear',
                'description' => 'ANSI approved clear safety glasses.',
                'category_id' => $categories['SAFE'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 30,
            ],
            [
                'sku' => 'SAFE-00002',
                'name' => 'Work Gloves Leather',
                'description' => 'Durable leather work gloves, pair.',
                'category_id' => $categories['SAFE'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 25,
            ],
            [
                'sku' => 'SAFE-00003',
                'name' => 'Dust Mask N95',
                'description' => 'N95 particulate respirator, 10-pack.',
                'category_id' => $categories['SAFE'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 40,
            ],
            [
                'sku' => 'SAFE-00004',
                'name' => 'Ear Protection Muffs',
                'description' => 'Noise reduction ear protection muffs.',
                'category_id' => $categories['SAFE'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 12,
            ],
            [
                'sku' => 'SAFE-00005',
                'name' => 'Hard Hat Yellow',
                'description' => 'ANSI approved hard hat, yellow.',
                'category_id' => $categories['SAFE'],
                'manufacturer_barcode' => null,
                'default_supplier_id' => $default_supplier_id, 
                'quantity_in_stock' => 0,
                'reorder_level' => 8,
            ],
        ];

        // Add timestamps and default fields
        $products = array_map(function ($product) use ($now) {
            $product['image_path'] = null;
            $product['is_active'] = true;
            $product['date_disabled'] = null;
            $product['disabled_by_user_id'] = null;
            $product['created_at'] = $now;
            $product['updated_at'] = $now;
            return $product;
        }, $products);

        DB::table('products')->insert($products);

        // Now create stock-in records for these products
        $this->createStockInRecords();
    }

    /**
     * Create stock-in records for the seeded products
     */
    private function createStockInRecords(): void
    {
        $now = Carbon::now();
        $default_supplier_id = 1;
        $received_by_user_id = 1; // Assuming user ID 1 exists

        // Get all product IDs we just created
        $productIds = DB::table('products')->pluck('id', 'sku')->toArray();

        // Define stock-in data with unit costs and retail prices
        $stockInData = [
            // Format: [quantity, unit_cost, retail_price]
            'FSTNR-00001' => [5000, 0.75, 1.50],
            'FSTNR-00002' => [2000, 2.50, 5.00],
            'FSTNR-00003' => [3000, 0.25, 0.60],
            'FSTNR-00004' => [1500, 1.20, 2.80],
            'FSTNR-00005' => [1800, 3.75, 8.50],

            'HNDTL-00001' => [100, 125.00, 250.00],
            'HNDTL-00002' => [80, 350.00, 699.00],
            'HNDTL-00003' => [60, 180.00, 350.00],
            'HNDTL-00004' => [70, 280.00, 550.00],
            'HNDTL-00005' => [120, 45.00, 95.00],

            'PWRTL-00001' => [25, 1200.00, 2499.00],
            'PWRTL-00002' => [20, 850.00, 1699.00],
            'PWRTL-00003' => [15, 1500.00, 2999.00],
            'PWRTL-00004' => [30, 600.00, 1199.00],
            'PWRTL-00005' => [10, 2200.00, 4499.00],

            'PLMB-00001' => [50, 120.00, 240.00],
            'PLMB-00002' => [200, 8.00, 18.00],
            'PLMB-00003' => [60, 150.00, 320.00],
            'PLMB-00004' => [100, 12.00, 25.00],
            'PLMB-00005' => [40, 280.00, 550.00],

            'ELEC-00001' => [500, 18.00, 36.00],
            'ELEC-00002' => [200, 60.00, 120.00],
            'ELEC-00003' => [50, 180.00, 350.00],
            'ELEC-00004' => [150, 45.00, 95.00],
            'ELEC-00005' => [100, 25.00, 55.00],

            'LMBR-00001' => [20, 450.00, 899.00],
            'LMBR-00002' => [80, 85.00, 180.00],
            'LMBR-00003' => [200, 15.00, 35.00],
            'LMBR-00004' => [60, 120.00, 250.00],
            'LMBR-00005' => [25, 320.00, 650.00],

            'PNT-00001' => [40, 350.00, 699.00],
            'PNT-00002' => [80, 45.00, 95.00],
            'PNT-00003' => [50, 120.00, 250.00],
            'PNT-00004' => [120, 30.00, 65.00],
            'PNT-00005' => [35, 180.00, 350.00],

            'HARDW-00001' => [150, 12.00, 28.00],
            'HARDW-00002' => [60, 120.00, 250.00],
            'HARDW-00003' => [80, 45.00, 95.00],
            'HARDW-00004' => [100, 18.00, 40.00],
            'HARDW-00005' => [40, 150.00, 320.00],

            'CHEM-00001' => [80, 45.00, 95.00],
            'CHEM-00002' => [100, 35.00, 75.00],
            'CHEM-00003' => [120, 25.00, 55.00],
            'CHEM-00004' => [60, 55.00, 120.00],
            'CHEM-00005' => [70, 65.00, 140.00],

            'SAFE-00001' => [120, 15.00, 35.00],
            'SAFE-00002' => [90, 85.00, 180.00],
            'SAFE-00003' => [150, 12.00, 28.00],
            'SAFE-00004' => [50, 120.00, 250.00],
            'SAFE-00005' => [35, 95.00, 200.00],
        ];

        // Create stock-in header
        $stockInId = DB::table('stock_ins')->insertGetId([
            'stock_in_date' => $now,
            'reference_no' => 'SEED-001',
            'received_by_user_id' => $received_by_user_id,
            'status' => 'completed',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create stock-in items and product prices
        foreach ($stockInData as $sku => $data) {
            list($quantity, $unitCost, $retailPrice) = $data;
            
            $productId = $productIds[$sku];

            // Create stock-in item
            DB::table('stock_in_items')->insert([
                'stock_in_id' => $stockInId,
                'product_id' => $productId,
                'supplier_id' => $default_supplier_id,  
                'quantity_received' => $quantity,
                'actual_unit_cost' => $unitCost,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Create product price
            DB::table('product_prices')->insert([
                'product_id' => $productId,
                'retail_price' => $retailPrice,
                'stock_in_id' => $stockInId,
                'updated_by_user_id' => $received_by_user_id, 
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Update product quantity_in_stock
            DB::table('products')
                ->where('id', $productId)
                ->update([
                    'quantity_in_stock' => $quantity,
                    'latest_unit_cost' => $unitCost, 
                    'updated_at' => $now,
                ]);
        }
    }
}