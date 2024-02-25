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
        $originalResponse = $next($request);
        $responseStatus = $originalResponse->status();
        $method = $request->method();
        $baseUrl = url('/');
        $previousResponse = null;

        if (
            $method !== 'GET'
            || $request->hasHeader('single-redirect')
            || (
                $responseStatus < 300
                || $responseStatus >= 400
            )
        ) {
            return $originalResponse;
        }

        $cookies = $request->cookies->all();
        $server = $request->server->all();
        $newResponse = $originalResponse;
        $allowedRedirectCount = config('single-redirect.redirect-count');
        $methodToUse = config('single-redirect.use-request-method', false) ? $method : 'HEAD';

        while ($newResponse instanceof RedirectResponse) {
            $url = $newResponse->headers->get('location');
            $previousResponse = $newResponse;

            // If the redirect is leaving our domain, proceed with the redirect
            if (str_starts_with($url, $baseUrl) === false) {
                return $newResponse;
            }

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

            $newResponse = $kernel->handle($request2);

            if ($newResponse->headers->get('location') == $url) {
                throw new \Exception("Redirecting to the same url!");
            }
        }

        if (!is_null($previousResponse)) {
            return $previousResponse->header('single-redirect', $infiniteLoop);
        }

        return $originalResponse;
    }
}
