<?php

namespace Perelson\SingleRedirect;

use Closure;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\HttpFoundation\Response;

class HandleSingleRedirect
{
    /**
     * Handle outgoing responses if they are redirects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $url = '';
        $infiniteLoop = 0;
        $response = $next($request);
        $responseStatus = $response->status();
        $method = $request->method();

        if (
            $method !== 'GET'
            || $request->hasHeader('single-redirect')
            || (
                $responseStatus < 300
                || $responseStatus >= 400
            )
        ) {
            return $response;
        }

        $cookies = $request->cookies->all();
        $server = $request->server->all();
        $newReponse = $response;
        $allowedRedirectCount = config('single-redirect.redirect-count');
        $methodToUse = config('single-redirect.use-request-method', false) ? $method : 'HEAD';

        while ($newReponse instanceof RedirectResponse) {
            $url = $newReponse->headers->get('location');

            if ($infiniteLoop++ > $allowedRedirectCount) {
                throw new \Exception("Infinite loop detected");
            }

            /** @var Kernel $kernel */
            $kernel = resolve(Kernel::class);

            /** @var Request $request2 */
            $request2 = Request::create(
                $url,
                $methodToUse ?? 'HEAD',
                [],
                $cookies ?? [],
                [],
                $server ?? []
            );
            $request2->headers->set('single-redirect', 'true');

            if ($extraHeaders = config('single-redirect.extra_headers')) {
                foreach ($extraHeaders as $key => $value) {
                    $request2->headers->set($key, $value);
                }
            }

            $newReponse = $kernel->handle($request2);

            if ($newReponse->headers->get('location') == $url) {
                throw new \Exception("Redirecting to the same url!");
            }
        }

        if (!empty($url)) {
            return redirect($url, $responseStatus, [
                'single-redirect' => $infiniteLoop,
            ]);
        }

        return $response;
    }
}
