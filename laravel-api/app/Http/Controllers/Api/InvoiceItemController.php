<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/30/2021
 * Time: 4:55 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\InvoiceItemRequest;
use App\Http\Resources\InvoiceItemResource;
use App\Rental\Repositories\Contracts\InvoiceItemInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceItemController extends ApiController
{
    /**
     * @var InvoiceItemInterface
     */
    protected $invoiceItemRepository, $load, $accountRepository;

    /**
     * InvoiceItemController constructor.
     * @param InvoiceItemInterface $invoiceItemInterface
     */
    public function __construct(InvoiceItemInterface $invoiceItemInterface)
    {
        $this->invoiceItemRepository = $invoiceItemInterface;
        $this->load = ['transactions'];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->invoiceItemRepository->listAll($this->formatFields($select), []);
        } else
            $data = InvoiceItemResource::collection($this->invoiceItemRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param InvoiceItemRequest $request
     * @return array|mixed
     */
    public function store(InvoiceItemRequest $request)
    {
        $data = $request->all();
        $save = $this->invoiceItemRepository->create($data);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! InvoiceItem has been created.');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param InvoiceItemRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(InvoiceItemRequest $request, $id)
    {
        $save = $this->invoiceItemRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! InvoiceItem has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return array|mixed
     */
    public function destroy($id)
    {
        $save = $this->invoiceItemRepository->delete($id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! InvoiceItem has been updated.');
    }

    public function search(Request $request)
    {
        $data = $request->all();
        if (array_key_exists('filter', $data)) {
            $filter = $data['filter'];
            return $this->invoiceItemRepository->search($filter);
        }
    }
}
