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
