import '../css/app.css'

// Admin dashboard vendor CSS
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'

import { createInertiaApp } from '@inertiajs/vue3'
import VueApexCharts from 'vue3-apexcharts'
import { initRTL } from './composables/useRTL'

createInertiaApp({
  withApp(app, { ssr }) {
    app.use(VueApexCharts)

    // Browser-only: localStorage/document access, skip during SSR
    if (!ssr) {
      initRTL()
    }
  },
})
