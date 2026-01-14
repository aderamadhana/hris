import '../css/app.css';
import '../css/components/header.css';
import '../css/components/sidebar.css';
import '../css/components/dashboard.css';
import '../css/components/content.css'
import '../css/components/datatable_filter_search.css'
import 'leaflet/dist/leaflet.css'

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

import jQuery from 'jquery'
window.$ = window.jQuery = jQuery

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
    faEye,
    faEyeSlash,
    faHouse,
    faUsers,
    faFileLines,
    faBuilding,
    faCalendarDays,
    faClock,
    faFileInvoiceDollar,
    faFileContract,
    faTriangleExclamation,
    faUpload,
    faDownload,
    faPlus,
    faFilter,
    faPenToSquare,
    faTrash,
    faChevronLeft,
    faChevronRight,
    faChevronDown,
    faChevronUp,
    faLayerGroup,
    faFolderOpen,
    faUser,
    faKey,
    faRightFromBracket,
    faRightToBracket,
    faBars,
    faSync,
    faLocationCrosshairs,
    faRotate,
    faSpinner,
    faMapLocationDot,
    faCircleCheck,
    faCircleExclamation,
    faSatelliteDish,
    faCircleXmark,
    faCircleInfo,
    faLocationDot,
    faHourglassHalf,
    faBriefcase,
    faCamera,
    faCheck,
    faArrowRightToBracket,
    faArrowRightFromBracket,
    faMobileScreen,
    faCalendarXmark,
    faClockRotateLeft,
    faCalendarCheck,
    faCalendar,
    faArrowRight,
    faShieldHalved,
    faSave,
    faTimes,
    faUserClock,
    faUserXmark,faBuildingCircleXmark,
    faUserPlus,
    faUserMinus
} from '@fortawesome/free-solid-svg-icons'

import 'select2/dist/css/select2.min.css'
import 'select2'

const appName = 'HRIS System';

library.add(
    faEye,
    faEyeSlash,
    faHouse,
    faUsers,
    faFileLines,
    faBuilding,
    faCalendarDays,
    faClock,
    faFileInvoiceDollar,
    faFileContract,
    faTriangleExclamation,
    faUpload,
    faDownload,
    faPlus,
    faFilter,
    faPenToSquare,
    faTrash,
    faChevronLeft,
    faChevronRight,
    faChevronDown,
    faChevronUp,
    faLayerGroup,
    faFolderOpen,
    faUser,
    faKey,
    faRightFromBracket,
    faRightToBracket,
    faBars,
    faSync,
    faLocationCrosshairs,
    faRotate,
    faSpinner,
    faMapLocationDot,
    faCircleCheck,
    faCircleExclamation,
    faSatelliteDish,
    faCircleXmark,
    faCircleInfo,
    faLocationDot,
    faHourglassHalf,
    faBriefcase,
    faCamera,
    faCheck,
    faArrowRightToBracket,
    faArrowRightFromBracket,
    faMobileScreen,
    faCalendarXmark,
    faClockRotateLeft,
    faCalendarCheck,
    faCalendar,
    faArrowRight,
    faShieldHalved,
    faSave,
    faTimes,
    faUserClock,
    faUserXmark,
  faBuildingCircleXmark,
  faUserPlus,
  faUserMinus
)

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
    progress: {
        color: '#c0926f',
        showSpinner: true,
    },
});

// This will set light / dark mode on page load...
initializeTheme();
