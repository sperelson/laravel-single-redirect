# Single Redirect
## Laravel Middleware Package

**Please do not use this on production - it doesn't even have tests!**

**Purpose:** Add a middleware to your Laravel project that will reduce redirect hops down to 1 redirect.

Sometimes, through no fault of your own, you can end up with redirects after redirects before your
website's visitors finally end up on their destination page.

**Why:** SEO folk keep saying that a single redirect hop is better. This makes any SEO practitioner
giddy and you didn't have to do anything difficult to combine all the possible redirect hops into a
single one.

**How:** It works by placing itself (tries to anyway) as the last middleware and intercepts redirects.
Upon intercepting a redirect, it creates an internal request to check if the next response is also a
redirect. **Only works with GET requests!**

## Installation
composer require sperelson/laravel-single-redirect

## Extra information
The package adds a header called `single-redirect` on each internal request. And inserts the
`single-redirect` header into the final redirect response with the count of redirects. If `1` then it
did not do anything for you. If the count is higher than `1` then it turned the response into a single
redirect for the user.
