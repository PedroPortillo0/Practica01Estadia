<?php

namespace App\Http\Controllers;

use App\Application\UseCases\CreateDailyQuote;
use App\Application\UseCases\UpdateDailyQuote;
use App\Application\UseCases\DeleteDailyQuote;
use App\Application\UseCases\GetAllDailyQuotes;
use Illuminate\Http\Request;

class AdminDailyQuoteController extends Controller
{
    private CreateDailyQuote $createDailyQuote;
    private UpdateDailyQuote $updateDailyQuote;
    private DeleteDailyQuote $deleteDailyQuote;
    private GetAllDailyQuotes $getAllDailyQuotes;

    public function __construct(
        CreateDailyQuote $createDailyQuote,
        UpdateDailyQuote $updateDailyQuote,
        DeleteDailyQuote $deleteDailyQuote,
        GetAllDailyQuotes $getAllDailyQuotes
    ) {
        $this->createDailyQuote = $createDailyQuote;
        $this->updateDailyQuote = $updateDailyQuote;
        $this->deleteDailyQuote = $deleteDailyQuote;
        $this->getAllDailyQuotes = $getAllDailyQuotes;
    }

    /**
     * Muestra el panel de administración
     */
    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 50);
        
        $result = $this->getAllDailyQuotes->execute($page, $limit);
        
        $total = $result['total'] ?? 0;
        $lastPage = $limit > 0 ? (int) ceil($total / $limit) : 1;
        
        return view('admin.daily-quotes.index', [
            'quotes' => $result['data'] ?? [],
            'total' => $total,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'limit' => $limit
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva frase
     */
    public function create()
    {
        return view('admin.daily-quotes.create');
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
                'day_of_year' => 'required|integer|min:1|max:366|unique:daily_quotes,day_of_year',
                'is_active' => 'nullable|boolean'
            ]);

            // Asegurar que is_active sea boolean
            $validated['is_active'] = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;

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
        $result = $this->getAllDailyQuotes->execute(false);
        $quotes = $result['data'] ?? [];
        
        $quote = collect($quotes)->firstWhere('id', (int)$id);
        
        if (!$quote) {
            return redirect()
                ->route('admin.daily-quotes.index')
                ->with('error', 'Frase no encontrada');
        }

        return view('admin.daily-quotes.edit', ['quote' => $quote]);
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
                'day_of_year' => 'required|integer|min:1|max:366|unique:daily_quotes,day_of_year,' . $id,
                'is_active' => 'nullable|boolean'
            ]);

            // Asegurar que is_active sea boolean
            $validated['is_active'] = isset($validated['is_active']) ? (bool)$validated['is_active'] : false;

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

