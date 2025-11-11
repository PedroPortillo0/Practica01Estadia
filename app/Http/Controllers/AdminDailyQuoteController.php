<?php

namespace App\Http\Controllers;

use App\Application\UseCases\CreateDailyQuote;
use App\Application\UseCases\UpdateDailyQuote;
use App\Application\UseCases\DeleteDailyQuote;
use App\Application\UseCases\GetAllDailyQuotes;
use App\Domain\Ports\DailyQuoteRepositoryInterface;
use Illuminate\Http\Request;

class AdminDailyQuoteController extends Controller
{
    private CreateDailyQuote $createDailyQuote;
    private UpdateDailyQuote $updateDailyQuote;
    private DeleteDailyQuote $deleteDailyQuote;
    private GetAllDailyQuotes $getAllDailyQuotes;
    private DailyQuoteRepositoryInterface $dailyQuoteRepository;

    public function __construct(
        CreateDailyQuote $createDailyQuote,
        UpdateDailyQuote $updateDailyQuote,
        DeleteDailyQuote $deleteDailyQuote,
        GetAllDailyQuotes $getAllDailyQuotes,
        DailyQuoteRepositoryInterface $dailyQuoteRepository
    ) {
        $this->createDailyQuote = $createDailyQuote;
        $this->updateDailyQuote = $updateDailyQuote;
        $this->deleteDailyQuote = $deleteDailyQuote;
        $this->getAllDailyQuotes = $getAllDailyQuotes;
        $this->dailyQuoteRepository = $dailyQuoteRepository;
    }

    /**
     * Muestra el panel de administración
     */
    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 50);
        $category = $request->query('category', null);
        $search = $request->query('search', null);
        
        $result = $this->getAllDailyQuotes->execute($page, $limit, $category, $search);
        
        $total = $result['total'] ?? 0;
        $lastPage = $limit > 0 ? (int) ceil($total / $limit) : 1;
        
        // Obtener todas las categorías disponibles para el filtro
        $allCategories = $this->getAllCategories();
        
        return view('admin.daily-quotes.index', [
            'quotes' => $result['data'] ?? [],
            'total' => $total,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'limit' => $limit,
            'selectedCategory' => $category,
            'searchTerm' => $search,
            'categories' => $allCategories
        ]);
    }
    
    /**
     * Obtiene todas las categorías disponibles
     */
    private function getAllCategories(): array
    {
        return [
            'Filosofica' => 'Filosófica',
            'Estoica' => 'Estoica',
            'Motivacional' => 'Motivacional',
            'Inspiracional' => 'Inspiracional',
            'Liderazgo' => 'Liderazgo',
            'Sabiduria' => 'Sabiduría',
            'Exito' => 'Éxito',
            'Perseverancia' => 'Perseverancia'
        ];
    }

    /**
     * Muestra el formulario para crear una nueva frase
     */
    public function create()
    {
        // Obtener días ocupados para el calendario
        $occupiedDays = $this->getOccupiedDays();
        
        return view('admin.daily-quotes.create', [
            'occupiedDays' => $occupiedDays
        ]);
    }
    
    /**
     * Obtiene los días del año que ya están ocupados
     */
    private function getOccupiedDays(): array
    {
        $allQuotes = $this->dailyQuoteRepository->findAll();
        $occupiedDays = [];
        
        foreach ($allQuotes as $quote) {
            $dayOfYear = $quote->getDayOfYear();
            // Convertir día del año a fecha del año actual
            $date = $this->dayOfYearToDate($dayOfYear);
            $occupiedDays[] = $date;
        }
        
        return $occupiedDays;
    }
    
    /**
     * API endpoint para obtener días ocupados
     */
    public function getOccupiedDaysApi()
    {
        $occupiedDays = $this->getOccupiedDays();
        
        return response()->json([
            'success' => true,
            'occupiedDays' => $occupiedDays
        ]);
    }

    /**
     * Guarda una nueva frase
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'quote' => 'required|string|max:1000',
                'author' => 'required|string|max:100',
                'category' => 'required|string|max:50',
                'quote_date' => 'required|date',
                'is_active' => 'nullable|boolean'
            ]);

            // Convertir fecha a día del año
            $dayOfYear = $this->dateToDayOfYear($validated['quote_date']);
            
            // Validar que el día del año no esté duplicado
            $existingQuote = $this->dailyQuoteRepository->findByDayOfYear($dayOfYear);
            if ($existingQuote) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Ya existe una frase para el día ' . $dayOfYear . ' del año');
            }

            // Asegurar que is_active sea boolean
            $validated['is_active'] = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
            
            // Agregar day_of_year calculado
            $validated['day_of_year'] = $dayOfYear;

            $result = $this->createDailyQuote->execute($validated);

            return redirect()
                ->route('admin.daily-quotes.index')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar una frase
     */
    public function edit($id)
    {
        try {
            $quoteEntity = $this->dailyQuoteRepository->findById((int)$id);
            
            if (!$quoteEntity) {
                return redirect()
                    ->route('admin.daily-quotes.index')
                    ->with('error', 'Frase no encontrada');
            }

            $quoteArray = $quoteEntity->toArray();
            
            // Convertir día del año a fecha (usando año actual)
            $quoteArray['quote_date'] = $this->dayOfYearToDate($quoteArray['day_of_year']);
            
            // Obtener días ocupados para el calendario (excluyendo la fecha actual)
            $occupiedDays = $this->getOccupiedDays();
            $currentDate = $quoteArray['quote_date'];
            $occupiedDays = array_filter($occupiedDays, function($date) use ($currentDate) {
                return $date !== $currentDate;
            });

            return view('admin.daily-quotes.edit', [
                'quote' => $quoteArray,
                'occupiedDays' => $occupiedDays
            ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.daily-quotes.index')
                ->with('error', 'Error al obtener la frase: ' . $e->getMessage());
        }
    }
    
    /**
     * Convierte día del año a fecha (formato Y-m-d)
     */
    private function dayOfYearToDate(int $dayOfYear): string
    {
        $year = date('Y');
        $date = new \DateTime();
        $date->setDate($year, 1, 1);
        $date->modify('+' . ($dayOfYear - 1) . ' days');
        return $date->format('Y-m-d');
    }
    
    /**
     * Convierte fecha a día del año
     */
    private function dateToDayOfYear(string $date): int
    {
        $timestamp = strtotime($date);
        return (int) date('z', $timestamp) + 1;
    }

    /**
     * Actualiza una frase existente
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'quote' => 'required|string|max:1000',
                'author' => 'required|string|max:100',
                'category' => 'required|string|max:50',
                'quote_date' => 'required|date',
                'is_active' => 'nullable|boolean'
            ]);

            // Convertir fecha a día del año
            $dayOfYear = $this->dateToDayOfYear($validated['quote_date']);
            
            // Verificar que el día del año no esté duplicado (excepto la frase actual)
            $existingQuote = $this->dailyQuoteRepository->findByDayOfYear($dayOfYear);
            if ($existingQuote && $existingQuote->getId() != $id) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Ya existe una frase para el día ' . $dayOfYear . ' del año');
            }

            // Asegurar que is_active sea boolean
            $validated['is_active'] = isset($validated['is_active']) ? (bool)$validated['is_active'] : false;
            
            // Agregar day_of_year calculado
            $validated['day_of_year'] = $dayOfYear;

            $result = $this->updateDailyQuote->execute((int)$id, $validated);

            return redirect()
                ->route('admin.daily-quotes.index')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina una frase
     */
    public function destroy($id)
    {
        try {
            $result = $this->deleteDailyQuote->execute((int)$id);

            return redirect()
                ->route('admin.daily-quotes.index')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}

