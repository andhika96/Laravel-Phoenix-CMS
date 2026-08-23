# Product Lead Form — Final Style Options Matrix

Tanggal: 2026-08-23

## Source and renderer audit

Control-binding audit terhadap module `product_lead_form`:

- 160 Product Lead Form controls ditemukan.
- 140 memiliki consumer Canvas/frontend runtime.
- 56 memiliki consumer Settings logic.
- 67 memiliki consumer editor/backend.
- 0 consumerless controls.
- 4 undeclared tokens berasal dari compatibility/default assignments lama dan tidak merupakan controlless Style option.

Critical options checked in the matrix: `cardHeightMode`, `imageShape`, `imageLabelPlacement`, `imageLabelGap`, `hoverBorderColor`, `hoverBorderWidth`, `selectedBorderColor`, `selectedBorderWidth`, and `selectedCheckIconSize`; semuanya memiliki consumer Canvas/frontend.

## Automated verification

- Product Lead Form Node: 10 passed.
- Full v2.4 Node: 388 passed.
- Full v2.4 PHP Feature+Unit: 154 passed, 10,310 assertions.
- Renderer custom state test: hover color, hover width, selected width, label placement, label gap, circle thumbnail, and checklist sizing passed.
- Control audit: 0 consumerless.
- Vite build, PHP/JS syntax, SFC compile, and `git diff --check`: passed.

## Browser limitation

Click-through visual QA untuk setiap control belum dapat dijalankan karena sesi editor browser yang tersedia di automation tidak memiliki session login terautentikasi. Matrix ini membuktikan binding dan renderer secara statis/fake; persistence dan screenshot editor setelah setiap perubahan masih perlu diverifikasi dari sesi editor user.
