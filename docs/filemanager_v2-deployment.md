# File Manager V2 deployment

## Runtime assets

The V2 page reads only the compiled public files in
`public/assets/plugins/filemanager_v2/`. It never calls Vite at runtime, so
visiting `/admin/file-manager-v2` does not require `npm run dev` or a running
Node process.

Run `npm run build:filemanager-v2` as part of a release only when the V2 Vue
source changes. The build also writes the Apache deny rule at
`storage/app/public/filemanager_v2/.htaccess`.

## Private paths

Only `storage/app/public/filemanager_v2/files/` is the file root used by V2.
The sibling `cache/` and `runtime/` folders must not be exposed directly.

Laravel registers an early `404` guard for every `/storage/filemanager_v2/*`
URL. Apache receives a second layer through the generated `.htaccess` file.
For Nginx deployments that serve `public/storage` as static content, add this
rule before the generic storage location:

```nginx
location ^~ /storage/filemanager_v2/ {
    return 404;
}
```

## Scheduled cleanup

`filemanager-v2:prune-uploads` runs hourly through Laravel's scheduler and
removes expired chunk-upload sessions from `runtime/uploads/`. Production still
needs the normal Laravel scheduler trigger (for example a cron or Windows Task
Scheduler entry that runs `php artisan schedule:run` each minute). This is a
server schedule, not a browser or npm process.

## Upload reliability on Laragon

The active Laragon installation uses eight PHP-CGI listeners (9003–9010), while
File Manager V2 transfers four files concurrently. Selecting five files or an
entire folder queues the remaining files. The default chunk size is 8 MiB;
files above 16 MiB use chunk sessions. These settings leave capacity for other
Laravel requests while uploads run.

PHP-CGI defaults to exiting after 500 requests when `PHP_FCGI_MAX_REQUESTS` is
unset. On this workstation the Windows user environment value is now `0`, and
the eight active PHP-CGI processes use that value. A future
Laragon launch must inherit the updated environment: fully close and reopen
Laragon when restarting it later. Do not launch a second Laragon instance to
reload Nginx. Long-running CGI processes still need ordinary monitoring.

The Phoenix vhost is `laravel-13-phoenix.aruna.conf` (without the `auto.`
prefix) so Laragon preserves the private-storage deny rule. Validate Nginx
configuration before reloading the existing master.

Browser recovery keys and server upload receipts last 24 hours. After a page
refresh or an interrupted upload, select the same files/folder, storage and
destination again to reuse those keys. Chunk sessions return the parts already
received; successful uploads return the saved asset instead of creating a
duplicate. Recovery does not silently regain browser access to local files.
Folder metadata is persisted at selection/batch boundaries, rather than
rewriting the complete browser journal after every file.

Every upload validates SHA-256 before and after storage writes. The server
reads the stored object to verify its size and hash, so R2 uploads also incur
read requests and verification time. Failed writes do not report success;
chunk sessions remain available for retry. Persistent receipts, sessions and
quota reservations must remain on the server across PHP restarts. Expired
receipts are removed by the existing prune command.

Five retries with backoff recover transient failures. They cannot guarantee
success during a sustained network outage, exhausted quota or storage failure.
