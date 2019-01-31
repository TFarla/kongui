<?php

namespace App\Http\Controllers;

use App\Entity\Service;
use App\Http\Requests\StoreService;
use App\Service\KongService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contains CRUD operations to manage services
 *
 * Class ServiceController
 * @package App\Http\Controllers
 */
class ServiceController extends Controller
{
    /**
     * @var KongService
     */
    private $kongService;

    /**
     * ServiceController constructor.
     * @param KongService $kongService
     */
    public function __construct(KongService $kongService)
    {
        $this->middleware('auth');
        $this->kongService = $kongService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $services = $this->kongService->getAll();

        return view('services.index', compact('services'));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return view('services.create');
    }

    /**
     * @param StoreService $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreService $request): RedirectResponse
    {
        $data = $request->validated();
        $service = new Service();
        $service->setName($data['name']);
        $newService = $this->kongService->create($service);

        return redirect(route('services.show', ['service' => $newService->getId()]));
    }

    /**
     * @param string $service
     * @return View
     */
    public function show(string $service): View
    {
        $existing = $this->kongService->getOne($service);
        if ($existing === null) {
            throw new NotFoundHttpException();
        }

        return view('services.show', ['service' => $existing]);
    }
}
