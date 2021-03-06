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
        $paginatedResult = $this->kongService->getManyServices();
        $services = $paginatedResult->getData()->toArray();

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
     * @param string $id
     * @return View
     */
    public function edit(string $id): View
    {
        $service = $this->kongService->getOneService($id);
        if ($service === null) {
            throw new NotFoundHttpException("Service $id could not be found");
        }

        return view('services.edit', compact('service'));
    }

    /**
     * @param StoreService $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(StoreService $request, string $id): RedirectResponse
    {
        $service = $this->kongService->getOneService($id);
        if ($service === null) {
            throw new NotFoundHttpException("Service with id $id not found.");
        }

        $service->fill($request->validated());
        $this->kongService->putService($service);

        return redirect(route('services.show', ['service' => $service->getId()]));
    }

    /**
     * @param StoreService $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreService $request): RedirectResponse
    {
        $service = new Service();
        $service->fill($request->validated());
        $newService = $this->kongService->createService($service);

        return redirect(route('services.show', ['service' => $newService->getId()]));
    }

    /**
     * @param string $service
     * @return View
     */
    public function show(string $service): View
    {
        $existing = $this->kongService->getOneService($service);
        if ($existing === null) {
            throw new NotFoundHttpException();
        }

        return view('services.show', ['service' => $existing]);
    }
}
