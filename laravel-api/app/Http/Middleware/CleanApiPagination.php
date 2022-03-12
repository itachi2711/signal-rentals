<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 3:45 PM
 */

namespace App\Http\Middleware;


use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class CleanApiPagination
{
    /**
     * Removes unwanted pagination information
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        if ($response->status() == 200 ) {
            $data = $response->getData(true);

            if (isset($data['links'])) {
                unset($data['links']);
            }
            if (isset($data['meta'], $data['meta']['links'])) {
                unset($data['meta']['links']);
            }
            if (isset($data['meta'], $data['meta']['path'])) {
                unset($data['meta']['path']);
            }

            $response->setData($data);
            return $response;
        }
        return $response;
    }

}
