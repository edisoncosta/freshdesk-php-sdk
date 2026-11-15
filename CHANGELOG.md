# Changelog

## [Unreleased] — 2.0.0

### Breaking changes

- Resource methods now return the decoded JSON array (or `null`) as documented,
  instead of the `Api` instance. The undeclared `$api->results` property was removed.
- `Contact::makeAgent($id, $data)` now sends `PUT` with an optional body (was `GET` with a query).
- Minimum PHP version is now 7.4.

### Fixed

- `Comment::create()` posted to `/discussions/topics/{id}/topics` instead of `/comments`.
- `Forum::create()`, `Folder::create()` and `Article::create()` were missing the
  `/forums`, `/folders` and `/articles` path suffix.
- `TimeEntry::toggle()` now calls `/time_entries/{id}/toggle_timer`.
- `SLAPolicy` used `/sla_policy` instead of `/sla_policies`.
- `unmonitor()` (forums/topics) now sends `DELETE` instead of `POST`.
- Empty API key or domain strings are now rejected.
- Network errors (e.g. `ConnectException`) are wrapped in `ApiException` instead of leaking Guzzle exceptions.
- `ApiException` now passes message, code and previous exception to the parent constructor.
- PHP 8.4+ deprecations for implicitly nullable parameters.

### Added

- `Api::getLastResponseHeaders()`.
- `ApiException::getResponse()`, `getStatusCode()`, `getResponseBody()` and `getErrors()`.
- Optional third constructor argument with extra Guzzle client options.
- PHPUnit test suite and GitHub Actions CI (PHP 7.4 – 8.5).
