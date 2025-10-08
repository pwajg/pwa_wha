import { createRouter, createWebHistory } from 'vue-router';
import InicioPage from '../pages/InicioPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import HomeWrapper from '../components/HomeWrapper.vue';
import CrearEncomiendaPage from '../pages/CrearEncomiendaPage.vue';

const routes = [
    { path: '/', name: 'Inicio', component: InicioPage },
    { path: '/login', name: 'Login', component: LoginPage },
    { path: '/home', name: 'Home', component: HomeWrapper, meta: { requiresAuth: true } },
    { path: '/crear-encomienda', name: 'CrearEncomienda', component: CrearEncomiendaPage, meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Guard de navegación para proteger rutas
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const usuario = localStorage.getItem('usuario');
    
    // Verificar si la ruta requiere autenticación
    if (to.meta.requiresAuth) {
        if (!token || !usuario) {
            // No hay token, redirigir al login
            next('/login');
        } else {
            // Usuario autenticado, permitir acceso
            next();
        }
    } else if (to.path === '/login' && token && usuario) {
        // Si ya está autenticado y va al login, redirigir al home
        next('/home');
    } else {
        next();
    }
});

export default router;