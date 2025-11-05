# 🎨 Sistema de Colores Whafren

Este documento explica cómo usar el sistema de colores de Whafren en toda la aplicación.

## 📁 Archivos del Sistema

- **`frontend/src/assets/css/whafren-colors.css`** - Variables CSS y clases de utilidad
- **`frontend/src/main.js`** - Importación del archivo de colores

## 🎯 Variables CSS Disponibles

### 1️⃣ Primario – Dorado Premium
```css
--whafren-gold-dark: #B6862C      /* Botones principales, logotipo */
--whafren-gold-medium: #D4A43E   /* Hover en botones, íconos destacados */
--whafren-gold-light: #EACB75    /* Líneas divisorias o acentos suaves */
--whafren-gold-soft: #FFF2C7     /* Fondos sutiles en secciones premium */
```

### 2️⃣ Secundario – Negro Grafito
```css
--whafren-black-pure: #0B0B0B     /* Header, footer, texto principal */
--whafren-black-graphite: #1C1C1C /* Fondos principales */
--whafren-gray-charcoal: #2B2B2B  /* Tarjetas, sombras o contraste sutil */
```

### 3️⃣ Neutros – Equilibrio visual
```css
--whafren-gray-light: #E5E5E5     /* Fondos de secciones */
--whafren-gray-medium: #CFCFCF    /* Bordes y líneas divisorias */
--whafren-white-pure: #FFFFFF     /* Texto sobre fondos oscuros o dorados */
```

### 4️⃣ Acentos – Energía y tecnología
```css
--whafren-blue-teal: #008B8B      /* Enlaces o indicadores de estado */
--whafren-green-success: #2ECC71  /* Confirmaciones o etiquetas de envío completado */
--whafren-red-alert: #E74C3C      /* Errores o advertencias importantes */
```

## 🌈 Gradientes Predefinidos

```css
--whafren-gradient-gold: linear-gradient(135deg, var(--whafren-gold-dark) 0%, var(--whafren-gold-medium) 100%);
--whafren-gradient-dark: linear-gradient(135deg, var(--whafren-black-pure) 0%, var(--whafren-black-graphite) 100%);
--whafren-gradient-hero: linear-gradient(135deg, var(--whafren-gold-dark) 0%, var(--whafren-black-graphite) 100%);
```

## 📏 Sombras Predefinidas

```css
--whafren-shadow-sm: 0 2px 4px rgba(11, 11, 11, 0.1);
--whafren-shadow-md: 0 4px 8px rgba(11, 11, 11, 0.15);
--whafren-shadow-lg: 0 8px 16px rgba(11, 11, 11, 0.2);
--whafren-shadow-xl: 0 16px 32px rgba(11, 11, 11, 0.25);
```

## 🔄 Transiciones

```css
--whafren-transition-fast: 0.2s ease;
--whafren-transition-normal: 0.3s ease;
--whafren-transition-slow: 0.5s ease;
```

## 📐 Bordes Redondeados

```css
--whafren-radius-sm: 6px;
--whafren-radius-md: 12px;
--whafren-radius-lg: 16px;
--whafren-radius-xl: 20px;
```

## 💡 Cómo Usar

### En CSS/SCSS:
```css
.mi-componente {
  background: var(--whafren-gold-dark);
  color: var(--whafren-white-pure);
  border-radius: var(--whafren-radius-md);
  box-shadow: var(--whafren-shadow-md);
  transition: all var(--whafren-transition-normal);
}

.mi-componente:hover {
  background: var(--whafren-gold-medium);
  box-shadow: var(--whafren-shadow-lg);
}
```

### En Vue.js (style scoped):
```vue
<style scoped>
.mi-boton {
  background: var(--whafren-gradient-gold);
  color: var(--whafren-white-pure);
  border-radius: var(--whafren-radius-md);
  transition: all var(--whafren-transition-normal);
}

.mi-boton:hover {
  transform: translateY(-2px);
  box-shadow: var(--whafren-shadow-lg);
}
</style>
```

## 🎨 Clases de Utilidad

### Colores de Texto:
```html
<span class="text-gold-dark">Texto dorado oscuro</span>
<span class="text-black-pure">Texto negro puro</span>
<span class="text-red-alert">Texto de error</span>
```

### Colores de Fondo:
```html
<div class="bg-gold-dark">Fondo dorado oscuro</div>
<div class="bg-black-graphite">Fondo negro grafito</div>
<div class="bg-green-success">Fondo verde éxito</div>
```

### Gradientes:
```html
<div class="bg-gradient-gold">Gradiente dorado</div>
<div class="bg-gradient-dark">Gradiente oscuro</div>
<div class="bg-gradient-hero">Gradiente hero</div>
```

### Sombras:
```html
<div class="shadow-whafren-sm">Sombra pequeña</div>
<div class="shadow-whafren-md">Sombra mediana</div>
<div class="shadow-whafren-lg">Sombra grande</div>
<div class="shadow-whafren-xl">Sombra extra grande</div>
```

### Bordes Redondeados:
```html
<div class="rounded-whafren-sm">Bordes pequeños</div>
<div class="rounded-whafren-md">Bordes medianos</div>
<div class="rounded-whafren-lg">Bordes grandes</div>
<div class="rounded-whafren-xl">Bordes extra grandes</div>
```

### Transiciones:
```html
<div class="transition-whafren-fast">Transición rápida</div>
<div class="transition-whafren-normal">Transición normal</div>
<div class="transition-whafren-slow">Transición lenta</div>
```

## 🎯 Ejemplos de Uso Común

### Botón Principal:
```css
.btn-primary {
  background: var(--whafren-gradient-gold);
  color: var(--whafren-white-pure);
  border: none;
  border-radius: var(--whafren-radius-md);
  padding: 1rem 2rem;
  font-weight: 600;
  transition: all var(--whafren-transition-normal);
  box-shadow: var(--whafren-shadow-sm);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--whafren-shadow-lg);
}
```

### Tarjeta:
```css
.card {
  background: var(--whafren-white-pure);
  border-radius: var(--whafren-radius-lg);
  box-shadow: var(--whafren-shadow-md);
  border: 1px solid var(--whafren-gray-medium);
  padding: 1.5rem;
  transition: all var(--whafren-transition-normal);
}

.card:hover {
  box-shadow: var(--whafren-shadow-lg);
  transform: translateY(-2px);
}
```

### Input:
```css
.input {
  border: 2px solid var(--whafren-gray-medium);
  border-radius: var(--whafren-radius-md);
  padding: 0.75rem 1rem;
  background: var(--whafren-gray-light);
  transition: all var(--whafren-transition-normal);
}

.input:focus {
  outline: none;
  border-color: var(--whafren-gold-dark);
  background: var(--whafren-white-pure);
  box-shadow: 0 0 0 3px rgba(182, 134, 44, 0.1);
}
```

## 📱 Responsive Design

Las variables funcionan perfectamente con media queries:

```css
@media (max-width: 768px) {
  .mi-componente {
    padding: 1rem;
    border-radius: var(--whafren-radius-sm);
  }
}
```

## 🔧 Personalización

Para cambiar colores globalmente, simplemente modifica las variables en `whafren-colors.css`:

```css
:root {
  --whafren-gold-dark: #tu-color-personalizado;
}
```

¡Todos los componentes que usen esta variable se actualizarán automáticamente!
