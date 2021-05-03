<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\User;
use Exception;
use Flash;
use Illuminate\Http\Request;
use Response;

class ServiceController extends AppBaseController
{
    /**
     * Display a listing of the Service.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        /** @var Service $services */
        $services = Service::latest()->paginate(5);

        return view('services.index')
            ->with('services', $services);
    }

    /**
     * Show the form for creating a new Service.
     *
     * @return Response
     */
    public function create()
    {
        $users = User::role('Chef de service')->pluck('name', 'id');

        return view('services.create')->with('users', $users);
    }

    /**
     * Store a newly created Service in storage.
     *
     * @param CreateServiceRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceRequest $request)
    {
        $input = $request->all();

        /** @var Service $service */
        $service = Service::create($input);
        $user = User::find($service->responsible_id);
        $user->departement_id = $service->departement_id;
        $user->service_id = $service->id;
        $user->save();

        Flash::success('Service créé avec succès.');

        return redirect(route('services.index'));
    }

    /**
     * Display the specified Service.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Service $service */
        $service = Service::find($id);

        if (empty($service)) {
            Flash::error('Service not found');

            return redirect(route('services.index'));
        }

        return view('services.show')->with('service', $service);
    }

    /**
     * Show the form for editing the specified Service.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        /** @var Service $service */
        $service = Service::find($id);
        $users = User::role('Chef de service')->pluck('name', 'id');

        if (empty($service)) {
            Flash::error('Service non trouvé');

            return redirect(route('services.index'));
        }

        return view('services.edit')->with(['service' => $service, 'users' => $users]);
    }

    /**
     * Update the specified Service in storage.
     *
     * @param int $id
     * @param UpdateServiceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceRequest $request)
    {
        /** @var Service $service */
        $service = Service::find($id);

        if (empty($service)) {
            Flash::error('Service non trouvé');

            return redirect(route('services.index'));
        }

        $service->fill($request->all());
        $service->save();

        Flash::success('Service mis à jour avec succès.');

        return redirect(route('services.index'));
    }

    /**
     * Remove the specified Service from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws Exception
     *
     */
    public function destroy($id)
    {
        /** @var Service $service */
        $service = Service::find($id);

        if (empty($service)) {
            Flash::error('Service non trouvé');

            return redirect(route('services.index'));
        }

        $service->delete();

        Flash::success('Sservice supprimé avec succès.');

        return redirect(route('services.index'));
    }
}