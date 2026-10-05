<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="isolated-qa">
<title>Workflow — isolated UI QA</title>
<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ url('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}">
<style>body{background:#f4f6f8;color:#17202a;font-family:system-ui,sans-serif}.qa-shell{padding:20px}.ph-content{background:white}.ph-btn-theme{background:#1fa675;color:white}.ph-btn-theme-outline{border:1px solid #1fa675;color:#14744f}.qa-notice{font-size:12px;color:#5c6873;margin-bottom:10px}</style>
@stack('css')</head><body><main class="qa-shell"><div class="qa-notice">QA terisolasi — template dan Vue produksi, layout minimal, data fixture di project-artifacts.</div>@yield('content')</main>
<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script><script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script><script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.21.global.prod.js') }}"></script>@stack('js')</body></html>
