<template>

  <!-- Sidebar posicionado debajo del header -->

  <aside class="sidebar" :class="{ 'open': isOpen, 'collapsed': isCollapsed }">

    <!-- Contenido del sidebar -->

    <div class="sidebar-content">

      <div class="sidebar-header">

        <button class="hamburger-btn" @click="toggleCollapse" aria-label="Alternar menú" :aria-expanded="!isCollapsed">

          <Bars3Icon v-if="isCollapsed" class="h-5 w-5 text-gray-700 dark:text-white"/>
          <ChevronLeftIcon v-else class="h-5 w-5 text-gray-700 dark:text-white"/>
        </button>

        <h2 v-if="!isCollapsed" class="sidebar-title">Panel Admin</h2>

      </div>

      

      <nav class="sidebar-nav">

        <ul class="nav-list">

          <li class="nav-item">

            <router-link to="/admin" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">

              <ChartBarIcon class="nav-icon h-5 w-5"/>
              <span class="nav-text" v-if="!isCollapsed">Dashboard</span>

              <span class="nav-tooltip" v-if="isCollapsed">Dashboard</span>

            </router-link>

          </li>

          

          <li class="nav-item has-submenu">

            <div 

              class="nav-link" 

              :class="{ 'collapsed': isCollapsed, 'active': isFletesMenuOpen || isFletesRouteActive }"

              @click="toggleFletesSubmenu"

            >

              <TruckIcon class="nav-icon h-5 w-5"/>
              <span class="nav-text" v-if="!isCollapsed">Fletes y Encomiendas</span>

              <span class="nav-tooltip" v-if="isCollapsed">Fletes</span>

              <ChevronDownIcon v-if="!isCollapsed" class="submenu-arrow h-4 w-4" :class="{ 'rotated': isFletesMenuOpen }"/>
            </div>

            

            <!-- Submenú -->

            <ul 

              v-if="!isCollapsed" 

              class="submenu"

              :class="{ 'open': isFletesMenuOpen }"

            >

              <li class="submenu-item">

                <router-link 

                  to="/admin/fletes/por-enviar" 

                  class="submenu-link"

                  @click="closeSidebar"

                >

                  <svg class="submenu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>

                  </svg>

                  <span>Por Enviar (Hoy)</span>

                </router-link>

              </li>

              <li class="submenu-item">

                <router-link 

                  to="/admin/fletes/todos" 

                  class="submenu-link"

                  @click="closeSidebar"

                >

                  <svg class="submenu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>

                  </svg>

                  <span>Todos los Fletes</span>

            </router-link>

              </li>

            </ul>

          </li>

          

          <li class="nav-item">

            <router-link to="/admin/usuarios" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">

              <UsersIcon class="nav-icon h-5 w-5"/>
              <span class="nav-text" v-if="!isCollapsed">Usuarios</span>

              <span class="nav-tooltip" v-if="isCollapsed">Usuarios</span>

            </router-link>

          </li>

          

          <li class="nav-item">

            <router-link to="/admin/sucursales" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">

              <BuildingOfficeIcon class="nav-icon h-5 w-5"/>
              <span class="nav-text" v-if="!isCollapsed">Sucursales</span>

              <span class="nav-tooltip" v-if="isCollapsed">Sucursales</span>

            </router-link>

          </li>

          

          <li class="nav-item">

            <router-link to="/admin/actividades" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">

              <ClockIcon class="nav-icon h-5 w-5"/>
              <span class="nav-text" v-if="!isCollapsed">Actividades de Usuarios</span>

              <span class="nav-tooltip" v-if="isCollapsed">Actividades</span>

            </router-link>

          </li>

          

          <li class="nav-item">

            <router-link to="/admin/configuracion" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">

              <Cog6ToothIcon class="nav-icon h-5 w-5"/>
              <span class="nav-text" v-if="!isCollapsed">Configuración del Sistema</span>

              <span class="nav-tooltip" v-if="isCollapsed">Configuración</span>

            </router-link>

          </li>

        </ul>

      </nav>


      <!-- Toggle de tema al final del sidebar -->
      <div class="sidebar-footer">
        <button 
          class="theme-toggle-btn" 
          @click="toggleTheme" 
          :disabled="isSyncing"
          role="switch" 
          :aria-checked="isDark.toString()" 
          :aria-label="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
        >
          <!-- Icono de sol (modo claro) -->
          <svg
            v-if="!isDark"
            class="theme-icon transition-all duration-300"
            :class="{ 'collapsed': isCollapsed }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
            />
          </svg>
          <!-- Icono de luna (modo oscuro) -->
          <svg
            v-else
            class="theme-icon transition-all duration-300"
            :class="{ 'collapsed': isCollapsed }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
            />
          </svg>
          <span class="toggle-text" v-if="!isCollapsed">{{ isDark ? 'Oscuro' : 'Claro' }}</span>
        </button>
      </div>
    </div>

  </aside>

</template>



<script>

import { 
  Bars3Icon,
  ChevronLeftIcon,
  ChevronDownIcon,
  ChartBarIcon,
  TruckIcon,
  UsersIcon,
  BuildingOfficeIcon,
  PresentationChartBarIcon,
  ClipboardIcon,
  ClockIcon,
  Cog6ToothIcon
} from '@heroicons/vue/24/outline'
import { useTheme } from '../composables/useTheme'

export default {

  name: 'SidebarAdmin',

  setup() {
    const { isDark, toggleTheme, isSyncing } = useTheme()
    return {
      isDark,
      toggleTheme,
      isSyncing
    }
  },

  data() {

    return {

      isOpen: false,

      isCollapsed: false,

      isFletesMenuOpen: false
    }

  },

  computed: {

    isFletesRouteActive() {

      return this.$route.path.startsWith('/admin/fletes')

    }

  },

  watch: {

    '$route'() {

      // Cerrar submenú al cambiar de ruta en móviles

      if (window.innerWidth < 1024) {

        this.isFletesMenuOpen = false

      }

    }

  },

  components: {
    Bars3Icon,
    ChevronLeftIcon,
    ChevronDownIcon,
    ChartBarIcon,
    TruckIcon,
    UsersIcon,
    BuildingOfficeIcon,
    PresentationChartBarIcon,
    ClipboardIcon,
    ClockIcon,
    Cog6ToothIcon
  },
  methods: {

    toggleSidebar() {

      this.isOpen = !this.isOpen;

      this.emitToggle();

    },

    closeSidebar() {

      this.isOpen = false;

      this.emitToggle();

    },

    toggleCollapse() {

      this.isCollapsed = !this.isCollapsed;

      this.emitToggle();

    },

    emitToggle() {

      this.$emit('sidebar-toggle', {

        isOpen: this.isOpen,

        isCollapsed: this.isCollapsed

      });

    },

    toggleFletesSubmenu() {

      if (!this.isCollapsed) {

        this.isFletesMenuOpen = !this.isFletesMenuOpen

      }

    }

  },

  mounted() {

    // En móviles, el sidebar debe estar oculto por defecto

    if (window.innerWidth < 1024) {

      this.isCollapsed = true;

    }

    

    // Abrir submenú si la ruta activa corresponde

    if (this.isFletesRouteActive) {

      this.isFletesMenuOpen = true;

    }
    

    // Emitir estado inicial

    this.emitToggle();

    

    // Cerrar sidebar con tecla Escape

    document.addEventListener('keydown', (e) => {

      if (e.key === 'Escape') {

        this.closeSidebar();

      }

    });

    

    // Manejar cambios de tamaño de ventana

    window.addEventListener('resize', () => {

      if (window.innerWidth < 1024) {

        this.isCollapsed = true;

        this.emitToggle();

      }

    });

  }

}

</script>



<style scoped>

/* Sidebar posicionado debajo del header - alineado con el inicio del admin-layout */

.sidebar {

  position: fixed;

  top: 4rem; /* Justo debajo del header, alineado con el inicio del admin-layout */

  left: 0;

  width: 250px;

  height: calc(100vh - 4rem); /* Altura completa menos el header */

  background: var(--color-surface);
  border-right: 1px solid var(--color-border);
  z-index: 999;

  transition: all 0.3s ease;

  overflow-y: auto;

  overflow-x: hidden; /* Evita scroll horizontal provocado por el botón flotante */

}



/* Estado colapsado */

.sidebar.collapsed {

  width: 60px;

}



/* Botón para colapsar/expandir */

.collapse-btn {

  position: absolute;

  top: 1rem;

  right: 0; /* Evita desplazar el layout fuera del viewport */

  transform: translateX(50%); /* Visualmente sobresale sin generar scroll */

  width: 30px;

  height: 30px;

  background: white;

  border: 1px solid #e5e7eb;

  border-radius: 50%;

  cursor: pointer;

  display: flex;

  align-items: center;

  justify-content: center;

  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

  transition: all 0.2s ease;

  z-index: 1100; /* Por encima del sidebar y contenido principal */

}



.collapse-btn:hover {

  background: #f9fafb;

  transform: scale(1.1);

}



.icon {

  font-size: 0.875rem;

  color: #6b7280;

}



/* Contenido del sidebar */

.sidebar-content {

  height: 100%;

  display: flex;

  flex-direction: column;

}



/* Sidebar header */

.sidebar-header {

  padding: 1rem;

  border-bottom: 1px solid var(--color-border);
  background: linear-gradient(135deg, var(--color-surface), var(--color-bg-soft));
  display: flex;

  align-items: center;

  justify-content: space-between; /* Espacio entre hamburguesa y título */

  position: relative; /* Para centrar el título absolutamente */

}



.sidebar-title {

  margin: 0;

  font-size: 1.25rem;

  font-weight: 600;

  color: var(--color-text);
  position: absolute;

  left: 50%;

  transform: translateX(-50%); /* Centrado perfecto horizontal */

  white-space: nowrap;

}

.dark .sidebar-title {
  color: #ffffff;
}



/* Botón hamburguesa dentro del header */

.hamburger-btn {

  width: 36px;

  height: 36px;

  display: inline-flex;

  align-items: center;

  justify-content: center;

  border: 1px solid var(--color-border);
  border-radius: 0.5rem;

  background: var(--color-surface);
  cursor: pointer;

  transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}



.hamburger-btn:hover {

  background: var(--color-hover);
}

/* Quitar borde negro en focus y aplicar realce muy sutil */
.hamburger-btn:focus,
.hamburger-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
  border-color: var(--color-border); /* mantener borde suave, sin negro */
}

/* Efecto sutil al hacer clic */
.hamburger-btn:active {
  transform: scale(0.98);
  background: #f3f4f6;
}

.dark .hamburger-btn:active {
  background: #1f2937;
}



/* SVG hamburger lines animation */

/* Contenedor para hacer transición entre iconos */

.icon-stack {

  position: relative;

  width: 22px;

  height: 22px;

}



.icon-svg {

  position: absolute;

  inset: 0;

  opacity: 0;

  transform: scale(0.9);

  transition: opacity 160ms ease, transform 160ms ease;

}



.icon-svg.visible {

  opacity: 1;

  transform: scale(1);

}



.hamburger-btn.active .hamburger-svg .line1 {

  transform: translateY(6px) rotate(45deg);

}



.hamburger-btn.active .hamburger-svg .line2 {

  opacity: 0;

}



.hamburger-btn.active .hamburger-svg .line3 {

  transform: translateY(-6px) rotate(-45deg);

}



.collapsed-header {

  display: flex;

  align-items: center;

  justify-content: center;

}



.collapsed-logo {

  font-size: 1.5rem;

  font-weight: 800;

  color: #1d4ed8;

  background: white;

  width: 2.5rem;

  height: 2.5rem;

  border-radius: 50%;

  display: flex;

  align-items: center;

  justify-content: center;

  box-shadow: 0 2px 8px rgba(29, 78, 216, 0.3);

}



/* Navigation */

.sidebar-nav {

  flex: 1;

  padding: 1rem 0;

}



.nav-list {

  list-style: none;

  margin: 0;

  padding: 0;

}



.nav-item {

  margin: 0;

}



.nav-link {
  display: flex;
  align-items: center;
  padding: 0.875rem 1.5rem;
  color: #374151; /* Gris en modo claro */
  text-decoration: none;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
  position: relative;
}

/* Modo oscuro: textos blancos */
.dark .nav-link {
  color: #ffffff;
}

.dark .nav-text {
  color: #ffffff;
}

.dark .nav-icon {
  color: #ffffff;
}

/* Hover en modo oscuro: iconos negros */
.dark .nav-link:hover .nav-icon {
  color: #000000;
}



.nav-link.collapsed {

  padding: 0.875rem 0.75rem;

  justify-content: center;

}



.nav-link:hover {

  background: #f9fafb;

  color: #1f2937;

}

.dark .nav-link:hover {
  background: rgba(255, 255, 255, 0.08); /* Efecto sutil de hover en modo oscuro */
  color: #ffffff;
  transform: translateX(2px); /* Efecto sutil de desplazamiento */
}

.dark .nav-link:hover .nav-text {
  color: #ffffff;
}



.nav-link.router-link-active {

  background: rgba(59, 130, 246, 0.1);

  color: #3b82f6;

  border-left-color: #3b82f6;

  font-weight: 500;

}

.dark .nav-link.router-link-active {
  background: rgba(59, 130, 246, 0.2);
  color: #ffffff;
}

.dark .nav-link.router-link-active .nav-icon {
  color: #ffffff;
}

.dark .nav-link.router-link-active .nav-text {
  color: #ffffff;
}

.dark .nav-link.router-link-active:hover {
  background: rgba(59, 130, 246, 0.25); /* Efecto sutil de hover en modo oscuro */
  transform: translateX(2px);
}

.dark .nav-link.router-link-active:hover .nav-icon {
  color: #000000;
}

.dark .nav-link.router-link-active:hover .nav-text {
  color: #ffffff;
}



.nav-icon {

  margin-right: 0.75rem;

  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;

  color: #374151; /* Gris en modo claro */
  transition: color 0.2s ease;
}

/* Modo oscuro: iconos blancos */
.dark .nav-icon {
  color: #ffffff;
}



.nav-link.collapsed .nav-icon {

  margin-right: 0;

}



.nav-text {

  font-size: 0.9rem;

  font-weight: 500;
  color: #374151; /* Gris en modo claro */
}

/* Modo oscuro: textos blancos */
.dark .nav-text {
  color: #ffffff;
}



.nav-tooltip {

  position: absolute;

  left: 100%;

  top: 50%;

  transform: translateY(-50%);

  background: #1f2937;

  color: white;

  padding: 0.5rem 0.75rem;

  border-radius: 0.375rem;

  font-size: 0.8rem;

  white-space: nowrap;

  opacity: 0;

  pointer-events: none;

  transition: opacity 0.2s ease;

  z-index: 1000;

  margin-left: 0.5rem;

}



.nav-tooltip::before {

  content: '';

  position: absolute;

  right: 100%;

  top: 50%;

  transform: translateY(-50%);

  border: 5px solid transparent;

  border-right-color: #1f2937;

}



.nav-link.collapsed:hover .nav-tooltip {

  opacity: 1;

}



/* Responsive adjustments */

@media (max-width: 1023px) {

  .sidebar {

    top: 3.5rem; /* Justo debajo del header en móviles */

    height: calc(100vh - 3.5rem);

    transform: translateX(-100%);

    width: 280px;

  }

  

  .sidebar.open {

    transform: translateX(0);

  }

  

  .sidebar.collapsed {

    width: 60px;

  }

  

  .collapse-btn {

    display: none; /* Ocultar botón de colapsar en móviles */

  }

}



@media (max-width: 480px) {

  .sidebar {

    top: 3rem; /* Justo debajo del header en móviles pequeños */

    height: calc(100vh - 3rem);

    width: 260px;

  }

  

  .sidebar-header {

    padding: 1rem 0.75rem;

  }

  

  .sidebar-header h2 {

    font-size: 1.125rem;

  }

  

  .nav-link {

    padding: 0.75rem 1rem;

  }

  

  .nav-text {

    font-size: 0.875rem;

  }

}



@media (min-width: 481px) and (max-width: 768px) {

  .sidebar {

    top: 4rem; /* Justo debajo del header */

    height: calc(100vh - 4rem);

    width: 300px;

  }

}



@media (min-width: 769px) and (max-width: 1023px) {

  .sidebar {

    top: 5rem; /* Justo debajo del header en tablets grandes */

    height: calc(100vh - 5rem);

    width: 320px;

  }

}



@media (min-width: 1024px) {

  .sidebar {

    top: 4rem; /* Justo debajo del header */

    height: calc(100vh - 4rem);

    width: 250px;

    transform: translateX(0);

  }

  

  .sidebar.collapsed {

    width: 60px;

  }

}



/* Animaciones */

.nav-link {

  animation: slideInLeft 0.3s ease-out;

}



@keyframes slideInLeft {

  from {

    opacity: 0;

    transform: translateX(-20px);

  }

  to {

    opacity: 1;

    transform: translateX(0);

  }

}



/* Scrollbar personalizado */

.sidebar::-webkit-scrollbar {

  width: 6px;

}



.sidebar::-webkit-scrollbar-track {

  background: #f1f1f1;

}



.sidebar::-webkit-scrollbar-thumb {

  background: #c1c1c1;

  border-radius: 3px;

}



.sidebar::-webkit-scrollbar-thumb:hover {

  background: #a8a8a8;

}



/* Submenú styles */

.has-submenu {

  position: relative;

}



.submenu-arrow {

  margin-left: auto;

  font-size: 0.8rem;
  line-height: 1;
  color: #9ca3af; /* gris suave por defecto en modo claro */
  transition: color 160ms ease, transform 200ms ease, opacity 160ms ease;
}

/* Modo oscuro: flecha blanca */
.dark .submenu-arrow {
  color: #ffffff;
}

.dark .nav-link:hover .submenu-arrow {
  color: #000000;
}

/* Quitar borde/outline negro en focus y dar efectos sutiles en hover/click */
.nav-link:focus, .submenu-arrow:focus {
  outline: none;
  box-shadow: none;
}

.has-submenu .nav-link:hover .submenu-arrow {
  color: #374151;
  transform: translateY(-1px);
}

.dark .has-submenu .nav-link:hover .submenu-arrow {
  color: #000000;
}

.has-submenu .nav-link:active .submenu-arrow {
  transform: translateY(0);
  opacity: 0.9;
}



.submenu-arrow.rotated {

  transform: rotate(180deg);

}


/* Coherencia con la UI: hover más oscuro, activo azul */
.has-submenu .nav-link:hover .submenu-arrow {
  color: #374151; /* gris-700 */
  transform: translateY(-1px);
}

.has-submenu .nav-link.active .submenu-arrow,
.has-submenu .nav-link.router-link-active .submenu-arrow,
.submenu.open ~ .submenu-arrow {
  color: #3b82f6; /* azul consistente con la interfaz */
}

.dark .has-submenu .nav-link.active .submenu-arrow,
.dark .has-submenu .nav-link.router-link-active .submenu-arrow {
  color: #ffffff;
}

.dark .has-submenu .nav-link.active:hover .submenu-arrow,
.dark .has-submenu .nav-link.router-link-active:hover .submenu-arrow {
  color: #000000;
}

.dark .has-submenu .nav-link:hover {
  background: rgba(255, 255, 255, 0.08); /* Efecto sutil de hover */
  transform: translateX(2px);
}


.submenu {

  max-height: 0;

  overflow: hidden;

  transition: max-height 0.3s ease;

  background: #f9fafb;

}

.dark .submenu {
  background: var(--color-surface);
}



.submenu.open {

  max-height: 300px;

}



.submenu-item {

  margin: 0;

}



.submenu-link {

  display: flex;

  align-items: center;

  padding: 0.75rem 1.5rem 0.75rem 3.5rem;

  color: #6b7280; /* Gris en modo claro */

  text-decoration: none;

  font-size: 0.875rem;

  transition: all 0.2s ease;

  border-left: 3px solid transparent;

  gap: 0.625rem;

}

.dark .submenu-link {
  color: #ffffff;
}

.dark .submenu-link .submenu-icon {
  color: #ffffff;
}

.dark .submenu-link:hover {
  background: rgba(255, 255, 255, 0.08); /* Efecto sutil de hover */
  color: #ffffff;
  transform: translateX(2px);
}

.dark .submenu-link:hover .submenu-icon {
  color: #000000;
}



.submenu-icon {

  width: 1rem;

  height: 1rem;

  flex-shrink: 0;
  transition: color 0.2s ease;
  color: #6b7280; /* Gris en modo claro */
}

/* Modo oscuro: iconos blancos */
.dark .submenu-icon {
  color: #ffffff;
}



.submenu-link:hover {

  background: #f3f4f6;

  color: #1f2937;

}

.dark .submenu-link:hover {
  background: rgba(255, 255, 255, 0.08); /* Efecto sutil de hover */
  color: #ffffff;
  transform: translateX(2px);
}



.submenu-link.router-link-active {

  background: rgba(59, 130, 246, 0.08);

  color: #3b82f6;

  border-left-color: #3b82f6;

  font-weight: 500;

}

.dark .submenu-link.router-link-active {
  background: rgba(59, 130, 246, 0.2);
  color: #ffffff;
}

.dark .submenu-link.router-link-active .submenu-icon {
  color: #ffffff;
}

.dark .submenu-link.router-link-active:hover {
  background: rgba(59, 130, 246, 0.25); /* Efecto sutil de hover */
  transform: translateX(2px);
}

.dark .submenu-link.router-link-active:hover .submenu-icon {
  color: #000000;
}


/* Footer y toggle de tema */
.sidebar-footer {
  margin-top: auto;
  padding: 1rem;
  border-top: 1px solid var(--color-border);
}

.theme-toggle-btn {
  width: 100%;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  justify-content: center;
  padding: 0.5rem 0.75rem;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 0.75rem;
  color: var(--color-text);
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.15s ease;
}

.theme-toggle-btn:hover {
  background: var(--color-hover);
}

.theme-toggle-btn:active {
  transform: scale(0.98);
}

.theme-toggle-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.theme-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.theme-icon.collapsed {
  width: 1rem;
  height: 1rem;
}

.toggle-text {
  font-size: 0.875rem;
  font-weight: 500;
  white-space: nowrap;
}

.sidebar.collapsed .sidebar-footer {
  padding: 0.5rem;
}

.sidebar.collapsed .theme-toggle-btn {
  gap: 0.25rem;
  padding: 0.375rem;
}

.sidebar.collapsed .toggle-text {
  display: none;
}


/* Ajustes responsivos para submenú */

@media (max-width: 1023px) {

  .submenu-link {

    padding-left: 3rem;

  }

}



@media (max-width: 480px) {

  .submenu-link {

    padding: 0.625rem 1rem 0.625rem 2.5rem;

    font-size: 0.8125rem;

  }

  

  .submenu-icon {

    width: 0.875rem;

    height: 0.875rem;

  }

}

.nav-link.router-link-active {

background: rgba(59, 130, 246, 0.1);

color: #3b82f6;

border-left-color: #3b82f6;

font-weight: 500;

}



.nav-icon {

margin-right: 0.75rem;

width: 1.25rem;
height: 1.25rem;
flex-shrink: 0;

color: var(--color-text);
}



.nav-link.collapsed .nav-icon {

margin-right: 0;

}



.nav-text {

font-size: 0.9rem;

font-weight: 500;

}



.nav-tooltip {

position: absolute;

left: 100%;

top: 50%;

transform: translateY(-50%);

background: #1f2937;

color: white;

padding: 0.5rem 0.75rem;

border-radius: 0.375rem;

font-size: 0.8rem;

white-space: nowrap;

opacity: 0;

pointer-events: none;

transition: opacity 0.2s ease;

z-index: 1000;

margin-left: 0.5rem;

}



.nav-tooltip::before {

content: '';

position: absolute;

right: 100%;

top: 50%;

transform: translateY(-50%);

border: 5px solid transparent;

border-right-color: #1f2937;

}



.nav-link.collapsed:hover .nav-tooltip {

opacity: 1;

}



/* Responsive adjustments */

@media (max-width: 1023px) {

.sidebar {

  top: 3.5rem; /* Justo debajo del header en móviles */

  height: calc(100vh - 3.5rem);

  transform: translateX(-100%);

  width: 280px;

}



.sidebar.open {

  transform: translateX(0);

}



.sidebar.collapsed {

  width: 60px;

}



.collapse-btn {

  display: none; /* Ocultar botón de colapsar en móviles */

}

}



@media (max-width: 480px) {

.sidebar {

  top: 3rem; /* Justo debajo del header en móviles pequeños */

  height: calc(100vh - 3rem);

  width: 260px;

}



.sidebar-header {

  padding: 1rem 0.75rem;

}



.sidebar-header h2 {

  font-size: 1.125rem;

}



.nav-link {

  padding: 0.75rem 1rem;

}



.nav-text {

  font-size: 0.875rem;

}

}



@media (min-width: 481px) and (max-width: 768px) {

.sidebar {

  top: 4rem; /* Justo debajo del header */

  height: calc(100vh - 4rem);

  width: 300px;

}

}



@media (min-width: 769px) and (max-width: 1023px) {

.sidebar {

  top: 5rem; /* Justo debajo del header en tablets grandes */

  height: calc(100vh - 5rem);

  width: 320px;

}

}



@media (min-width: 1024px) {

.sidebar {

  top: 4rem; /* Justo debajo del header */

  height: calc(100vh - 4rem);

  width: 250px;

  transform: translateX(0);

}



.sidebar.collapsed {

  width: 60px;

}

}



/* Animaciones */

.nav-link {

animation: slideInLeft 0.3s ease-out;

}



@keyframes slideInLeft {

from {

  opacity: 0;

  transform: translateX(-20px);

}

to {

  opacity: 1;

  transform: translateX(0);

}

}



/* Scrollbar personalizado */

.sidebar::-webkit-scrollbar {

width: 6px;

}



.sidebar::-webkit-scrollbar-track {

background: #f1f1f1;

}



.sidebar::-webkit-scrollbar-thumb {

background: #c1c1c1;

border-radius: 3px;

}



.sidebar::-webkit-scrollbar-thumb:hover {

background: #a8a8a8;

}



/* Submenú styles */

.has-submenu {

position: relative;

}



.submenu-arrow {

margin-left: auto;

font-size: 0.8rem;
line-height: 1;
color: #9ca3af; /* gris suave por defecto */
transition: color 160ms ease, transform 200ms ease, opacity 160ms ease;
}

/* Quitar borde/outline negro en focus y dar efectos sutiles en hover/click */
.nav-link:focus, .submenu-arrow:focus {
outline: none;
box-shadow: none;
}

.has-submenu .nav-link:hover .submenu-arrow {
color: #374151;
transform: translateY(-1px);
}

.has-submenu .nav-link:active .submenu-arrow {
transform: translateY(0);
opacity: 0.9;
}



.submenu-arrow.rotated {

transform: rotate(180deg);

}


/* Coherencia con la UI: hover más oscuro, activo azul */
.has-submenu .nav-link:hover .submenu-arrow {
color: #374151; /* gris-700 */
transform: translateY(-1px);
}

.has-submenu .nav-link.active .submenu-arrow,
.has-submenu .nav-link.router-link-active .submenu-arrow,
.submenu.open ~ .submenu-arrow {
color: #3b82f6; /* azul consistente con la interfaz */
}


.submenu {

max-height: 0;

overflow: hidden;

transition: max-height 0.3s ease;

background: #f9fafb;

}



.submenu.open {

max-height: 300px;

}



.submenu-item {

margin: 0;

}



.submenu-link {

display: flex;

align-items: center;

padding: 0.75rem 1.5rem 0.75rem 3.5rem;

color: #6b7280;

text-decoration: none;

font-size: 0.875rem;

transition: all 0.2s ease;

border-left: 3px solid transparent;

gap: 0.625rem;

}



.submenu-icon {

width: 1rem;

height: 1rem;

flex-shrink: 0;

}



.submenu-link:hover {

background: #f3f4f6;

color: #1f2937;

}



.submenu-link.router-link-active {

background: rgba(59, 130, 246, 0.08);

color: #3b82f6;

border-left-color: #3b82f6;

font-weight: 500;

}


/* Footer y toggle de tema */
.sidebar-footer {
margin-top: auto;
padding: 1rem;
border-top: 1px solid var(--color-border);
}

.theme-toggle {
width: 100%;
display: inline-flex;
align-items: center;
gap: 0.75rem;
justify-content: center;
padding: 0.5rem 0.75rem;
background: var(--color-surface);
border: 1px solid var(--color-border);
border-radius: 0.75rem;
color: var(--color-text);
cursor: pointer;
transition: background-color 0.2s ease, transform 0.15s ease;
}

.theme-toggle:hover { background: var(--color-hover); }
.theme-toggle:active { transform: scale(0.98); }

.toggle-track {
position: relative;
width: 64px;
height: 30px;
border-radius: 999px;
background: var(--color-hover);
border: 1px solid var(--color-border);
display: flex;
align-items: center;
justify-content: space-between;
padding: 0 8px;
box-sizing: border-box;
}

.toggle-icon { color: var(--color-text-muted); transition: color 0.2s ease, opacity 0.2s ease; }
.toggle-icon.active { color: var(--color-primary); }

.toggle-knob {
position: absolute;
top: 3px;
left: 3px;
width: 24px;
height: 24px;
background: var(--color-surface);
border: 1px solid var(--color-border);
border-radius: 999px;
box-shadow: var(--shadow-sm);
transition: transform 0.2s ease;
}
.toggle-knob.dark { transform: translateX(34px); }

/* Variante vertical para sidebar colapsado */
.toggle-track.vertical {
width: 30px;
height: 64px;
padding: 8px 0;
flex-direction: column;
}

.toggle-knob.vertical {
top: 3px;
left: 3px;
}

.toggle-knob.vertical.dark {
transform: translateY(34px);
}

.sidebar.collapsed .sidebar-footer { padding: 0.5rem; }
.sidebar.collapsed .theme-toggle { gap: 0.25rem; padding: 0.375rem; }
.sidebar.collapsed .toggle-text { display: none; }


/* Ajustes responsivos para submenú */

@media (max-width: 1023px) {

.submenu-link {

  padding-left: 3rem;

}

}



@media (max-width: 480px) {

.submenu-link {

  padding: 0.625rem 1rem 0.625rem 2.5rem;

  font-size: 0.8125rem;

}



.submenu-icon {

  width: 0.875rem;

  height: 0.875rem;

}

}



</style>






