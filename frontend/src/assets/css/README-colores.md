# 🎨 Sistema de Colores Wafren

Este documento explica cómo usar el sistema de colores de Wafren en toda la aplicación.

## 📁 Archivos del Sistema

- **`frontend/src/assets/css/wafren-colors.css`** - Variables CSS y clases de utilidad
- **`frontend/src/main.js`** - Importación del archivo de colores

## 🎯 Variables CSS Disponibles

### 1️⃣ Primario – Dorado Premium
```css
--wafren-gold-dark: #B6862C      /* Botones principales, logotipo */
--wafren-gold-medium: #D4A43E   /* Hover en botones, íconos destacados */
--wafren-gold-light: #EACB75    /* Líneas divisorias o acentos suaves */
--wafren-gold-soft: #FFF2C7     /* Fondos sutiles en secciones premium */
```

### 2️⃣ Secundario – Negro Grafito
```css
--wafren-black-pure: #0B0B0B     /* Header, footer, texto principal */
--wafren-black-graphite: #1C1C1C /* Fondos principales */
--wafren-gray-charcoal: #2B2B2B  /* Tarjetas, sombras o contraste sutil */
```

### 3️⃣ Neutros – Equilibrio visual
```css
--wafren-gray-light: #E5E5E5     /* Fondos de secciones */
--wafren-gray-medium: #CFCFCF    /* Bordes y líneas divisorias */
--wafren-white-pure: #FFFFFF     /* Texto sobre fondos oscuros o dorados */
```

### 4️⃣ Acentos – Energía y tecnología
```css
--wafren-blue-teal: #008B8B      /* Enlaces o indicadores de estado */
--wafren-green-success: #2ECC71  /* Confirmaciones o etiquetas de envío completado */
--wafren-red-alert: #E74C3C      /* Errores o advertencias importantes */
```

## 🌈 Gradientes Predefinidos

```css
--wafren-gradient-gold: linear-gradient(135deg, var(--wafren-gold-dark) 0%, var(--wafren-gold-medium) 100%);
--wafren-gradient-dark: linear-gradient(135deg, var(--wafren-black-pure) 0%, var(--wafren-black-graphite) 100%);
--wafren-gradient-hero: linear-gradient(135deg, var(--wafren-gold-dark) 0%, var(--wafren-black-graphite) 100%);
```

## 📏 Sombras Predefinidas

```css
--wafren-shadow-sm: 0 2px 4px rgba(11, 11, 11, 0.1);
--wafren-shadow-md: 0 4px 8px rgba(11, 11, 11, 0.15);
--wafren-shadow-lg: 0 8px 16px rgba(11, 11, 11, 0.2);
--wafren-shadow-xl: 0 16px 32px rgba(11, 11, 11, 0.25);
```

## 🔄 Transiciones

```css
--wafren-transition-fast: 0.2s ease;
--wafren-transition-normal: 0.3s ease;
--wafren-transition-slow: 0.5s ease;
```

## 📐 Bordes Redondeados

```css
--wafren-radius-sm: 6px;
--wafren-radius-md: 12px;
--wafren-radius-lg: 16px;
--wafren-radius-xl: 20px;
```

## 💡 Cómo Usar

### En CSS/SCSS:
```css
.mi-componente {
  background: var(--wafren-gold-dark);
  color: var(--wafren-white-pure);
  border-radius: var(--wafren-radius-md);
  box-shadow: var(--wafren-shadow-md);
  transition: all var(--wafren-transition-normal);
}

.mi-componente:hover {
  background: var(--wafren-gold-medium);
  box-shadow: var(--wafren-shadow-lg);
}
```

### En Vue.js (style scoped):
```vue
<style scoped>
.mi-boton {
  background: var(--wafren-gradient-gold);
  color: var(--wafren-white-pure);
  border-radius: var(--wafren-radius-md);
  transition: all var(--wafren-transition-normal);
}

.mi-boton:hover {
  transform: translateY(-2px);
  box-shadow: var(--wafren-shadow-lg);
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
<div class="shadow-wafren-sm">Sombra pequeña</div>
<div class="shadow-wafren-md">Sombra mediana</div>
<div class="shadow-wafren-lg">Sombra grande</div>
<div class="shadow-wafren-xl">Sombra extra grande</div>
```

### Bordes Redondeados:
```html
<div class="rounded-wafren-sm">Bordes pequeños</div>
<div class="rounded-wafren-md">Bordes medianos</div>
<div class="rounded-wafren-lg">Bordes grandes</div>
<div class="rounded-wafren-xl">Bordes extra grandes</div>
```

### Transiciones:
```html
<div class="transition-wafren-fast">Transición rápida</div>
<div class="transition-wafren-normal">Transición normal</div>
<div class="transition-wafren-slow">Transición lenta</div>
```

## 🎯 Ejemplos de Uso Común

### Botón Principal:
```css
.btn-primary {
  background: var(--wafren-gradient-gold);
  color: var(--wafren-white-pure);
  border: none;
  border-radius: var(--wafren-radius-md);
  padding: 1rem 2rem;
  font-weight: 600;
  transition: all var(--wafren-transition-normal);
  box-shadow: var(--wafren-shadow-sm);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--wafren-shadow-lg);
}
```

### Tarjeta:
```css
.card {
  background: var(--wafren-white-pure);
  border-radius: var(--wafren-radius-lg);
  box-shadow: var(--wafren-shadow-md);
  border: 1px solid var(--wafren-gray-medium);
  padding: 1.5rem;
  transition: all var(--wafren-transition-normal);
}

.card:hover {
  box-shadow: var(--wafren-shadow-lg);
  transform: translateY(-2px);
}
```

### Input:
```css
.input {
  border: 2px solid var(--wafren-gray-medium);
  border-radius: var(--wafren-radius-md);
  padding: 0.75rem 1rem;
  background: var(--wafren-gray-light);
  transition: all var(--wafren-transition-normal);
}

.input:focus {
  outline: none;
  border-color: var(--wafren-gold-dark);
  background: var(--wafren-white-pure);
  box-shadow: 0 0 0 3px rgba(182, 134, 44, 0.1);
}
```

## 📱 Responsive Design

Las variables funcionan perfectamente con media queries:

```css
@media (max-width: 768px) {
  .mi-componente {
    padding: 1rem;
    border-radius: var(--wafren-radius-sm);
  }
}
```

## 🔧 Personalización

Para cambiar colores globalmente, simplemente modifica las variables en `wafren-colors.css`:

```css
:root {
  --wafren-gold-dark: #tu-color-personalizado;
}
```

¡Todos los componentes que usen esta variable se actualizarán automáticamente!
