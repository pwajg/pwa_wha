import { createRouter, createWebHistory } from 'vue-router';
import InicioPage from '../pages/InicioPage.vue';
import LoginPage from '../pages/LoginPage.vue';
import HomeWrapper from '../components/HomeWrapper.vue';
import CrearEncomiendaPage from '../pages/CrearEncomiendaPage.vue';
import HomeAdminPage from '../pages/Administrador/HomeAdminPage.vue';
import FletesPage from '../pages/Administrador/FletesPage.vue';
import UsuariosPage from '../pages/Administrador/UsuariosPage.vue';
import SucursalesPage from '../pages/Administrador/SucursalesPage.vue';
import ReportesPage from '../pages/Administrador/ReportesPage.vue';
import ConfiguracionPage from '../pages/Administrador/ConfiguracionPage.vue';

const routes = [
    // Rutas públicas
    { 
        path: '/', 
        name: 'Inicio', 
        component: InicioPage 
    },
    { 
        path: '/login', 
        name: 'Login', 
        component: LoginPage 
    },
    
    // Rutas protegidas para usuarios normales
    { 
        path: '/home', 
        name: 'Home', 
        component: HomeWrapper, 
        meta: { requiresAuth: true } 
    },
    { 
        path: '/crear-encomienda', 
        name: 'CrearEncomienda', 
        component: CrearEncomiendaPage, 
        meta: { requiresAuth: true } 
    },
    
    // Rutas protegidas para administradores
    { 
        path: '/admin', 
        name: 'HomeAdmin', 
        component: HomeAdminPage, 
        meta: { requiresAuth: true, requiresAdmin: true } 
    },
    { 
        path: '/admin/fletes', 
        name: 'Fletes', 
        component: FletesPage, 
        meta: { requiresAuth: true, requiresAdmin: true } 
    },
    { 
        path: '/admin/usuarios', 
        name: 'Usuarios', 
        component: UsuariosPage, 
        meta: { requiresAuth: true, requiresAdmin: true } 
    },
    { 
        path: '/admin/sucursales', 
        name: 'Sucursales', 
        component: SucursalesPage, 
        meta: { requiresAuth: true, requiresAdmin: true } 
    },
    { 
        path: '/admin/reportes', 
        name: 'Reportes', 
        component: ReportesPage, 
        meta: { requiresAuth: true, requiresAdmin: true } 
    },
    { 
        path: '/admin/configuracion', 
        name: 'Configuracion', 
        component: ConfiguracionPage, 
        meta: { requiresAuth: true, requiresAdmin: true } 
    },
    
    // Ruta catch-all para páginas no encontradas
    { 
        path: '/:pathMatch(.*)*', 
        name: 'NotFound', 
        redirect: '/' 
    }
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
            return;
        }
        
        // Verificar si requiere permisos de administrador
        if (to.meta.requiresAdmin) {
            try {
                const userData = JSON.parse(usuario);
                if (userData.rol !== 'Administrador') {
                    // No es administrador, redirigir al home
                    next('/home');
                    return;
                }
            } catch (error) {
                console.error('Error parsing user data:', error);
                next('/login');
                return;
            }
        }
        
        // Usuario autenticado y con permisos, permitir acceso
        next();
    } else if (to.path === '/login' && token && usuario) {
        // Si ya está autenticado y va al login, redirigir según rol
        try {
            const userData = JSON.parse(usuario);
            if (userData.rol === 'Administrador') {
                next('/admin');
            } else {
                next('/home');
            }
        } catch (error) {
            console.error('Error parsing user data:', error);
            next('/home');
        }
    } else {
        next();
    }
});

export default router;