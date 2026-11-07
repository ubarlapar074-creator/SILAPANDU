import Alpine from 'alpinejs';
import registrationModal from './modules/registration-modal';
import serviceDashboard from './modules/service-dashboard';
import waliHandler from './modules/wali-handler';
import settingsHandler from './modules/settings-handler';
import resiList from './modules/resi-list';
import sidebar from './modules/sidebar';

// Register Alpine Components
Alpine.data('sidebar', sidebar);
Alpine.data('registrationModal', registrationModal);
Alpine.data('serviceDashboard', serviceDashboard);
Alpine.data('waliHandler', waliHandler);
Alpine.data('settingsHandler', settingsHandler);
Alpine.data('resiList', resiList);

window.Alpine = Alpine;
Alpine.start();
