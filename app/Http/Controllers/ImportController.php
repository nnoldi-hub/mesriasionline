<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Location;

class ImportController extends Controller
{
    /**
     * Show import form
     */
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Preview import data before actual import
     */
    public function previewCraftsmen(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('file');
            $data = $this->parseCsvFile($file);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fișierul CSV este gol sau invalid.',
                ], 422);
            }

            // Validate all rows
            $validRows = [];
            $errorRows = [];

            foreach ($data as $index => $row) {
                $validation = $this->validateCraftsmanRow($row, $index + 2); // +2 for header and 0-index

                if ($validation['valid']) {
                    $validRows[] = $row;
                } else {
                    $errorRows[] = [
                        'row' => $index + 2,
                        'data' => $row,
                        'errors' => $validation['errors'],
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'preview' => [
                    'total' => count($data),
                    'valid' => count($validRows),
                    'invalid' => count($errorRows),
                    'validRows' => array_slice($validRows, 0, 10), // Show first 10 valid
                    'errorRows' => $errorRows,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Import preview error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Eroare la procesarea fișierului: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import craftsmen from CSV
     */
    public function importCraftsmen(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'skip_duplicates' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $data = $this->parseCsvFile($file);
            $skipDuplicates = $request->boolean('skip_duplicates', true);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                $validation = $this->validateCraftsmanRow($row, $index + 2);

                if (!$validation['valid']) {
                    $errors[] = [
                        'row' => $index + 2,
                        'errors' => $validation['errors'],
                    ];
                    continue;
                }

                // Check for duplicate email
                if (User::where('email', $row['email'])->exists()) {
                    if ($skipDuplicates) {
                        $skipped++;
                        continue;
                    } else {
                        $errors[] = [
                            'row' => $index + 2,
                            'errors' => ['Email-ul există deja în baza de date.'],
                        ];
                        continue;
                    }
                }

                // Create craftsman
                $craftsman = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'password' => bcrypt($row['password'] ?? 'password123'),
                    'role' => 'craftsman',
                    'is_active' => $row['is_active'] ?? true,
                    'location_id' => $this->getOrCreateLocation($row['location'] ?? null),
                    'bio' => $row['bio'] ?? null,
                    'years_of_experience' => $row['years_of_experience'] ?? 0,
                ]);

                // Attach services if provided
                if (!empty($row['services'])) {
                    $serviceIds = $this->resolveServiceIds($row['services']);
                    if (!empty($serviceIds)) {
                        $craftsman->services()->sync($serviceIds);
                    }
                }

                $imported++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import finalizat cu succes!",
                'stats' => [
                    'imported' => $imported,
                    'skipped' => $skipped,
                    'errors' => count($errors),
                ],
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import craftsmen error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Eroare la import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import services from CSV
     */
    public function importServices(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $data = $this->parseCsvFile($file);

            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                if (empty($row['name'])) {
                    $errors[] = [
                        'row' => $index + 2,
                        'errors' => ['Numele serviciului este obligatoriu.'],
                    ];
                    continue;
                }

                // Check for duplicate
                if (Service::where('name', $row['name'])->exists()) {
                    continue; // Skip duplicates
                }

                Service::create([
                    'name' => $row['name'],
                    'slug' => \Str::slug($row['name']),
                    'description' => $row['description'] ?? null,
                    'category_id' => $this->resolveCategoryId($row['category'] ?? null),
                    'base_price' => $row['base_price'] ?? null,
                    'unit' => $row['unit'] ?? 'serviciu',
                ]);

                $imported++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Au fost importate {$imported} servicii.",
                'stats' => [
                    'imported' => $imported,
                    'errors' => count($errors),
                ],
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import services error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Eroare la import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download CSV template for craftsmen import
     */
    public function downloadCraftsmenTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_meseriasi.csv"',
        ];

        $csvData = [
            ['name', 'email', 'phone', 'password', 'location', 'bio', 'years_of_experience', 'services', 'is_active'],
            ['Ion Popescu', 'ion.popescu@example.com', '0722123456', 'parola123', 'București', 'Electrician cu 10 ani experiență', '10', 'Instalații electrice,Reparații electrice', 'true'],
            ['Maria Ionescu', 'maria.ionescu@example.com', '0733987654', 'parola123', 'Cluj-Napoca', 'Instalator profesionist', '5', 'Instalații sanitare,Reparații', 'true'],
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV template for services import
     */
    public function downloadServicesTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_servicii.csv"',
        ];

        $csvData = [
            ['name', 'description', 'category', 'base_price', 'unit'],
            ['Instalații electrice', 'Montare prize, întrerupătoare, corpuri de iluminat', 'Electrician', '150', 'oră'],
            ['Zugrăveli interioare', 'Vopsire pereți interiori', 'Zugravi & Vopsitori', '50', 'mp'],
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Parse CSV file
     */
    protected function parseCsvFile($file): array
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Get headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        // Read rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        fclose($handle);
        return $data;
    }

    /**
     * Validate craftsman row
     */
    protected function validateCraftsmanRow(array $row, int $rowNumber): array
    {
        $validator = Validator::make($row, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|regex:/^[0-9]{10}$/',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        return ['valid' => true];
    }

    /**
     * Get or create location by name
     */
    protected function getOrCreateLocation(?string $locationName): ?int
    {
        if (!$locationName) {
            return null;
        }

        $location = Location::firstOrCreate(
            ['name' => trim($locationName)],
            ['slug' => \Str::slug($locationName)]
        );

        return $location->id;
    }

    /**
     * Resolve service IDs from comma-separated names
     */
    protected function resolveServiceIds(string $servicesString): array
    {
        $serviceNames = array_map('trim', explode(',', $servicesString));
        
        return Service::whereIn('name', $serviceNames)->pluck('id')->toArray();
    }

    /**
     * Resolve category ID from name
     */
    protected function resolveCategoryId(?string $categoryName): ?int
    {
        if (!$categoryName) {
            return null;
        }

        $category = Category::where('name', trim($categoryName))->first();
        return $category?->id;
    }
}
