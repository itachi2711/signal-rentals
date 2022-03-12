<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\GeneralSetting;
use App\Models\Lease;
use App\Models\LeaseSetting;
use App\Rental\Repositories\Contracts\AccountInterface;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class AccountController extends ApiController
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository, $load;

    /**
     * AccountController constructor.
     * @param AccountInterface $accountInterface
     */
    public function __construct(AccountInterface $accountInterface)
    {
        $this->accountRepository = $accountInterface;
        $this->load = ['journalDebitEntries', 'journalCreditEntries'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->accountRepository->listAll($this->formatFields($select));
        }
        $data = $this->accountRepository->getAllPaginate($this->load);

        $data->map(function($item) {
            $item['accountBalance'] =  format_money($this->accountRepository->accountBalance($item['id']));
            return $item;
        });
        return $this->respondWithData(AccountResource::collection($data));
    }

    /**
     * @param AccountRequest $request
     * @return mixed
     */
    public function store(AccountRequest $request)
    {
        $save = $this->accountRepository->create($request->all());

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Account has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $account = $this->accountRepository->getById($uuid);

        if (!$account) {
            return $this->respondNotFound('Account not found.');
        }
        return $this->respondWithData(new AccountResource($account));

    }

    /**
     * @param AccountRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AccountRequest $request, $uuid)
    {
        $save = $this->accountRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! Account has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        return $this->respondNotFound('Account not deleted');
    }

    /**
     * Fetch statement given a $loanId
     * @param Request $request
     * @return mixed
     */
    public function leaseAccountStatement(Request $request) {
        $data = $request->all();
        $leaseID = $data['id'];
        if(isset($data['pdf'])){
            $request['type'] = 'lease';
            return $this->downloadAccountStatement($request);
        }
        $account = Account::where('lease_id', $leaseID)
            ->where('account_name', LEASE_ACCOUNT)
            ->first();
        if (isset($account))
            $account['statement'] = $this->accountRepository->fetchAccountStatement($account->id);
        return $this->respondWithData(new AccountResource($account));
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    private function downloadAccountStatement(Request $request) {
        $account = $this->getAccount($request->all());
        $settings = GeneralSetting::first();
        $leaseSettings = LeaseSetting::first();
        $file_path = $settings->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
        $settings->logo_url = $local_path;
        $settings->invoice_footer = $leaseSettings->invoice_footer;

        if (isset($account)){
            $lease = [];
            if (isset($account->lease_id)) {
                $lease =  Lease::with('tenants', 'property', 'units')->find($account->lease_id);
            }

            $rawStatement = $this->accountRepository->fetchAccountStatement($account->id);
            $account['statement'] =  $rawStatement;
           // $pageData = AccountResource::make($account)->toArray($request);
            $pageData = AccountResource::make($account)->resolve();
            $pdf = PDF::loadView('reports.account-statement', compact('pageData', 'settings', 'lease'));
           // return view('reports.account-statement', compact('pageData', 'setting'));
            return $pdf->download('statement.pdf');
        }
        return null;
    }

    /**
     * Special cases for member deposit and loan accounts as compared to other accounts
     * @param $data
     * @return mixed
     */
    private function getAccount($data) {
        switch ($data['type']){
            case  'lease' :
                return Account::where('lease_id', $data['id'])->where('account_type', LEASE_ACCOUNT)
                    ->first();
                break;
            default :
                return $this->accountRepository->getById($data['id']);
                break;
        }
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function generalAccountStatement(Request $request) {
        $data = $request->all();
        $uuid = $data['id'];
        if(isset($data['pdf']) && $data['pdf'] == true){
            $request['type'] = 'general';
            return $this->downloadAccountStatement($request);
        }
        $account = $this->accountRepository->getById($uuid);
        if (isset($account))
            $account['statement'] = $this->accountRepository->fetchAccountStatement($account->id);
        return $this->respondWithData(new AccountResource($account));
    }
}
