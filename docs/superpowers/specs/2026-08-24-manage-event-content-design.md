# Manage Event Content Design

## Goal

Menambahkan content type Event yang terisolasi dari Article, mengikuti pola CMS Manage Article, dengan kategori sendiri, occurrence/session, admin CRUD, dan authenticated list/detail.

## Scope

- Event memiliki title, URI unik, summary, content, tags, thumbnail `events`, category, publication status (`draft`, `published`, `hidden`), dan visibility (`public`, `private`).
- Occurrence memiliki jadwal, timezone, mode/lokasi, registration window, lifecycle (`scheduled`, `cancelled`, `completed`), dan capacity wajib.
- Admin memakai module permission `manage_event` dan middleware permission generik yang sama dengan Manage Article.
- User login dapat membaca Event published/public; registrasi dipisahkan ke Booking MVP.
- Tidak termasuk guest access, recurrence, room/asset booking, payment, master venue, atau Layout Builder.

## Interfaces

- Admin route family: `/manage_event`, `/manage_event/add`, `/manage_event/edit/{idOrSlug}`, CRUD/list/category, dan nested occurrence endpoints.
- Attendee route family: `/event`, `/event/listdata`, `/event/{idOrSlug}`, dan occurrence read endpoint.
- Pagination memakai `Base_API_Rev_Controller`; mutations mengembalikan envelope `success`, `status`, `message`, dan `data` untuk request JSON.
- Migration aktif berada di `database/migrations`; legacy `database/migrations_new` tidak digunakan.

## Compatibility

- Article routes, controllers, models, views, assets, dan tables tetap untouched.
- Existing `Manage Event` parent is retained; legacy Add New and Category links are normalized, Layout is hidden until implemented.
- Existing account model/table and CKFinder session bridge are reused.
