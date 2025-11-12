<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now(); 
        $categories = [
            [
                'name' => 'Fasteners',
                'description' => 'Bolts, nuts, screws, washers, anchors, rivets, nails, staples, hooks.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Hand Tools',
                'description' => 'Hammers, screwdrivers, wrenches, pliers, measuring tapes, levels, clamps, saws (hand-powered).',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Power Tools',
                'description' => 'Drills, angle grinders, sanders, circular saws, rotary tools, batteries, and chargers.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Plumbing',
                'description' => 'Pipes (PVC, copper, PEX), fittings, valves, faucets, pipe cement, sealants.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Electrical',
                'description' => 'Wires, cables, conduit, outlets, switches, circuit breakers, extension cords, light bulbs.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Lumber & Wood',
                'description' => 'Boards, plywood, treated lumber, wooden dowels, moldings, shelving materials.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Paints & Supplies',
                'description' => 'Cans of paint, primers, stains, brushes, rollers, trays, painter\'s tape, drop cloths.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Hardware',
                'description' => 'Hinges, cabinet pulls, drawer slides, latches, security locks, door knobs, door closers.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Adhesives & Chemicals',
                'description' => 'Glue (wood, construction, super), epoxy, silicone caulk, lubricants (WD-40), solvents, mineral spirits.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Safety & Apparel',
                'description' => 'Gloves, safety glasses, ear protection, hard hats, safety vests, dust masks, work boots.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Lawn & Garden',
                'description' => 'Shovels, rakes, hoses, sprinklers, wheelbarrows, gardening gloves, fertilizers.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'name' => 'Automotive & Garage',
                'description' => 'Car cleaning supplies, motor oil, funnels, jumper cables, wheel chocks, garage organization.',
                'created_at' => $now, 
                'updated_at' => $now  
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
