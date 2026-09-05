import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const dashboard = readFileSync('resources/views/dashboard/dashboard.blade.php', 'utf8');
const script = readFileSync('public/assets/js/vue3/dashboard/vueV3-dashboard-2026.js', 'utf8');

test('dashboard exposes semantic mobile hooks without changing existing chart ids', () => {
  assert.match(dashboard, /class="[^"]*ph-dashboard-summary[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-stats[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-stat[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-stat-icon[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-stat-label[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-stat-value[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-stat-trend[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-projection-title[^"]*"/);
  assert.match(dashboard, /class="[^"]*ph-dashboard-projection-chart[^"]*"/);
  assert.match(dashboard, /id="echartSeriesSimpleBar_ProjectionActual"/);
  assert.equal((dashboard.match(/id="echartSeriesSimpleBar_ProjectionActual"/g) ?? []).length, 1);
});

test('dashboard chart keeps one Vue 3 CDN instance and separates mobile title and legend space', () => {
  assert.match(script, /const isMobileDashboardViewport\s*=\s*\(\)\s*=>/);
  assert.match(script, /const getProjectionChartOption\s*=\s*\(isMobile\)/);
  assert.match(script, /legend:[\s\S]*?top:\s*isMobile\s*\?\s*'34px'/);
  assert.match(script, /grid:[\s\S]*?top:\s*isMobile\s*\?\s*'22%'/);
  assert.match(script, /window\.addEventListener\('resize',/);
  assert.match(script, /projectionChartInstance\.resize\(\)/);
  assert.equal((script.match(/\.mount\('#ph-app-echarts'\)/g) ?? []).length, 1);
});

console.log('Arunika mobile dashboard static contract loaded.');
