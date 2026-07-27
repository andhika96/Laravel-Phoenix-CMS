# File Manager V2: Upload Center and Safe Retry

## Goal

Make a large folder upload recoverable without losing its progress view: the upload panel can be reopened after close, failed items can be retried together after the queue reaches a terminal state, and a retry does not create a duplicate filename when the first upload actually reached storage but its HTTP response failed.

## Scope

- Keep the existing FilePond engine and the existing `maxParallel` setting.
- Add a persistent, reopenable Upload Center trigger in the File Manager header.
- Add a `Retry failed` action for completed folder batches and keep individual retry.
- Carry one stable client idempotency key per logical upload through direct and chunked requests.
- Persist the completed asset or in-progress chunk session against that key server-side, so a repeated request returns the original result rather than allocating `name (1).ext`.
- Do not expose or modify Laragon/PHP-FPM worker configuration in the application UI; that is a global service setting requiring a service restart.

## Implementation sequence

1. Add focused failing feature/source-contract tests for Upload Center/retry wiring and storage retry idempotency.
2. Back up every existing source file touched by the change.
3. Extend the folder coordinator with a retry-all operation that only activates after its batch is complete.
4. Wire App, FilePond metadata, and UploadPanel: preserve history on close, reopen from a header button, and retry all failed jobs.
5. Add minimal styling for the header counter and disabled retry state.
6. Add idempotency key support to the upload client, V2 controller validation, and storage service for direct and chunked upload paths.
7. Build the File Manager V2 bundle; run the focused and full FileManagerV2 test suites; check syntax and diff hygiene.
8. Refresh the existing Graphify graph incrementally and save the implementation checkpoint to memory.

## Safety notes

- Existing unrelated work remains untouched.
- Retried folder jobs continue to use the original destination path and original idempotency key.
- The retry action remains available only when the related batch has settled, avoiding a second queue admission during an active batch.
